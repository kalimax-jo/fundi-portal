{{-- File Path: resources/views/admin/inspection-requests/pending.blade.php --}}

@extends('layouts.admin')

@section('title', 'Pending Inspection Requests')

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Pending Inspection Requests
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <a href="{{ route('admin.inspection-requests.index') }}" class="text-indigo-600 hover:text-indigo-500">All Requests</a>
                <span class="mx-2">/</span>
                <span>Pending</span>
            </div>
            <div class="mt-2 flex items-center text-sm text-yellow-600">
                <svg class="mr-1.5 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L8.28 10.5a.75.75 0 00-1.06 1.061l1.5 1.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                </svg>
                {{ $requests->total() }} pending requests
            </div>
        </div>
    </div>
    <div class="mt-4 flex space-x-3 md:ml-4 md:mt-0">
        <a href="{{ route('admin.inspection-requests.index') }}" 
           class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 010-1.06L10.94 8.25H4a.75.75 0 010-1.5h6.94L7.72 3.53a.75.75 0 011.06-1.06l4.5 4.5a.75.75 0 010 1.06l-4.5 4.5a.75.75 0 01-1.06 0z" clip-rule="evenodd" />
            </svg>
            View All Requests
        </a>
        <a href="{{ route('admin.inspection-requests.assign') }}" 
           class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 9a3 3 0 100-6 3 3 0 000 6zM6 8a2 2 0 11-4 0 2 2 0 014 0zM1.49 15.326a.78.78 0 01-.358-.442 3 3 0 014.308-3.516 6.484 6.484 0 00-1.905 3.959c-.023.222-.014.442.025.654a4.97 4.97 0 01-2.07-.655z" />
            </svg>
            Assign Inspectors
        </a>
        <a href="{{ route('admin.inspection-requests.create') }}" 
           class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
            </svg>
            New Request
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Quick Actions Bar -->
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">
                    {{ $requests->total() }} requests waiting for assignment
                </h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p>These requests are sorted by urgency (Emergency → Urgent → Normal) and creation date. Assign inspectors to get them moving!</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form method="GET" action="{{ route('admin.inspection-requests.pending') }}" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <!-- Search -->
                    <div class="sm:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                        <div class="mt-1 relative">
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                   placeholder="Request number, requester name, property address..."
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-end space-x-3">
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Search
                        </button>
                        @if(request('search'))
                        <a href="{{ route('admin.inspection-requests.pending') }}" 
                           class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Clear
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Pending Requests List -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        @if($requests->count() > 0)
        <ul role="list" class="divide-y divide-gray-200">
            @foreach($requests as $request)
            <li class="hover:bg-gray-50 transition-colors duration-200">
                <div class="px-4 py-6 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center min-w-0 flex-1">
                            <!-- Urgency Indicator -->
                            <div class="flex-shrink-0 mr-4">
                                <div class="h-12 w-12 rounded-full flex items-center justify-center
                                    {{ $request->urgency === 'emergency' ? 'bg-red-100' : 
                                       ($request->urgency === 'urgent' ? 'bg-yellow-100' : 'bg-gray-100') }}">
                                    @if($request->urgency === 'emergency')
                                        <svg class="h-6 w-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    @elseif($request->urgency === 'urgent')
                                        <svg class="h-6 w-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                        </svg>
                                    @else
                                        <svg class="h-6 w-6 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                </div>
                            </div>

                            <!-- Request Details -->
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center">
                                    <p class="text-lg font-semibold text-indigo-600">
                                        {{ $request->request_number }}
                                    </p>
                                    <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $request->urgency === 'emergency' ? 'bg-red-100 text-red-800' : 
                                           ($request->urgency === 'urgent' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ ucfirst($request->urgency) }}
                                    </span>
                                    @if($request->preferred_date && $request->preferred_date->isToday())
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Today Preferred
                                    </span>
                                    @endif
                                </div>
                                
                                <div class="mt-2">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $request->requester->full_name }}
                                        @if($request->businessPartner)
                                        <span class="text-indigo-600">via {{ $request->businessPartner->name }}</span>
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <span class="font-medium">Property:</span> {{ $request->property->address }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        <span class="font-medium">Package:</span> {{ $request->package->display_name }}
                                        <span class="text-gray-500">• {{ $request->package->duration_hours }}h duration</span>
                                    </p>
                                </div>

                                <!-- Additional Info -->
                                <div class="mt-3 flex items-center space-x-4 text-sm text-gray-500">
                                    <div class="flex items-center">
                                        <svg class="mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                        </svg>
                                        Created {{ $request->created_at->diffForHumans() }}
                                    </div>
                                    @if($request->preferred_date)
                                    <div class="flex items-center">
                                        <svg class="mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                        </svg>
                                        Preferred: {{ $request->preferred_date->format('M j, Y') }} ({{ ucfirst($request->preferred_time_slot) }})
                                    </div>
                                    @endif
                                    <div class="flex items-center">
                                        <svg class="mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM12 15a1 1 0 101-1 1 1 0 00-1 1zm-4-1a1 1 0 11-2 0 1 1 0 012 0z" />
                                        </svg>
                                        {{ number_format($request->total_cost, 0) }} RWF
                                    </div>
                                </div>

                                @if($request->special_instructions)
                                <div class="mt-3">
                                    <p class="text-sm text-gray-700">
                                        <span class="font-medium">Instructions:</span> 
                                        {{ Str::limit($request->special_instructions, 120) }}
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col items-end space-y-2 ml-4">
                            <a href="{{ route('admin.inspection-requests.show', $request) }}" 
                               class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                View Details
                            </a>
                            
                            <button onclick="quickAssign({{ $request->id }})" 
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zM6 8a2 2 0 11-4 0 2 2 0 014 0zM1.49 15.326a.78.78 0 01-.358-.442 3 3 0 014.308-3.516 6.484 6.484 0 00-1.905 3.959c-.023.222-.014.442.025.654a4.97 4.97 0 01-2.07-.655z" />
                                </svg>
                                Quick Assign
                            </button>
                        </div>
                    </div>
                </div>
            </li>
            @endforeach
        </ul>

        <!-- Pagination -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $requests->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No pending requests!</h3>
            <p class="mt-1 text-sm text-gray-500">
                All inspection requests have been assigned. Great work!
            </p>
            <div class="mt-6">
                <a href="{{ route('admin.inspection-requests.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    View All Requests
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Quick Assign Modal (Simple version) -->
<div id="quickAssignModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg max-w-md w-full mx-auto shadow-xl">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Quick Assignment</h3>
            <p class="text-sm text-gray-600 mb-4">This will redirect you to the full assignment page where you can select an inspector and schedule the inspection.</p>
            <div class="flex justify-end space-x-3">
                <button onclick="hideQuickAssignModal()" 
                        class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </button>
                <a id="assignLink" href="#" 
                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Go to Assignment
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function quickAssign(requestId) {
    document.getElementById('assignLink').href = '{{ route("admin.inspection-requests.assign") }}#request-' + requestId;
    document.getElementById('quickAssignModal').classList.remove('hidden');
}

function hideQuickAssignModal() {
    document.getElementById('quickAssignModal').classList.add('hidden');
}

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideQuickAssignModal();
    }
});
</script>
@endpush

@endsection