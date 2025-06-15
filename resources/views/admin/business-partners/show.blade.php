{{-- File Path: resources/views/admin/business-partners/show.blade.php --}}

@extends('layouts.admin')

@section('title', $businessPartner->name . ' - Business Partner')

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            {{ $businessPartner->name }}
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <a href="{{ route('admin.business-partners.index') }}" class="text-indigo-600 hover:text-indigo-500">Business Partners</a>
                <span class="mx-2">/</span>
                <span>{{ $businessPartner->name }}</span>
            </div>
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <svg class="mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.75 2.75 0 0118 6.75v8.5A2.75 2.75 0 0115.25 18H4.75A2.75 2.75 0 012 15.25v-8.5A2.75 2.75 0 014.75 4H5V2.75A.75.75 0 015.75 2zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75z" clip-rule="evenodd" />
                </svg>
                Partner since {{ $businessPartner->partnership_start_date->format('M d, Y') }}
            </div>
        </div>
    </div>
    <div class="mt-4 flex space-x-3 md:ml-4 md:mt-0">
        <!-- Status Badge -->
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ 
            $businessPartner->partnership_status === 'active' ? 'bg-green-100 text-green-800' : 
            ($businessPartner->partnership_status === 'suspended' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') 
        }}">
            {{ ucfirst($businessPartner->partnership_status) }}
        </span>
        
        <!-- Action Buttons -->
        <button onclick="togglePartnerStatus({{ $businessPartner->id }})" 
                class="inline-flex items-center rounded-md px-3 py-2 text-sm font-semibold shadow-sm {{ 
                    $businessPartner->partnership_status === 'active' ? 'bg-yellow-600 text-white hover:bg-yellow-500' : 'bg-green-600 text-white hover:bg-green-500'
                }}">
            {{ $businessPartner->partnership_status === 'active' ? 'Deactivate' : 'Activate' }}
        </button>

        <a href="{{ route('admin.business-partners.edit', $businessPartner) }}" 
           class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z" />
            </svg>
            Edit Partner
        </a>
    </div>
</div>
@endsection

