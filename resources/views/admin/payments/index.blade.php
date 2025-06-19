@extends('layouts.admin')

@section('title', 'Payment Management')

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Payment Management
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <svg class="mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" />
                </svg>
                Manage and monitor all payment transactions
            </div>
        </div>
    </div>
    <div class="mt-4 flex space-x-3 md:ml-4 md:mt-0">
        <a href="{{ route('admin.payments.analytics') }}" 
           class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
            </svg>
            Analytics
        </a>
        <a href="{{ route('admin.payments.export') }}" 
           class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
            Export CSV
        </a>
    </div>
</div>
@endsection

@section('content')
<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    <!-- Total Payments -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Payments</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_payments']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
                <span class="text-green-600 font-medium">{{ number_format($stats['this_month_payments']) }}</span>
                <span class="text-gray-500"> this month</span>
            </div>
        </div>
    </div>

    <!-- Total Amount -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Amount</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_amount']) }} RWF</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
                <span class="text-green-600 font-medium">{{ number_format($stats['this_month_amount']) }} RWF</span>
                <span class="text-gray-500"> this month</span>
            </div>
        </div>
    </div>

    <!-- Completed Payments -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-emerald-500 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Completed</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['completed_payments']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
                <span class="text-orange-600 font-medium">{{ number_format($stats['pending_payments']) }}</span>
                <span class="text-gray-500"> pending</span>
            </div>
        </div>
    </div>

    <!-- Failed Payments -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Failed</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['failed_payments']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
                <span class="text-blue-600 font-medium">{{ number_format($stats['today_payments']) }}</span>
                <span class="text-gray-500"> today</span>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="bg-white shadow rounded-lg mb-6">
    <div class="px-4 py-5 sm:p-6">
        <form method="GET" class="space-y-4 sm:space-y-0 sm:grid sm:grid-cols-1 lg:grid-cols-6 sm:gap-4">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" name="search" id="search" 
                           class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" 
                           placeholder="Search by reference, payer name, phone..." 
                           value="{{ request('search') }}">
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>

            <!-- Payment Method Filter -->
            <div>
                <label for="payment_method" class="block text-sm font-medium text-gray-700">Method</label>
                <select name="payment_method" id="payment_method" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">All Methods</option>
                    <option value="mtn_momo" {{ request('payment_method') == 'mtn_momo' ? 'selected' : '' }}>MTN Mobile Money</option>
                    <option value="airtel_money" {{ request('payment_method') == 'airtel_money' ? 'selected' : '' }}>Airtel Money</option>
                    <option value="visa" {{ request('payment_method') == 'visa' ? 'selected' : '' }}>Visa Card</option>
                    <option value="mastercard" {{ request('payment_method') == 'mastercard' ? 'selected' : '' }}>Mastercard</option>
                    <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                </select>
            </div>

            <!-- Business Partner Filter -->
            <div>
                <label for="business_partner" class="block text-sm font-medium text-gray-700">Partner</label>
                <select name="business_partner" id="business_partner" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">All Partners</option>
                    @foreach($businessPartners as $id => $name)
                        <option value="{{ $id }}" {{ request('business_partner') == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Submit Button -->
            <div class="flex items-end">
                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                    Filter
                </button>
            </div>
        </form>

        @if(request()->hasAny(['search', 'status', 'payment_method', 'business_partner']))
        <div class="mt-4 flex items-center justify-between">
            <p class="text-sm text-gray-500">
                Showing filtered results
            </p>
            <a href="{{ route('admin.payments.index') }}" 
               class="text-sm text-indigo-600 hover:text-indigo-500">
                Clear filters
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Payments Table -->
<div class="bg-white shadow overflow-hidden sm:rounded-md">
    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Payments ({{ $payments->total() }})
            </h3>
            <div class="flex items-center space-x-4">
                <!-- Sort Dropdown -->
                <div class="relative">
                    <select onchange="updateSort(this.value)" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="initiated_at-desc" {{ request('sort') == 'initiated_at' && request('direction') == 'desc' ? 'selected' : '' }}>
                            Newest First
                        </option>
                        <option value="initiated_at-asc" {{ request('sort') == 'initiated_at' && request('direction') == 'asc' ? 'selected' : '' }}>
                            Oldest First
                        </option>
                        <option value="amount-desc" {{ request('sort') == 'amount' && request('direction') == 'desc' ? 'selected' : '' }}>
                            Highest Amount
                        </option>
                        <option value="amount-asc" {{ request('sort') == 'amount' && request('direction') == 'asc' ? 'selected' : '' }}>
                            Lowest Amount
                        </option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <ul role="list" class="divide-y divide-gray-200">
        @forelse($payments as $payment)
        <li>
            <div class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center min-w-0 flex-1">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full {{ 
                                $payment->status === 'completed' ? 'bg-green-100' : 
                                ($payment->status === 'failed' ? 'bg-red-100' : 
                                ($payment->status === 'pending' ? 'bg-yellow-100' : 'bg-gray-100')) 
                            }} flex items-center justify-center">
                                @if($payment->payment_method === 'mtn_momo')
                                    <svg class="h-6 w-6 {{ 
                                        $payment->status === 'completed' ? 'text-green-600' : 
                                        ($payment->status === 'failed' ? 'text-red-600' : 
                                        ($payment->status === 'pending' ? 'text-yellow-600' : 'text-gray-600')) 
                                    }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                @elseif($payment->payment_method === 'airtel_money')
                                    <svg class="h-6 w-6 {{ 
                                        $payment->status === 'completed' ? 'text-green-600' : 
                                        ($payment->status === 'failed' ? 'text-red-600' : 
                                        ($payment->status === 'pending' ? 'text-yellow-600' : 'text-gray-600')) 
                                    }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                @else
                                    <svg class="h-6 w-6 {{ 
                                        $payment->status === 'completed' ? 'text-green-600' : 
                                        ($payment->status === 'failed' ? 'text-red-600' : 
                                        ($payment->status === 'pending' ? 'text-yellow-600' : 'text-gray-600')) 
                                    }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                @endif
                            </div>
                        </div>
                        <div class="ml-4 flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-indigo-600 truncate">
                                        <a href="{{ route('admin.payments.show', $payment) }}" class="hover:text-indigo-500">
                                            {{ $payment->transaction_reference }}
                                        </a>
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ $payment->getPaymentMethodDisplayName() }}
                                        @if($payment->inspectionRequest)
                                            • {{ $payment->inspectionRequest->request_number }}
                                        @endif
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <!-- Payment Status -->
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                        $payment->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                        ($payment->status === 'failed' ? 'bg-red-100 text-red-800' : 
                                        ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) 
                                    }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="mt-2 flex items-center text-sm text-gray-500">
                                <div class="flex items-center">
                                    <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                    {{ $payment->payer_name }}
                                    @if($payment->payer_phone)
                                        • {{ $payment->payer_phone }}
                                    @endif
                                </div>
                                <div class="ml-4 flex items-center">
                                    <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $payment->initiated_at->format('M j, Y g:i A') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex space-x-2">
                        <!-- Amount -->
                        <div class="text-right">
                            <p class="text-lg font-medium text-gray-900">{{ number_format($payment->amount) }} RWF</p>
                            @if($payment->inspectionRequest && $payment->inspectionRequest->property)
                                <p class="text-sm text-gray-500 truncate max-w-xs">
                                    {{ $payment->inspectionRequest->property->address }}
                                </p>
                            @endif
                        </div>

                        <!-- View Button -->
                        <a href="{{ route('admin.payments.show', $payment) }}" 
                           class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                            View
                        </a>

                        <!-- Actions Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="inline-flex items-center px-2 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" 
                                 class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                <div class="py-1">
                                    @if($payment->status === 'completed')
                                        <button onclick="showRefundModal({{ $payment->id }}, {{ $payment->amount }})" 
                                                class="block w-full text-left px-4 py-2 text-sm text-purple-700 hover:bg-purple-50">
                                            Process Refund
                                        </button>
                                    @endif
                                    @if($payment->status === 'pending' || $payment->status === 'processing')
                                        <button onclick="showMarkCompletedModal({{ $payment->id }})" 
                                                class="block w-full text-left px-4 py-2 text-sm text-green-700 hover:bg-green-50">
                                            Mark Completed
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        @empty
        <li class="px-4 py-8 text-center">
            <div class="text-sm text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                </svg>
                <p class="mt-2">No payments found</p>
                <p class="mt-1">
                    @if(request()->hasAny(['search', 'status', 'payment_method', 'business_partner']))
                        <a href="{{ route('admin.payments.index') }}" class="text-indigo-600 hover:text-indigo-500">Clear filters</a>
                        or
                    @endif
                    <span class="text-gray-400">wait for new payments to be processed</span>
                </p>
            </div>
        </li>
        @endforelse
    </ul>

    <!-- Pagination -->
    @if($payments->hasPages())
    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
        {{ $payments->links() }}
    </div>
    @endif
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
function updateSort(value) {
    const [sort, direction] = value.split('-');
    const url = new URL(window.location);
    url.searchParams.set('sort', sort);
    url.searchParams.set('direction', direction);
    window.location = url;
}

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