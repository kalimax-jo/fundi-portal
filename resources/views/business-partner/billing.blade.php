@extends('layouts.business-partner')

@section('title', 'Billing & Subscription')

@section('page-header')
<div class="flex items-center justify-between">
    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
        Billing & Subscription
    </h2>
</div>
@endsection

@section('content')
<div class="w-full py-10 px-2 md:px-6">
    @if($activeTier)
        <div class="mb-10 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between">
            <div>
                <div class="text-sm text-gray-700">Current Tier:</div>
                <div class="text-lg font-semibold text-green-800">{{ $activeTier->tier->name }}</div>
                <div class="text-xs text-gray-500">Expires: {{ $activeTier->expires_at ? $activeTier->expires_at->format('M d, Y') : 'N/A' }}</div>
                <div class="text-xs text-gray-500 mt-1">Remaining Requests: <span class="font-bold text-green-700">{{ $remainingRequests }}</span> of {{ $activeTier->tier->request_quota }}</div>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
        </div>
    @endif

    <div class="mb-14 w-full">
        <h3 class="text-xl font-bold text-gray-900 mb-6 border-b pb-2">Available Tiers</h3>
        <div class="overflow-x-auto bg-white shadow rounded-lg w-full">
            <table class="w-full min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quota</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Packages</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($tiers as $tier)
                        <tr class="{{ $activeTier && $activeTier->tier_id === $tier->id ? 'bg-green-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap font-bold">{{ $tier->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-indigo-700 font-semibold">{{ number_format($tier->price, 2) }} RWF</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $tier->request_quota }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <ul class="text-xs text-gray-500 list-disc list-inside">
                                    @foreach($tier->inspectionPackages as $package)
                                        <li>{{ $package->display_name ?? $package->name }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($activeTier && $activeTier->tier_id === $tier->id)
                                    <button class="px-4 py-2 rounded bg-green-100 text-green-700 font-semibold cursor-not-allowed" disabled>
                                        Current Tier
                                    </button>
                                @else
                                    <form method="POST" action="{{ route('partner.billing.select-tier', $tier) }}">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 rounded bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition">
                                            Select & Pay
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mb-10 w-full">
        <h3 class="text-xl font-bold text-gray-900 mb-6 border-b pb-2">Pending Invoices</h3>
        <div class="overflow-x-auto bg-white shadow rounded-lg w-full">
            <table class="w-full min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tier</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pendingInvoices as $invoice)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap font-bold">#{{ $invoice->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $invoice->partnerTier->tier->name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-orange-700 font-semibold">{{ number_format($invoice->amount, 2) }} RWF</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $invoice->created_at ? \Carbon\Carbon::parse($invoice->created_at)->format('M d, Y') : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="#" class="text-indigo-600 hover:text-indigo-900 font-medium">Pay Now</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No pending invoices found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mb-10 w-full">
        <h3 class="text-xl font-bold text-gray-900 mb-6 border-b pb-2">Paid Invoices</h3>
        <div class="overflow-x-auto bg-white shadow rounded-lg w-full">
            <table class="w-full min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tier</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paid Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Download</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($paidInvoices as $invoice)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap font-bold">#{{ $invoice->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $invoice->partnerTier->tier->name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-green-700 font-semibold">{{ number_format($invoice->amount, 2) }} RWF</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $invoice->paid_at ? \Carbon\Carbon::parse($invoice->paid_at)->format('M d, Y') : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="#" class="text-indigo-600 hover:text-indigo-900 font-medium">Download</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No paid invoices found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 