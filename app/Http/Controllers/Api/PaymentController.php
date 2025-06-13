<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\InspectionRequest;
use App\Models\BusinessPartner;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Get all payments (with filtering and permissions)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $query = Payment::with(['inspectionRequest.property', 'inspectionRequest.requester']);

            // Apply user-based filtering based on role
            if ($user->isIndividualClient()) {
                // Individual clients can only see their own payments
                $query->whereHas('inspectionRequest', function ($q) use ($user) {
                    $q->where('requester_user_id', $user->id);
                });
            } elseif ($user->isBusinessPartner()) {
                // Business partners can see payments from their organization
                $partnerIds = $user->businessPartners->pluck('id');
                $query->whereHas('inspectionRequest', function ($q) use ($partnerIds) {
                    $q->whereIn('business_partner_id', $partnerIds);
                });
            }
            // Admins and head technicians can see all payments (no filter)

            // Search functionality
            if ($request->has('search') && $request->search) {
                $query->search($request->search);
            }

            // Filter by status
            if ($request->has('status') && $request->status) {
                $query->byStatus($request->status);
            }

            // Filter by payment method
            if ($request->has('payment_method') && $request->payment_method) {
                $query->byMethod($request->payment_method);
            }

            // Filter by date range
            if ($request->has('start_date') && $request->start_date) {
                $query->whereDate('initiated_at', '>=', $request->start_date);
            }
            if ($request->has('end_date') && $request->end_date) {
                $query->whereDate('initiated_at', '<=', $request->end_date);
            }

            // Filter by amount range
            if ($request->has('min_amount') && $request->min_amount) {
                $query->where('amount', '>=', $request->min_amount);
            }
            if ($request->has('max_amount') && $request->max_amount) {
                $query->where('amount', '<=', $request->max_amount);
            }

            // Special filters
            if ($request->has('today') && $request->today === 'true') {
                $query->today();
            }

            if ($request->has('this_month') && $request->this_month === 'true') {
                $query->thisMonth();
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'initiated_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = min($request->get('per_page', 15), 100);
            $payments = $query->paginate($perPage);

            // Transform the data
            $transformedPayments = $payments->getCollection()->map(function ($payment) {
                return $this->transformPayment($payment);
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'payments' => $transformedPayments,
                    'pagination' => [
                        'current_page' => $payments->currentPage(),
                        'last_page' => $payments->lastPage(),
                        'per_page' => $payments->perPage(),
                        'total' => $payments->total(),
                        'from' => $payments->firstItem(),
                        'to' => $payments->lastItem()
                    ],
                    'filters' => [
                        'search' => $request->search,
                        'status' => $request->status,
                        'payment_method' => $request->payment_method,
                        'start_date' => $request->start_date,
                        'end_date' => $request->end_date
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve payments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Initiate a new payment
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function initiatePayment(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Validate payment data
            $validator = Validator::make($request->all(), [
                'inspection_request_id' => 'required|integer|exists:inspection_requests,id',
                'payment_method' => 'required|in:mtn_momo,airtel_money,visa,mastercard,bank_transfer',
                'payer_name' => 'required|string|max:255',
                'payer_phone' => 'required_if:payment_method,mtn_momo,airtel_money|string|max:20',
                'payer_email' => 'required_if:payment_method,visa,mastercard|email|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get inspection request
            $inspectionRequest = InspectionRequest::with(['package', 'businessPartner'])->findOrFail($request->inspection_request_id);

            // Check if user can pay for this inspection
            if (!$this->canPayForInspection($user, $inspectionRequest)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to pay for this inspection'
                ], 403);
            }

            // Check if payment already exists
            if ($inspectionRequest->payments()->whereIn('status', ['pending', 'processing', 'completed'])->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment already exists for this inspection request'
                ], 400);
            }

            // Calculate amount
            $amount = $inspectionRequest->calculateTotalCost();

            // Create payment record
            $payment = Payment::create([
                'inspection_request_id' => $inspectionRequest->id,
                'amount' => $amount,
                'currency' => 'RWF',
                'payment_method' => $request->payment_method,
                'payer_name' => $request->payer_name,
                'payer_phone' => $request->payer_phone,
                'payer_email' => $request->payer_email,
                'status' => 'pending'
            ]);

            // Process payment based on method
            $paymentResult = $this->processPayment($payment);

            return response()->json([
                'success' => true,
                'message' => 'Payment initiated successfully',
                'data' => [
                    'payment' => $this->transformPayment($payment),
                    'payment_instructions' => $paymentResult['instructions'] ?? null,
                    'next_action' => $paymentResult['next_action'] ?? null
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific payment
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            
            $payment = Payment::with([
                'inspectionRequest.property', 'inspectionRequest.package',
                'inspectionRequest.requester', 'logs'
            ])->findOrFail($id);

            // Check permissions
            if (!$this->canAccessPayment($user, $payment)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to view this payment'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'payment' => $this->transformPaymentDetailed($payment)
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check payment status
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function checkStatus(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $payment = Payment::findOrFail($id);

            // Check permissions
            if (!$this->canAccessPayment($user, $payment)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to check this payment status'
                ], 403);
            }

            // Refresh payment status from gateway if pending/processing
            if ($payment->isPending()) {
                $this->refreshPaymentStatus($payment);
                $payment->refresh();
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'payment_id' => $payment->id,
                    'transaction_reference' => $payment->transaction_reference,
                    'status' => $payment->status,
                    'status_info' => $payment->getStatusInfo(),
                    'amount' => $payment->amount,
                    'formatted_amount' => $payment->getFormattedAmount(),
                    'payment_method' => $payment->payment_method,
                    'payment_method_display' => $payment->getPaymentMethodDisplayName(),
                    'initiated_at' => $payment->initiated_at,
                    'completed_at' => $payment->completed_at,
                    'failed_at' => $payment->failed_at,
                    'failure_reason' => $payment->failure_reason
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check payment status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process refund (Admin only)
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function processRefund(Request $request, int $id): JsonResponse
    {
        try {
            // Check if user is admin
            if (!$request->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            $payment = Payment::findOrFail($id);

            // Validate refund data
            $validator = Validator::make($request->all(), [
                'refund_amount' => 'nullable|numeric|min:0|max:' . $payment->amount,
                'reason' => 'required|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Process refund
            $refundAmount = $request->get('refund_amount', $payment->amount);
            $payment->processRefund($refundAmount);

            return response()->json([
                'success' => true,
                'message' => 'Refund processed successfully',
                'data' => [
                    'payment' => $this->transformPayment($payment),
                    'refund_amount' => $refundAmount,
                    'reason' => $request->reason
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process refund',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel payment
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function cancelPayment(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $payment = Payment::findOrFail($id);

            // Check permissions
            if (!$this->canModifyPayment($user, $payment)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to cancel this payment'
                ], 403);
            }

            // Check if payment can be cancelled
            if (!$payment->isPending()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending payments can be cancelled'
                ], 400);
            }

            // Validate cancellation data
            $validator = Validator::make($request->all(), [
                'reason' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Cancel payment
            $reason = $request->get('reason', 'Cancelled by user');
            $payment->cancel($reason);

            return response()->json([
                'success' => true,
                'message' => 'Payment cancelled successfully',
                'data' => [
                    'payment' => $this->transformPayment($payment)
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment analytics (Admin/Head Technician only)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getAnalytics(Request $request): JsonResponse
    {
        try {
            // Check permissions
            if (!$request->user()->isAdmin() && !$request->user()->isHeadTechnician()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin or Head Technician access required.'
                ], 403);
            }

            // Validate date range
            $validator = Validator::make($request->all(), [
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $startDate = $request->start_date ? Carbon::parse($request->start_date) : null;
            $endDate = $request->end_date ? Carbon::parse($request->end_date) : null;

            $analytics = Payment::getAnalytics($startDate, $endDate);

            return response()->json([
                'success' => true,
                'data' => [
                    'analytics' => $analytics,
                    'period' => [
                        'start_date' => $startDate?->toDateString(),
                        'end_date' => $endDate?->toDateString(),
                        'is_current_month' => !$startDate && !$endDate
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get payment analytics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available payment methods
     * 
     * @return JsonResponse
     */
    public function getPaymentMethods(): JsonResponse
    {
        try {
            $paymentMethods = [
                'mtn_momo' => [
                    'name' => 'MTN Mobile Money',
                    'description' => 'Pay using MTN Mobile Money',
                    'icon' => 'mtn-logo.png',
                    'supported_currencies' => ['RWF'],
                    'requires_phone' => true,
                    'processing_time' => 'Instant',
                    'is_available' => true
                ],
                'airtel_money' => [
                    'name' => 'Airtel Money',
                    'description' => 'Pay using Airtel Money',
                    'icon' => 'airtel-logo.png',
                    'supported_currencies' => ['RWF'],
                    'requires_phone' => true,
                    'processing_time' => 'Instant',
                    'is_available' => true
                ],
                'visa' => [
                    'name' => 'Visa Card',
                    'description' => 'Pay using Visa credit/debit card',
                    'icon' => 'visa-logo.png',
                    'supported_currencies' => ['RWF', 'USD', 'EUR'],
                    'requires_phone' => false,
                    'processing_time' => '1-2 minutes',
                    'is_available' => true
                ],
                'mastercard' => [
                    'name' => 'Mastercard',
                    'description' => 'Pay using Mastercard credit/debit card',
                    'icon' => 'mastercard-logo.png',
                    'supported_currencies' => ['RWF', 'USD', 'EUR'],
                    'requires_phone' => false,
                    'processing_time' => '1-2 minutes',
                    'is_available' => true
                ],
                'bank_transfer' => [
                    'name' => 'Bank Transfer',
                    'description' => 'Direct bank transfer (Business partners only)',
                    'icon' => 'bank-logo.png',
                    'supported_currencies' => ['RWF'],
                    'requires_phone' => false,
                    'processing_time' => '1-3 business days',
                    'is_available' => false // Only for business partners
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'payment_methods' => $paymentMethods,
                    'default_currency' => 'RWF'
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get payment methods',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Webhook endpoint for payment status updates
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function webhook(Request $request): JsonResponse
    {
        try {
            // Validate webhook data (implement signature verification in production)
            $validator = Validator::make($request->all(), [
                'transaction_reference' => 'required|string',
                'status' => 'required|string',
                'gateway_transaction_id' => 'nullable|string',
                'gateway_reference' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid webhook data'
                ], 400);
            }

            // Find payment by transaction reference
            $payment = Payment::where('transaction_reference', $request->transaction_reference)->first();

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found'
                ], 404);
            }

            // Update payment status based on webhook
            switch (strtolower($request->status)) {
                case 'success':
                case 'completed':
                    $payment->markAsCompleted(
                        $request->gateway_transaction_id,
                        $request->gateway_reference
                    );
                    break;
                
                case 'failed':
                case 'declined':
                    $payment->markAsFailed(
                        $request->get('failure_reason', 'Payment failed'),
                        $request->all()
                    );
                    break;
                
                case 'processing':
                case 'pending':
                    $payment->markAsProcessing($request->gateway_transaction_id);
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Webhook processing failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process payment based on method
     * 
     * @param Payment $payment
     * @return array
     */
    private function processPayment(Payment $payment): array
    {
        // This is a simplified implementation
        // In production, integrate with actual payment gateways

        switch ($payment->payment_method) {
            case 'mtn_momo':
                return $this->processMTNMoMo($payment);
            
            case 'airtel_money':
                return $this->processAirtelMoney($payment);
            
            case 'visa':
            case 'mastercard':
                return $this->processCardPayment($payment);
            
            case 'bank_transfer':
                return $this->processBankTransfer($payment);
            
            default:
                throw new \Exception('Unsupported payment method');
        }
    }

    /**
     * Process MTN Mobile Money payment
     * 
     * @param Payment $payment
     * @return array
     */
    private function processMTNMoMo(Payment $payment): array
    {
        // Simulate MTN MoMo API call
        $payment->markAsProcessing('MTN' . time());
        
        return [
            'instructions' => "Please dial *182*7*1# and follow the prompts to complete your payment of {$payment->getFormattedAmount()}",
            'next_action' => 'wait_for_ussd_completion'
        ];
    }

    /**
     * Process Airtel Money payment
     * 
     * @param Payment $payment
     * @return array
     */
    private function processAirtelMoney(Payment $payment): array
    {
        // Simulate Airtel Money API call
        $payment->markAsProcessing('AIRTEL' . time());
        
        return [
            'instructions' => "Please dial *175# and follow the prompts to complete your payment of {$payment->getFormattedAmount()}",
            'next_action' => 'wait_for_ussd_completion'
        ];
    }

    /**
     * Process card payment
     * 
     * @param Payment $payment
     * @return array
     */
    private function processCardPayment(Payment $payment): array
    {
        // Simulate Stripe API call
        $payment->markAsProcessing('STRIPE' . time());
        
        return [
            'instructions' => 'Please complete the card payment in the secure form',
            'next_action' => 'redirect_to_gateway',
            'gateway_url' => 'https://checkout.stripe.com/pay/...'
        ];
    }

    /**
     * Process bank transfer
     * 
     * @param Payment $payment
     * @return array
     */
    private function processBankTransfer(Payment $payment): array
    {
        return [
            'instructions' => 'Please transfer the amount to our bank account. Reference: ' . $payment->transaction_reference,
            'next_action' => 'manual_verification_required',
            'bank_details' => [
                'bank_name' => 'Bank of Kigali',
                'account_number' => '123456789',
                'account_name' => 'Fundi Rwanda Ltd',
                'reference' => $payment->transaction_reference
            ]
        ];
    }

    /**
     * Refresh payment status from gateway
     * 
     * @param Payment $payment
     * @return void
     */
    private function refreshPaymentStatus(Payment $payment): void
    {
        // In production, call actual payment gateway APIs to check status
        // For now, simulate random completion for demo purposes
        if ($payment->initiated_at->diffInMinutes() > 2) {
            // Simulate payment completion after 2 minutes
            $payment->markAsCompleted('GATEWAY' . time());
        }
    }

    /**
     * Check if user can pay for inspection
     * 
     * @param User $user
     * @param InspectionRequest $inspectionRequest
     * @return bool
     */
    private function canPayForInspection($user, $inspectionRequest): bool
    {
        // Requester can pay for their own inspection
        if ($inspectionRequest->requester_user_id === $user->id) {
            return true;
        }

        // Business partner users can pay for their organization's inspections
        if ($user->isBusinessPartner() && $user->businessPartners->contains($inspectionRequest->business_partner_id)) {
            return true;
        }

        // Admins can initiate payments
        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Check if user can access payment
     * 
     * @param User $user
     * @param Payment $payment
     * @return bool
     */
    private function canAccessPayment($user, $payment): bool
    {
        // Admins can access all payments
        if ($user->isAdmin() || $user->isHeadTechnician()) {
            return true;
        }

        return $this->canPayForInspection($user, $payment->inspectionRequest);
    }

    /**
     * Check if user can modify payment
     * 
     * @param User $user
     * @param Payment $payment
     * @return bool
     */
    private function canModifyPayment($user, $payment): bool
    {
        // Only admins and the payment creator can modify payments
        if ($user->isAdmin()) {
            return true;
        }

        return $payment->inspectionRequest->requester_user_id === $user->id;
    }

    /**
     * Transform payment for API response
     * 
     * @param Payment $payment
     * @return array
     */
    private function transformPayment($payment): array
    {
        return [
            'id' => $payment->id,
            'transaction_reference' => $payment->transaction_reference,
            'amount' => $payment->amount,
            'formatted_amount' => $payment->getFormattedAmount(),
            'currency' => $payment->currency,
            'payment_method' => $payment->payment_method,
            'payment_method_display' => $payment->getPaymentMethodDisplayName(),
            'status' => $payment->status,
            'status_info' => $payment->getStatusInfo(),
            'payer_info' => [
                'name' => $payment->payer_name,
                'phone' => $payment->payer_phone,
                'email' => $payment->payer_email
            ],
            'inspection_request' => [
                'id' => $payment->inspectionRequest->id,
                'request_number' => $payment->inspectionRequest->request_number,
                'property_address' => $payment->inspectionRequest->property->address ?? null,
                'package_name' => $payment->inspectionRequest->package->display_name ?? null
            ],
            'gateway_info' => [
                'provider' => $payment->gateway_provider,
                'transaction_id' => $payment->gateway_transaction_id,
                'reference' => $payment->gateway_reference
            ],
            'timeline' => [
                'initiated_at' => $payment->initiated_at,
                'completed_at' => $payment->completed_at,
                'failed_at' => $payment->failed_at,
                'processing_time_minutes' => $payment->getProcessingTimeMinutes()
            ],
            'failure_reason' => $payment->failure_reason,
            'statistics' => $payment->getStatistics()
        ];
    }

    /**
     * Transform payment with detailed information
     * 
     * @param Payment $payment
     * @return array
     */
    private function transformPaymentDetailed($payment): array
    {
        $basic = $this->transformPayment($payment);
        
        // Add detailed inspection request information
        $basic['inspection_request_details'] = [
            'id' => $payment->inspectionRequest->id,
            'request_number' => $payment->inspectionRequest->request_number,
            'requester' => $payment->inspectionRequest->requester->full_name,
            'property' => [
                'property_code' => $payment->inspectionRequest->property->property_code,
                'address' => $payment->inspectionRequest->property->address,
                'type' => $payment->inspectionRequest->property->property_type
            ],
            'package' => [
                'name' => $payment->inspectionRequest->package->display_name,
                'original_price' => $payment->inspectionRequest->package->price
            ]
        ];

        // Add payment logs for debugging (admin only)
        $basic['payment_logs'] = $payment->logs->map(function ($log) {
            return [
                'action' => $log->action,
                'status_before' => $log->status_before,
                'status_after' => $log->status_after,
                'notes' => $log->notes,
                'logged_at' => $log->logged_at
            ];
        });

        return $basic;
    }
}