@section('content')
<!-- Statistics Overview -->
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    <!-- Total Inspections -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3-6v10a1 1 0 01-1 1H5a1 1 0 01-1-1V7a1 1 0 011-1h4.75l2.5-2.5H17a1 1 0 011 1v9.5z" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Inspections</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_inspections']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- This Month -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5a2.25 2.25 0 002.25-2.25m-18 0v-7.5A2.25 2.25 0 005.25 9h13.5a2.25 2.25 0 002.25 2.25v7.5" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">This Month</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['current_month_inspections']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Spent -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Spent</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_amount_spent']) }} RWF</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Average Monthly -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-purple-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Avg. Monthly</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['average_monthly_inspections'], 1) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-8">
        <!-- Company Information -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Company Information</h3>
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Company Name</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $businessPartner->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Business Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $businessPartner->getTypeDisplayName() }}</dd>
                    </div>
                    @if($businessPartner->registration_number)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Registration Number</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $businessPartner->registration_number }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Partnership Tier</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                $businessPartner->tier === 'platinum' ? 'bg-gray-100 text-gray-800' : 
                                ($businessPartner->tier === 'gold' ? 'bg-yellow-100 text-yellow-800' : 
                                ($businessPartner->tier === 'silver' ? 'bg-gray-100 text-gray-600' : 'bg-orange-100 text-orange-800')) 
                            }}">
                                {{ ucfirst($businessPartner->tier) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <a href="mailto:{{ $businessPartner->email }}" class="text-indigo-600 hover:text-indigo-500">
                                {{ $businessPartner->email }}
                            </a>
                        </dd>
                    </div>
                    @if($businessPartner->phone)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Phone</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <a href="tel:{{ $businessPartner->phone }}" class="text-indigo-600 hover:text-indigo-500">
                                {{ $businessPartner->phone }}
                            </a>
                        </dd>
                    </div>
                    @endif
                    @if($businessPartner->website)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Website</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <a href="{{ $businessPartner->website }}" target="_blank" class="text-indigo-600 hover:text-indigo-500">
                                {{ $businessPartner->website }}
                                <svg class="inline-block ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        </dd>
                    </div>
                    @endif
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Address</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $businessPartner->address }}<br>
                            {{ $businessPartner->city }}, {{ $businessPartner->country }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Primary Contact</h3>
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Contact Person</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $businessPartner->contact_person }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <a href="mailto:{{ $businessPartner->contact_email }}" class="text-indigo-600 hover:text-indigo-500">
                                {{ $businessPartner->contact_email }}
                            </a>
                        </dd>
                    </div>
                    @if($businessPartner->contact_phone)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Phone</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <a href="tel:{{ $businessPartner->contact_phone }}" class="text-indigo-600 hover:text-indigo-500">
                                {{ $businessPartner->contact_phone }}
                            </a>
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <!-- Partnership Terms -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Partnership Terms</h3>
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Discount Percentage</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ number_format($businessPartner->discount_percentage, 1) }}%</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Billing Cycle</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($businessPartner->billing_cycle) }}</dd>
                    </div>
                    @if($businessPartner->credit_limit)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Credit Limit</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ number_format($businessPartner->credit_limit) }} RWF</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Partnership Duration</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $stats['partnership_duration_months'] }} months</dd>
                    </div>
                    @if($businessPartner->contract_end_date)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Contract End Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $businessPartner->contract_end_date->format('M d, Y') }}</dd>
                    </div>
                    @endif
                    @if($businessPartner->notes)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Notes</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $businessPartner->notes }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <!-- Recent Inspection Requests -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Inspection Requests</h3>
                    <a href="{{ route('admin.inspection-requests.index', ['business_partner' => $businessPartner->id]) }}" 
                       class="text-sm text-indigo-600 hover:text-indigo-500">
                        View all →
                    </a>
                </div>
                @if($businessPartner->inspectionRequests->count() > 0)
                    <div class="flow-root">
                        <ul role="list" class="-mb-8">
                            @foreach($businessPartner->inspectionRequests->take(5) as $request)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full {{ 
                                                $request->status === 'completed' ? 'bg-green-500' : 
                                                ($request->status === 'in_progress' ? 'bg-blue-500' : 
                                                ($request->status === 'assigned' ? 'bg-yellow-500' : 'bg-gray-400')) 
                                            }} flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-900">
                                                    Request #{{ $request->request_number }} - 
                                                    <a href="{{ route('admin.inspection-requests.show', $request) }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                                                        {{ $request->property->address ?? 'Property inspection' }}
                                                    </a>
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    Status: {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                                    @if($request->assigned_inspector_id)
                                                        • Assigned to {{ $request->assignedInspector->user->full_name ?? 'Inspector' }}
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                {{ $request->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="text-center py-6">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">No inspection requests yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-8">
        <!-- Quick Actions -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.business-partners.users', $businessPartner->id) }}" 
                       class="w-full inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                        </svg>
                        Manage Users
                    </a>
                    
                    <a href="{{ route('admin.inspection-requests.create', ['business_partner' => $businessPartner->id]) }}" 
                       class="w-full inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        New Inspection Request
                    </a>

                    <button onclick="generateReport()" 
                            class="w-full inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Generate Report
                    </button>
                </div>
            </div>
        </div>

        <!-- Associated Users -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Associated Users</h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        {{ $businessPartner->users->count() }}
                    </span>
                </div>
                @if($businessPartner->users->count() > 0)
                    <ul class="divide-y divide-gray-200">
                        @foreach($businessPartner->users->take(5) as $user)
                        <li class="py-3 flex justify-between items-start">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-600">
                                            {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $user->full_name }}
                                    </p>
                                    <p class="text-sm text-gray-500 truncate">
                                        {{ ucfirst($user->pivot->access_level) }}
                                        @if($user->pivot->is_primary_contact)
                                            • Primary Contact
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('admin.users.show', $user) }}" 
                               class="text-indigo-600 hover:text-indigo-500 text-sm">
                                View
                            </a>
                        </li>
                        @endforeach
                    </ul>
                    @if($businessPartner->users->count() > 5)
                    <div class="mt-4">
                        <a href="{{ route('admin.business-partners.users', $businessPartner) }}" 
                           class="text-sm text-indigo-600 hover:text-indigo-500">
                            View all {{ $businessPartner->users->count() }} users →
                        </a>
                    </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">No users associated</p>
                        <p class="mt-1">
                            <a href="{{ route('admin.business-partners.users', $businessPartner) }}" class="text-indigo-600 hover:text-indigo-500 text-sm">
                                Add users
                            </a>
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Monthly Activity Chart -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Monthly Activity</h3>
                <div class="space-y-4">
                    @if($monthlyInspections->count() > 0)
                        <div class="space-y-2">
                            @foreach($monthlyInspections->take(6) as $month)
                                @php
                                    $maxCount = $monthlyInspections->max('count');
                                    $percentage = $maxCount > 0 ? ($month->count / $maxCount) * 100 : 0;
                                @endphp
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium text-gray-900 w-16">
                                            {{ DateTime::createFromFormat('!m', $month->month)->format('M') }} {{ $month->year }}
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-2 flex-1 ml-4">
                                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                                            <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <span class="text-sm text-gray-600 w-8 text-right">{{ $month->count }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No activity data</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Billing Information -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Billing Information</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Pending Amount</span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ number_format($stats['pending_billing_amount']) }} RWF
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Current Discount</span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ number_format($businessPartner->discount_percentage, 1) }}%
                        </span>
                    </div>
                    @if($businessPartner->credit_limit)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Credit Limit</span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ number_format($businessPartner->credit_limit) }} RWF
                        </span>
                    </div>
                    @endif
                    <div class="pt-4 border-t border-gray-200">
                        @if($businessPartner->billings->count() > 0)
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Recent Billing</h4>
                            <div class="space-y-2">
                                @foreach($businessPartner->billings->take(3) as $billing)
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500">{{ $billing->created_at->format('M d') }}</span>
                                    <span class="font-medium text-gray-900">{{ number_format($billing->amount) }} RWF</span>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">No billing records yet</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Partnership Status -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Partnership Status</h3>
                <div class="space-y-4">
                    <!-- Status -->
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Current Status</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                            $businessPartner->partnership_status === 'active' ? 'bg-green-100 text-green-800' : 
                            ($businessPartner->partnership_status === 'suspended' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') 
                        }}">
                            {{ ucfirst($businessPartner->partnership_status) }}
                        </span>
                    </div>

                    <!-- Contract End Date -->
                    @if($businessPartner->contract_end_date)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Contract Expires</span>
                        <span class="text-sm font-medium {{ $businessPartner->contract_end_date->isPast() ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $businessPartner->contract_end_date->format('M d, Y') }}
                            @if($businessPartner->contract_end_date->isPast())
                                (Expired)
                            @elseif($businessPartner->contract_end_date->diffInDays() <= 30)
                                ({{ $businessPartner->contract_end_date->diffInDays() }} days)
                            @endif
                        </span>
                    </div>
                    @endif

                    <!-- Partnership Duration -->
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Partnership Duration</span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ $stats['partnership_duration_months'] }} months
                        </span>
                    </div>

                    <!-- Performance Indicators -->
                    <div class="pt-4 border-t border-gray-200">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Performance</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500">Activity Level</span>
                                <span class="font-medium {{ 
                                    $stats['current_month_inspections'] > 10 ? 'text-green-600' : 
                                    ($stats['current_month_inspections'] > 5 ? 'text-yellow-600' : 'text-red-600') 
                                }}">
                                    @if($stats['current_month_inspections'] > 10)
                                        High
                                    @elseif($stats['current_month_inspections'] > 5)
                                        Medium
                                    @else
                                        Low
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500">Avg. Monthly</span>
                                <span class="font-medium text-gray-900">{{ number_format($stats['average_monthly_inspections'], 1) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function togglePartnerStatus(partnerId) {
    if (!confirm('Are you sure you want to change this partner\'s status?')) {
        return;
    }

    fetch(`/admin/business-partners/${partnerId}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the partner status.');
    });
}

function generateReport() {
    // You can implement report generation here
    alert('Report generation feature will be implemented soon!');
}

// Initialize any charts or interactive elements
document.addEventListener('DOMContentLoaded', function() {
    // Add any initialization code here if needed
});
</script>
@endpush

@endsection