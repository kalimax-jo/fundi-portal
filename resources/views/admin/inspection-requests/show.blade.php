{{-- File Path: resources/views/admin/inspection-requests/show.blade.php --}}

@extends('layouts.admin')

@section('title', 'Inspection Request - ' . $inspectionRequest->request_number)

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            {{ $inspectionRequest->request_number }}
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <a href="{{ route('admin.inspection-requests.index') }}" class="text-indigo-600 hover:text-indigo-500">Inspection Requests</a>
                <span class="mx-2">/</span>
                <span>{{ $inspectionRequest->request_number }}</span>
            </div>
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <svg class="mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                </svg>
                Created {{ $inspectionRequest->created_at->format('M j, Y \a\t g:i A') }}
            </div>
            @if($inspectionRequest->scheduled_date)
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <svg class="mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                </svg>
                Scheduled {{ \Carbon\Carbon::parse($inspectionRequest->scheduled_date)->format('M j, Y') }}
                @if($inspectionRequest->scheduled_time)
                at {{ \Carbon\Carbon::parse($inspectionRequest->scheduled_time)->format('g:i A') }}
                @endif
            </div>
            @endif
        </div>
    </div>
    <div class="mt-4 flex space-x-3 md:ml-4 md:mt-0">
        <a href="{{ route('admin.inspection-requests.edit', $inspectionRequest) }}" 
           class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z" />
            </svg>
            Edit Request
        </a>
        
        @if($inspectionRequest->status === 'pending' && $availableInspectors->count() > 0)
        <button onclick="showAssignModal()" 
                class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 9a3 3 0 100-6 3 3 0 000 6zM6 8a2 2 0 11-4 0 2 2 0 014 0zM1.49 15.326a.78.78 0 01-.358-.442 3 3 0 014.308-3.516 6.484 6.484 0 00-1.905 3.959c-.023.222-.014.442.025.654a4.97 4.97 0 01-2.07-.655z" />
            </svg>
            Assign Inspector
        </button>
        @endif

        <!-- Status Update Dropdown -->
        <div class="relative inline-block text-left" x-data="{ open: false }">
            <button @click="open = !open" type="button" 
                    class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500">
                Update Status
                <svg class="ml-1 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                </svg>
            </button>

            <div x-show="open" @click.away="open = false" x-transition
                 class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5">
                <div class="py-1">
                    @if($inspectionRequest->status !== 'pending')
                    <button onclick="updateStatus('pending')" 
                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Mark as Pending
                    </button>
                    @endif
                    @if($inspectionRequest->status !== 'assigned')
                    <button onclick="updateStatus('assigned')" 
                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Mark as Assigned
                    </button>
                    @endif
                    @if($inspectionRequest->status !== 'in_progress')
                    <button onclick="updateStatus('in_progress')" 
                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Mark as In Progress
                    </button>
                    @endif
                    @if($inspectionRequest->status !== 'completed')
                    <button onclick="updateStatus('completed')" 
                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Mark as Completed
                    </button>
                    @endif
                    @if($inspectionRequest->status !== 'cancelled')
                    <button onclick="updateStatus('cancelled')" 
                            class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                        Cancel Request
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Status Banner -->
    <div class="rounded-md p-4 
        {{ $inspectionRequest->status === 'pending' ? 'bg-yellow-50 border border-yellow-200' : 
           ($inspectionRequest->status === 'assigned' ? 'bg-blue-50 border border-blue-200' : 
           ($inspectionRequest->status === 'in_progress' ? 'bg-indigo-50 border border-indigo-200' : 
           ($inspectionRequest->status === 'completed' ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-200'))) }}">
        <div class="flex">
            <div class="flex-shrink-0">
                @if($inspectionRequest->status === 'pending')
                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L8.28 10.5a.75.75 0 00-1.06 1.061l1.5 1.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                </svg>
                @elseif($inspectionRequest->status === 'assigned')
                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zM6 8a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                @elseif($inspectionRequest->status === 'in_progress')
                <svg class="h-5 w-5 text-indigo-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9.5 9.293 10.793a1 1 0 101.414 1.414l2-2a1 1 0 000-1.414z" clip-rule="evenodd" />
                </svg>
                @elseif($inspectionRequest->status === 'completed')
                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                @else
                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                </svg>
                @endif
            </div>
            <div class="ml-3 flex-1">
                <h3 class="text-sm font-medium 
                    {{ $inspectionRequest->status === 'pending' ? 'text-yellow-800' : 
                       ($inspectionRequest->status === 'assigned' ? 'text-blue-800' : 
                       ($inspectionRequest->status === 'in_progress' ? 'text-indigo-800' : 
                       ($inspectionRequest->status === 'completed' ? 'text-green-800' : 'text-gray-800'))) }}">
                    Status: {{ ucfirst(str_replace('_', ' ', $inspectionRequest->status)) }}
                    @if($inspectionRequest->urgency !== 'normal')
                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $inspectionRequest->urgency === 'emergency' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ ucfirst($inspectionRequest->urgency) }}
                    </span>
                    @endif
                </h3>
                <div class="mt-1 text-sm 
                    {{ $inspectionRequest->status === 'pending' ? 'text-yellow-700' : 
                       ($inspectionRequest->status === 'assigned' ? 'text-blue-700' : 
                       ($inspectionRequest->status === 'in_progress' ? 'text-indigo-700' : 
                       ($inspectionRequest->status === 'completed' ? 'text-green-700' : 'text-gray-700'))) }}">
                    @if($inspectionRequest->status === 'pending')
                        This inspection request is waiting to be assigned to an inspector.
                    @elseif($inspectionRequest->status === 'assigned')
                        This inspection has been assigned to {{ $inspectionRequest->assignedInspector->user->full_name }}.
                    @elseif($inspectionRequest->status === 'in_progress')
                        Inspection is currently in progress by {{ $inspectionRequest->assignedInspector->user->full_name }}.
                    @elseif($inspectionRequest->status === 'completed')
                        Inspection has been completed on {{ $inspectionRequest->completed_at->format('M j, Y \a\t g:i A') }}.
                    @else
                        This inspection request has been cancelled.
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Request Details -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Request Details</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Basic information about this inspection request.</p>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Request Number</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $inspectionRequest->request_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Purpose</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $inspectionRequest->purpose)) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Package</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $inspectionRequest->package->display_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Cost</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ number_format($inspectionRequest->total_cost, 0) }} RWF</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Preferred Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $inspectionRequest->preferred_date ? \Carbon\Carbon::parse($inspectionRequest->preferred_date)->format('M j, Y') : 'Not specified' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Time Preference</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($inspectionRequest->preferred_time_slot) }}</dd>
                        </div>
                        @if($inspectionRequest->special_instructions)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Special Instructions</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $inspectionRequest->special_instructions }}</dd>
                        </div>
                        @endif
                        @if($inspectionRequest->loan_amount)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Loan Amount</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ number_format($inspectionRequest->loan_amount, 0) }} RWF</dd>
                        </div>
                        @endif
                        @if($inspectionRequest->loan_reference)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Loan Reference</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $inspectionRequest->loan_reference }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
            <!-- Property Details -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Property Information</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Details about the property to be inspected.</p>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Property Code</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $inspectionRequest->property->property_code }}</dd>
                        </div>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Contact Person</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $inspectionRequest->businessPartner->contact_person }}</dd>
                        </div>
                        @if($inspectionRequest->businessPartner->contact_email)
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $inspectionRequest->businessPartner->contact_email }}</dd>
                        </div>
                        @endif
                        @if($inspectionRequest->businessPartner->discount_percentage > 0)
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Discount Applied</dt>
                            <dd class="mt-1 text-sm text-green-600 font-semibold">{{ $inspectionRequest->businessPartner->discount_percentage }}%</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
            @endif

            <!-- Package Details -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Package Details</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Inspection package information.</p>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Package Name</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">{{ $inspectionRequest->package->display_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Price</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ number_format($inspectionRequest->package->price, 0) }} RWF</dd>
                        </div>
                        @if($inspectionRequest->package->duration_hours)
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Duration</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $inspectionRequest->package->duration_hours }} hours</dd>
                        </div>
                        @endif
                        @if($inspectionRequest->package->description)
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $inspectionRequest->package->description }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Payment Status -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Payment Status</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Payment information for this request.</p>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm font-medium text-gray-500">Status</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $inspectionRequest->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 
                               ($inspectionRequest->payment_status === 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst($inspectionRequest->payment_status) }}
                        </span>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Total Amount</span>
                            <span class="text-sm font-medium text-gray-900">{{ number_format($inspectionRequest->total_cost, 0) }} RWF</span>
                        </div>
                        @if($inspectionRequest->payments->count() > 0)
                        <div class="border-t pt-3">
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Payment History</h4>
                            @foreach($inspectionRequest->payments as $payment)
                            <div class="flex justify-between items-center py-2">
                                <div>
                                    <p class="text-xs text-gray-900">{{ $payment->created_at->format('M j, Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ $payment->payment_method }}</p>
                                </div>
                                <span class="text-xs font-medium text-green-600">+{{ number_format($payment->amount, 0) }} RWF</span>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Quick Actions</h3>
                </div>
                <div class="border-t border-gray-200">
                    <ul class="divide-y divide-gray-200">
                        <li>
                            <a href="{{ route('admin.properties.show', $inspectionRequest->property) }}" 
                               class="block px-4 py-4 hover:bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-indigo-600">View Property Details</p>
                                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </a>
                        </li>
                        @if($inspectionRequest->assignedInspector)
                        <li>
                            <a href="{{ route('admin.inspectors.show', $inspectionRequest->assignedInspector) }}" 
                               class="block px-4 py-4 hover:bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-indigo-600">View Inspector Profile</p>
                                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </a>
                        </li>
                        @endif
                        @if($inspectionRequest->businessPartner)
                        <li>
                            <a href="{{ route('admin.business-partners.show', $inspectionRequest->businessPartner) }}" 
                               class="block px-4 py-4 hover:bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-indigo-600">View Business Partner</p>
                                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </a>
                        </li>
                        @endif
                        <li>
                            <button onclick="printRequest()" 
                                    class="w-full text-left px-4 py-4 hover:bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-600">Print Request Details</p>
                                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Inspector Modal -->
@if($inspectionRequest->status === 'pending' && $availableInspectors->count() > 0)
<div id="assignModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg max-w-md w-full mx-auto shadow-xl">
        <form id="assignForm" method="POST" action="{{ route('admin.inspection-requests.assign-inspector', $inspectionRequest) }}">
            @csrf
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Assign Inspector</h3>
                
                <!-- Inspector Selection -->
                <div class="mb-4">
                    <label for="inspector_id" class="block text-sm font-medium text-gray-700">Select Inspector</label>
                    <select name="inspector_id" id="inspector_id" required
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Choose an inspector...</option>
                        @foreach($availableInspectors as $inspector)
                        <option value="{{ $inspector->id }}">
                            {{ $inspector->user->full_name }} ({{ $inspector->certification_level }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Scheduled Date -->
                <div class="mb-4">
                    <label for="scheduled_date" class="block text-sm font-medium text-gray-700">Scheduled Date</label>
                    <input type="date" name="scheduled_date" id="scheduled_date" required
                           min="{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}"
                           value="{{ $inspectionRequest->preferred_date }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <!-- Scheduled Time -->
                <div class="mb-4">
                    <label for="scheduled_time" class="block text-sm font-medium text-gray-700">Scheduled Time</label>
                    <input type="time" name="scheduled_time" id="scheduled_time" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <!-- Notes -->
                <div class="mb-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                    <textarea name="notes" id="notes" rows="3"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                              placeholder="Any special instructions for the inspector..."></textarea>
                </div>
            </div>
            
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="submit" 
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Assign Inspector
                </button>
                <button type="button" onclick="hideAssignModal()" 
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@push('scripts')
<script>
// Modal functions
function showAssignModal() {
    document.getElementById('assignModal').classList.remove('hidden');
}

function hideAssignModal() {
    document.getElementById('assignModal').classList.add('hidden');
}

// Status update function
function updateStatus(newStatus) {
    if (!confirm(`Are you sure you want to change the status to "${newStatus.replace('_', ' ')}"?`)) {
        return;
    }

    fetch(`{{ route('admin.inspection-requests.update-status', $inspectionRequest) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            status: newStatus
        })
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
        alert('An error occurred while updating the status.');
    });
}

// Print function
function printRequest() {
    window.print();
}

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideAssignModal();
    }
});

// Set default time based on preferred time slot
document.addEventListener('DOMContentLoaded', function() {
    const timeSlot = '{{ $inspectionRequest->preferred_time_slot }}';
    const timeInput = document.getElementById('scheduled_time');
    
    if (timeInput) {
        switch(timeSlot) {
            case 'morning':
                timeInput.value = '09:00';
                break;
            case 'afternoon':
                timeInput.value = '14:00';
                break;
            case 'evening':
                timeInput.value = '17:00';
                break;
            default:
                timeInput.value = '10:00';
        }
    }
});
</script>
@endpush

@endsection
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Property Type</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($inspectionRequest->property->property_type) }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Address</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $inspectionRequest->property->address }}</dd>
                        </div>
                        @if($inspectionRequest->property->owner_name)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Owner</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $inspectionRequest->property->owner_name }}</dd>
                        </div>
                        @endif
                        @if($inspectionRequest->property->owner_phone)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Owner Phone</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $inspectionRequest->property->owner_phone }}</dd>
                        </div>
                        @endif
                        @if($inspectionRequest->property->total_area_sqm)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Area</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ number_format($inspectionRequest->property->total_area_sqm, 0) }} sqm</dd>
                        </div>
                        @endif
                        @if($inspectionRequest->property->built_year)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Built Year</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $inspectionRequest->property->built_year }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            @if($inspectionRequest->assignedInspector)
            <!-- Inspector Details -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Assigned Inspector</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Information about the assigned inspector.</p>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                    <div class="flex items-center">
                        <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                            <span class="text-lg font-medium text-indigo-800">
                                {{ strtoupper(substr($inspectionRequest->assignedInspector->user->first_name, 0, 1) . substr($inspectionRequest->assignedInspector->user->last_name, 0, 1)) }}
                            </span>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-medium text-gray-900">{{ $inspectionRequest->assignedInspector->user->full_name }}</h4>
                            <p class="text-sm text-gray-500">{{ $inspectionRequest->assignedInspector->inspector_code }}</p>
                        </div>
                        <div class="ml-auto">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $inspectionRequest->assignedInspector->availability_status === 'available' ? 'bg-green-100 text-green-800' : 
                                   ($inspectionRequest->assignedInspector->availability_status === 'busy' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($inspectionRequest->assignedInspector->availability_status) }}
                            </span>
                        </div>
                    </div>
                    <dl class="mt-6 grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $inspectionRequest->assignedInspector->user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Phone</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $inspectionRequest->assignedInspector->user->phone }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Certification Level</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($inspectionRequest->assignedInspector->certification_level) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Experience</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $inspectionRequest->assignedInspector->experience_years }} years</dd>
                        </div>
                        @if($inspectionRequest->assignedInspector->specializations)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Specializations</dt>
                            <dd class="mt-1">
                                @foreach($inspectionRequest->assignedInspector->specializations as $specialization)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2 mb-1">
                                    {{ ucfirst(str_replace('_', ' ', $specialization)) }}
                                </span>
                                @endforeach
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Sidebar -->
        <div class="space-y-6">
            <!-- Requester Information -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Requester</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Who requested this inspection.</p>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                    <div class="flex items-center mb-4">
                        <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                            <span class="text-sm font-medium text-gray-700">
                                {{ strtoupper(substr($inspectionRequest->requester->first_name, 0, 1) . substr($inspectionRequest->requester->last_name, 0, 1)) }}
                            </span>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">{{ $inspectionRequest->requester->full_name }}</p>
                            <p class="text-sm text-gray-500">{{ ucfirst($inspectionRequest->requester_type) }} Client</p>
                        </div>
                    </div>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $inspectionRequest->requester->email }}</dd>
                        </div>
                        @if($inspectionRequest->requester->phone)
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Phone</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $inspectionRequest->requester->phone }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            @if($inspectionRequest->businessPartner)
            <!-- Business Partner Information -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Business Partner</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Partner organization details.</p>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                    <div class="flex items-center mb-4">
                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                            <span class="text-sm font-medium text-indigo-800">
                                {{ strtoupper(substr($inspectionRequest->businessPartner->name, 0, 2)) }}
                            </span>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">{{ $inspectionRequest->businessPartner->name }}</p>
                            <p class="text-sm text-gray-500">{{ ucfirst($inspectionRequest->businessPartner->type) }}</p>
                        </div>
                    </div>