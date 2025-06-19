<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\InspectionRequest;
use App\Models\BusinessPartner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments
     */
    public function index(Request $request)
    {
        $query = Payment::with(['inspectionRequest.property', 'inspectionRequest.requester', 'inspectionRequest.businessPartner']);

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

        // Filter by business partner
        if ($request->has('business_partner') && $request->business_partner) {
            $query->whereHas('inspectionRequest', function ($q) use ($request) {
                $q->where('business_partner_id', $request->business_partner);
            });
        }

        // Date range filter
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('initiated_at', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('initiated_at', '<=', $request->end_date);
        }

        // Amount range filter
        if ($request->has('min_amount') && $request->min_amount) {
            $query->where('amount', '>=', $request->min_amount);
        }
        if ($request->has('max_amount') && $request->max_amount) {
            $query->where('amount', '<=', $request->max_amount);
        }

        // Sort
        $sortBy = $request->get('sort', 'initiated_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $payments = $query->paginate(20)->withQueryString();

        // Get filter options
        $businessPartners = BusinessPartner::active()->pluck('name', 'id');

        // Calculate statistics
        $stats = [
            'total_payments' => Payment::count(),
            'total_amount' => Payment::completed()->sum('amount'),
            'pending_payments' => Payment::pending()->count(),
            'completed_payments' => Payment::completed()->count(),
            'failed_payments' => Payment::failed()->count(),
            'today_payments' => Payment::today()->count(),
            'this_month_payments' => Payment::thisMonth()->count(),
            'this_month_amount' => Payment::thisMonth()->completed()->sum('amount'),
        ];

        return view('admin.payments.index', compact(
            'payments', 
            'stats', 
            'businessPartners'
        ));
    }

    /**
     * Display the specified payment
     */
    public function show(Payment $payment)
    {
        $payment->load([
            'inspectionRequest.property',
            'inspectionRequest.package',
            'inspectionRequest.requester',
            'inspectionRequest.businessPartner',
            'logs' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }
        ]);

        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Process refund for a payment
     */
    public function refund(Request $request, Payment $payment)
    {
        $validator = Validator::make($request->all(), [
            'refund_amount' => 'required|numeric|min:0.01|max:' . $payment->amount,
            'refund_reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Check if payment can be refunded
            if (!$payment->isSuccessful()) {
                return redirect()->back()
                    ->with('error', 'Only completed payments can be refunded.');
            }

            // Process refund
            $payment->processRefund($request->refund_amount);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Refund processed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to process refund: ' . $e->getMessage());
        }
    }

    /**
     * Mark payment as manually completed
     */
    public function markCompleted(Request $request, Payment $payment)
    {
        $validator = Validator::make($request->all(), [
            'gateway_transaction_id' => 'nullable|string|max:255',
            'gateway_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Check if payment can be marked as completed
            if ($payment->isSuccessful()) {
                return redirect()->back()
                    ->with('error', 'Payment is already completed.');
            }

            // Mark as completed
            $payment->markAsCompleted(
                $request->gateway_transaction_id,
                $request->gateway_reference
            );

            // Add note if provided
            if ($request->notes) {
                $payment->logs()->create([
                    'action' => 'manual_completion',
                    'notes' => $request->notes,
                    'status_before' => $payment->getOriginal('status'),
                    'status_after' => 'completed'
                ]);
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Payment marked as completed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to mark payment as completed: ' . $e->getMessage());
        }
    }

    /**
     * Get payment analytics
     */
    public function analytics(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : null;

        $analytics = Payment::getAnalytics($startDate, $endDate);

        // Get payment method breakdown
        $methodBreakdown = Payment::when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            return $query->whereBetween('initiated_at', [$startDate, $endDate]);
        })
        ->completed()
        ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total_amount')
        ->groupBy('payment_method')
        ->get();

        // Get daily payment trends
        $dailyTrends = Payment::when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            return $query->whereBetween('initiated_at', [$startDate, $endDate]);
        })
        ->selectRaw('DATE(initiated_at) as date, COUNT(*) as count, SUM(amount) as total_amount')
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        // Get business partner payment breakdown
        $partnerBreakdown = Payment::when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            return $query->whereBetween('initiated_at', [$startDate, $endDate]);
        })
        ->whereHas('inspectionRequest.businessPartner')
        ->with('inspectionRequest.businessPartner')
        ->completed()
        ->get()
        ->groupBy('inspectionRequest.businessPartner.name')
        ->map(function ($payments) {
            return [
                'count' => $payments->count(),
                'total_amount' => $payments->sum('amount'),
                'average_amount' => $payments->avg('amount')
            ];
        });

        return view('admin.payments.analytics', compact(
            'analytics',
            'methodBreakdown',
            'dailyTrends',
            'partnerBreakdown',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export payments to CSV
     */
    public function export(Request $request)
    {
        $query = Payment::with(['inspectionRequest.property', 'inspectionRequest.requester', 'inspectionRequest.businessPartner']);

        // Apply filters
        if ($request->has('status') && $request->status) {
            $query->byStatus($request->status);
        }

        if ($request->has('payment_method') && $request->payment_method) {
            $query->byMethod($request->payment_method);
        }

        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('initiated_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('initiated_at', '<=', $request->end_date);
        }

        $payments = $query->orderBy('initiated_at', 'desc')->get();

        $filename = 'payments_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($payments) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Transaction Reference',
                'Amount (RWF)',
                'Payment Method',
                'Status',
                'Payer Name',
                'Payer Phone',
                'Payer Email',
                'Inspection Request',
                'Property Address',
                'Business Partner',
                'Initiated At',
                'Completed At',
                'Failure Reason'
            ]);

            // CSV data
            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->transaction_reference,
                    $payment->amount,
                    $payment->getPaymentMethodDisplayName(),
                    $payment->status,
                    $payment->payer_name,
                    $payment->payer_phone,
                    $payment->payer_email,
                    $payment->inspectionRequest->request_number ?? '',
                    $payment->inspectionRequest->property->address ?? '',
                    $payment->inspectionRequest->businessPartner->name ?? '',
                    $payment->initiated_at?->format('Y-m-d H:i:s'),
                    $payment->completed_at?->format('Y-m-d H:i:s'),
                    $payment->failure_reason
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
} 