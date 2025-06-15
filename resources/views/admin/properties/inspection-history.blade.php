{{-- File Path: resources/views/admin/properties/inspection-history.blade.php --}}

@extends('layouts.admin')

@section('title', 'Inspection History - ' . $property->property_code)

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Inspection History: {{ $property->property_code }}
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <a href="{{ route('admin.properties.index') }}" class="text-indigo-600 hover:text-indigo-500">Properties</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.properties.show', $property) }}" class="text-indigo-600 hover:text-indigo-500">{{ $property->property_code }}</a>
                <span class="mx-2">/</span>
                <span>Inspection History</span>
            </div>
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <svg class="mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                {{ $property->address }}
            </div>
        </div>
    </div>
    <div class="mt-4 flex space-x-3 md:ml-4 md:mt-0">
        <a href="{{ route('admin.properties.show', $property) }}" 
           class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 010-1.06L10.94 8.25H4a.75.75 0 010-1.5h6.94L7.72 3.53a.75.75 0 011.06-1.06l4.5 4.5a.75.75 0 010 1.06l-4.5 4.5a.75.75 0 01-1.06 0z" clip-rule="evenodd" />
            </svg>
            Back to Property
        </a>
        <a href="{{ route('admin.inspection-requests.create') }}?property_id={{ $property->id }}" 
           class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
            </svg>
            Schedule New Inspection
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Property Overview Card -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 rounded-lg flex items-center justify-center
                        {{ $property->property_type === 'residential' ? 'bg-blue-100' : 
                           ($property->property_type === 'commercial' ? 'bg-green-100' : 
                           ($property->property_type === 'industrial' ? 'bg-yellow-100' : 'bg-purple-100')) }}">
                        @if($property->property_type === 'residential')
                            <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        @elseif($property->property_type === 'commercial')
                            <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        @elseif($property->property_type === 'industrial')
                            <svg class="h-8 w-8 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2z" />
                            </svg>
                        @else
                            <svg class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" />
                            </svg>
                        @endif
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl class="space-y-1">
                        <dt class="text-sm font-medium text-gray-500">Property</dt>
                        <dd class="text-lg font-semibold text-gray-900">{{ $property->property_code }}</dd>
                    </dl>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl class="space-y-1">
                        <dt class="text-sm font-medium text-gray-500">Owner</dt>
                        <dd class="text-lg font-semibold text-gray-900">{{ $property->owner_name ?: 'Not specified' }}</dd>
                    </dl>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl class="space-y-1">
                        <dt class="text-sm font-medium text-gray-500">Type</dt>
                        <dd class="text-lg font-semibold text-gray-900">{{ $property->getTypeDisplayName() }}</dd>
                    </dl>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl class="space-y-1">
                        <dt class="text-sm font-medium text-gray-500">Total Inspections</dt>
                        <dd class="text-lg font-semibold text-gray-900">{{ $inspectionRequests->total() }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Inspection Timeline -->
    @if($inspectionRequests->count() > 0)
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Inspection Timeline</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Complete history of all inspections for this property.
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    <!-- Status Filter -->
                    <select onchange="filterByStatus(this.value)" class="text-sm border-gray-300 rounded-md">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Assigned</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="border-t border-gray-200">
            <div class="flow-root">
                <ul class="divide-y divide-gray-200">
                    @foreach($inspectionRequests as $inspection)
                    <li class="px-4 py-6 sm:px-6 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center min-w-0 flex-1">
                                <!-- Status Icon -->
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full flex items-center justify-center
                                        {{ $inspection->status === 'completed' ? 'bg-green-100' : 
                                           ($inspection->status === 'assigned' ? 'bg-blue-100' : 
                                           ($inspection->status === 'in_progress' ? 'bg-yellow-100' : 
                                           ($inspection->status === 'cancelled' ? 'bg-red-100' : 'bg-gray-100'))) }}">
                                        @if($inspection->status === 'completed')
                                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @elseif($inspection->status === 'assigned')
                                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        @elseif($inspection->status === 'in_progress')
                                            <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @elseif($inspection->status === 'cancelled')
                                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @else
                                            <svg class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @endif
                                    </div>
                                </div>

                                <!-- Inspection Details -->
                                <div class="ml-4 min-w-0 flex-1">
                                    <div class="flex items-center justify-between">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-indigo-600 truncate">
                                                {{ $inspection->request_number }}
                                            </p>
                                            <p class="text-sm text-gray-900 font-medium">
                                                {{ $inspection->package->name ?? 'Package Not Found' }}
                                            </p>
                                        </div>
                                        <div class="ml-2 flex-shrink-0 flex items-center space-x-2">
                                            <!-- Status Badge -->
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $inspection->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                   ($inspection->status === 'assigned' ? 'bg-blue-100 text-blue-800' : 
                                                   ($inspection->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 
                                                   ($inspection->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))) }}">
                                                {{ ucfirst(str_replace('_', ' ', $inspection->status)) }}
                                            </span>
                                            
                                            <!-- Priority Badge -->
                                            @if($inspection->urgency && $inspection->urgency !== 'normal')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    {{ $inspection->urgency === 'emergency' ? 'bg-red-100 text-red-800' : 'bg-orange-100 text-orange-800' }}">
                                                    {{ ucfirst($inspection->urgency) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="mt-2 flex items-center text-sm text-gray-500">
                                        <!-- Date Created -->
                                        <div class="flex items-center">
                                            <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <p>Requested {{ $inspection->created_at->format('M j, Y') }}</p>
                                        </div>
                                        
                                        <!-- Purpose -->
                                        <span class="mx-2">•</span>
                                        <p>{{ ucfirst($inspection->purpose) }}</p>
                                        
                                        <!-- Inspector (if assigned) -->
                                        @if($inspection->assignedInspector)
                                            <span class="mx-2">•</span>
                                            <div class="flex items-center">
                                                <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                <p>{{ $inspection->assignedInspector->user->full_name }}</p>
                                            </div>
                                        @endif
                                        
                                        <!-- Scheduled Date (if exists) -->
                                        @if($inspection->scheduled_date)
                                            <span class="mx-2">•</span>
                                            <div class="flex items-center">
                                                <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <p>Scheduled {{ \Carbon\Carbon::parse($inspection->scheduled_date)->format('M j, Y') }}
                                                    @if($inspection->scheduled_time)
                                                        at {{ \Carbon\Carbon::parse($inspection->scheduled_time)->format('g:i A') }}
                                                    @endif
                                                </p>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Additional Info -->
                                    @if($inspection->completed_at || $inspection->started_at)
                                    <div class="mt-2 flex items-center text-sm text-gray-500">
                                        @if($inspection->started_at)
                                            <div class="flex items-center">
                                                <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1M9 16h6m-7 4h8a2 2 0 002-2V6a2 2 0 00-2-2H8a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <p>Started {{ \Carbon\Carbon::parse($inspection->started_at)->format('M j, Y g:i A') }}</p>
                                            </div>
                                        @endif
                                        
                                        @if($inspection->completed_at)
                                            @if($inspection->started_at)
                                                <span class="mx-2">•</span>
                                            @endif
                                            <div class="flex items-center">
                                                <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <p>Completed {{ \Carbon\Carbon::parse($inspection->completed_at)->format('M j, Y g:i A') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="ml-4 flex-shrink-0 flex items-center space-x-2">
                                <a href="{{ route('admin.inspection-requests.show', $inspection) }}" 
                                   class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                    View Details
                                </a>
                                
                                @if($inspection->status === 'pending')
                                    <a href="{{ route('admin.inspection-requests.assign') }}#request-{{ $inspection->id }}" 
                                       class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-indigo-600 hover:bg-indigo-700">
                                        Assign Inspector
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Special Instructions (if any) -->
                        @if($inspection->special_instructions)
                        <div class="mt-3 ml-14">
                            <div class="bg-blue-50 rounded-md p-3">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-medium text-blue-800">Special Instructions</h4>
                                        <p class="mt-1 text-sm text-blue-700">{{ $inspection->special_instructions }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Pagination -->
        @if($inspectionRequests->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $inspectionRequests->links() }}
        </div>
        @endif
    </div>
    @else
    <!-- Empty State -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-8 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No Inspections Yet</h3>
            <p class="mt-2 text-sm text-gray-500">
                This property has not been inspected yet. Schedule the first inspection to get started.
            </p>
            <div class="mt-6">
                <a href="{{ route('admin.inspection-requests.create') }}?property_id={{ $property->id }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Schedule First Inspection
                </a>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
function filterByStatus(status) {
    const url = new URL(window.location);
    if (status) {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    window.location = url;
}

// Auto-refresh page every 30 seconds to show real-time updates
setInterval(function() {
    // Only refresh if there are pending or in-progress inspections
    const hasPendingInspections = document.querySelector('.bg-yellow-100, .bg-blue-100');
    if (hasPendingInspections) {
        window.location.reload();
    }
}, 30000);
</script>
@endpush

@endsection