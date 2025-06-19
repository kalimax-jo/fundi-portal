@extends('layouts.admin')

@section('title', 'Payment Details - ' . $payment->transaction_reference)

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Payment Details
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <svg class="mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" />
                </svg>
                Transaction: {{ $payment->transaction_reference }}
            </div>
        </div>
    </div>
    <div class="mt-4 flex space-x-3 md:ml-4 md:mt-0">
        <a href="{{ route('admin.payments.index') }}" 
           class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to Payments
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Payment Details -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Payment Information Card -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Payment Information</h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                        $payment->status === 'completed' ? 'bg-green-100 text-green-800' : 
                        ($payment->status === 'failed' ? 'bg-red-100 text-red-800' : 
                        ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) 
                    }}">
                        {{ ucfirst($payment->status) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Transaction Reference</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $payment->transaction_reference }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Amount</dt>
                            <dd class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($payment->amount) }} {{ $payment->currency }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $payment->getPaymentMethodDisplayName() }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Gateway Provider</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $payment->gateway_provider ?? 'N/A' }}</dd>
                        </div>

                        @if($payment->gateway_transaction_id)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Gateway Transaction ID</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $payment->gateway_transaction_id }}</dd>
                        </div>
                        @endif

                        @if($payment->gateway_reference)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Gateway Reference</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $payment->gateway_reference }}</dd>
                        </div>
                        @endif
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Payer Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $payment->payer_name }}</dd>
                        </div>

                        @if($payment->payer_phone)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Payer Phone</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $payment->payer_phone }}</dd>
                        </div>
                        @endif

                        @if($payment->payer_email)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Payer Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $payment->payer_email }}</dd>
                        </div>
                        @endif

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Initiated At</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $payment->initiated_at->format('M j, Y g:i A') }}</dd>
                        </div>

                        @if($payment->completed_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Completed At</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $payment->completed_at->format('M j, Y g:i A') }}</dd>
                        </div>
                        @endif

                        @if($payment->failed_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Failed At</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $payment->failed_at->format('M j, Y g:i A') }}</dd>
                        </div>
                        @endif

                        @if($payment->failure_reason)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Failure Reason</dt>
                            <dd class="mt-1 text-sm text-red-600">{{ $payment->failure_reason }}</dd>
                        </div>
                        @endif

                        @if($payment->getProcessingTimeMinutes())
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Processing Time</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $payment->getProcessingTimeMinutes() }} minutes</dd>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Inspection Request Information -->
        @if($payment->inspectionRequest)
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Related Inspection Request</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Request Number</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $payment->inspectionRequest->request_number }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Requester</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $payment->inspectionRequest->requester->name ?? 'N/A' }}</dd>
                        </div>

                        @if($payment->inspectionRequest->businessPartner)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Business Partner</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $payment->inspectionRequest->businessPartner->name }}</dd>
                        </div>
                        @endif

                        @if($payment->inspectionRequest->package)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Package</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $payment->inspectionRequest->package->display_name }}</dd>
                        </div>
                        @endif
                    </div>

                    <div class="space-y-4">
                        @if($payment->inspectionRequest->property)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Property Address</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $payment->inspectionRequest->property->address }}</dd>
                        </div>
                        @endif

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Request Status</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                    $payment->inspectionRequest->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                    ($payment->inspectionRequest->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                                    ($payment->inspectionRequest->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) 
                                }}">
                                    {{ ucfirst(str_replace('_', ' ', $payment->inspectionRequest->status)) }}
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Payment Status</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                    $payment->inspectionRequest->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 
                                    ($payment->inspectionRequest->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') 
                                }}">
                                    {{ ucfirst($payment->inspectionRequest->payment_status) }}
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created At</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $payment->inspectionRequest->created_at->format('M j, Y g:i A') }}</dd>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex space-x-3">
                    <a href="{{ route('admin.inspection-requests.show', $payment->inspectionRequest) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        View Inspection Request
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- Payment Logs -->
        @if($payment->logs->count() > 0)
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Payment Activity Log</h3>
                
                <div class="flow-root">
                    <ul role="list" class="-mb-8">
                        @foreach($payment->logs as $log)
                        <li>
                            <div class="relative pb-8">
                                @if(!$loop->last)
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                            <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-gray-500">
                                                {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                                @if($log->notes)
                                                    <span class="font-medium text-gray-900">{{ $log->notes }}</span>
                                                @endif
                                            </p>
                                            @if($log->status_before && $log->status_after)
                                            <p class="text-xs text-gray-400">
                                                Status changed from 
                                                <span class="font-medium">{{ ucfirst($log->status_before) }}</span> 
                                                to 
                                                <span class="font-medium">{{ ucfirst($log->status_after) }}</span>
                                            </p>
                                            @endif
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                            <time datetime="{{ $log->created_at->format('Y-m-d H:i:s') }}">
                                                {{ $log->created_at->format('M j, Y g:i A') }}
                                            </time>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Payment Actions -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Actions</h3>
                
                <div class="space-y-3">
                    @if($payment->status === 'completed')
                    <button onclick="showRefundModal({{ $payment->id }}, {{ $payment->amount }})" 
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                        Process Refund
                    </button>
                    @endif

                    @if($payment->status === 'pending' || $payment->status === 'processing')
                    <button onclick="showMarkCompletedModal({{ $payment->id }})" 
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Mark Completed
                    </button>
                    @endif

                    <a href="{{ route('admin.payments.index') }}" 
                       class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        Back to List
                    </a>
                </div>
            </div>
        </div>

        <!-- Payment Statistics -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Payment Statistics</h3>
                
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                        <dd class="text-sm text-gray-900">{{ $payment->getPaymentMethodDisplayName() }}</dd>
                    </div>

                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Is Mobile Money</dt>
                        <dd class="text-sm text-gray-900">{{ $payment->isMobileMoney() ? 'Yes' : 'No' }}</dd>
                    </div>

                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Is Card Payment</dt>
                        <dd class="text-sm text-gray-900">{{ $payment->isCardPayment() ? 'Yes' : 'No' }}</dd>
                    </div>

                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Is Successful</dt>
                        <dd class="text-sm text-gray-900">{{ $payment->isSuccessful() ? 'Yes' : 'No' }}</dd>
                    </div>

                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Days Since Payment</dt>
                        <dd class="text-sm text-gray-900">{{ $payment->initiated_at->diffInDays(now()) }} days</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>

<!-- Refund Modal -->
<div id="refundModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Process Refund</h3>
            <form id="refundForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="refund_amount" class="block text-sm font-medium text-gray-700">Refund Amount (RWF)</label>
                    <input type="number" step="0.01" id="refund_amount" name="refund_amount" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div class="mb-4">
                    <label for="refund_reason" class="block text-sm font-medium text-gray-700">Refund Reason</label>
                    <textarea id="refund_reason" name="refund_reason" rows="3" required
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeRefundModal()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 rounded-md">
                        Process Refund
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Mark Completed Modal -->
<div id="markCompletedModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Mark Payment as Completed</h3>
            <form id="markCompletedForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="gateway_transaction_id" class="block text-sm font-medium text-gray-700">Gateway Transaction ID</label>
                    <input type="text" id="gateway_transaction_id" name="gateway_transaction_id"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div class="mb-4">
                    <label for="gateway_reference" class="block text-sm font-medium text-gray-700">Gateway Reference</label>
                    <input type="text" id="gateway_reference" name="gateway_reference"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div class="mb-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeMarkCompletedModal()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-md">
                        Mark Completed
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showRefundModal(paymentId, maxAmount) {
    document.getElementById('refund_amount').value = maxAmount;
    document.getElementById('refund_amount').max = maxAmount;
    document.getElementById('refundForm').action = `/admin/payments/${paymentId}/refund`;
    document.getElementById('refundModal').classList.remove('hidden');
}

function closeRefundModal() {
    document.getElementById('refundModal').classList.add('hidden');
}

function showMarkCompletedModal(paymentId) {
    document.getElementById('markCompletedForm').action = `/admin/payments/${paymentId}/mark-completed`;
    document.getElementById('markCompletedModal').classList.remove('hidden');
}

function closeMarkCompletedModal() {
    document.getElementById('markCompletedModal').classList.add('hidden');
}
</script>
@endpush

@endsection 