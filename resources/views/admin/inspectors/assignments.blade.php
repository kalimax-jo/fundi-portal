@extends('layouts.admin')

@section('title', 'Inspector Assignments')

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Inspector Assignments Overview
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                Real-time view of inspector workloads and assignments
            </div>
        </div>
    </div>
    <div class="mt-4 flex space-x-3 md:ml-4 md:mt-0">
        <a href="{{ route('admin.inspectors.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 010-1.06L10.94 8.25H4a.75.75 0 010-1.5h6.94L7.72 3.53a.75.75 0 011.06-1.06l4.5 4.5a.75.75 0 010 1.06l-4.5 4.5a.75.75 0 01-1.06 0z" clip-rule="evenodd" />
            </svg>
            Back to Inspectors
        </a>
        <button type="button" onclick="refreshAssignments()" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
            </svg>
            Refresh
        </button>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Inspectors -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Inspectors</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $inspectors->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Now -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Available Now</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $inspectors->where('availability_status', 'available')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Currently Busy -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Currently Busy</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $inspectors->where('availability_status', 'busy')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Assignments Today -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Assignments Today</dt>
                            <dd class="text-lg font-medium text-gray-900">0</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <span class="text-gray-500">Will show when inspection requests are implemented</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Inspector Grid -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-6">Inspector Status Overview</h3>
            
            @if($inspectors->count() > 0)
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($inspectors as $inspector)
                        <div class="bg-gray-50 rounded-lg p-6 hover:bg-gray-100 transition-colors duration-200">
                            <!-- Inspector Header -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    @if($inspector->user->profile_photo)
                                        <img class="h-12 w-12 rounded-full" src="{{ Storage::url($inspector->user->profile_photo) }}" alt="{{ $inspector->user->full_name }}">
                                    @else
                                        <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $inspector->user->full_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $inspector->inspector_code }}</p>
                                    </div>
                                </div>
                                
                                <!-- Availability Status -->
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $inspector->availability_status === 'available' ? 'bg-green-100 text-green-800' : 
                                       ($inspector->availability_status === 'busy' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 {{ $inspector->availability_status === 'available' ? 'text-green-400' : 
                                       ($inspector->availability_status === 'busy' ? 'text-yellow-400' : 'text-red-400') }}" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    {{ ucfirst($inspector->availability_status) }}
                                </span>
                            </div>

                            <!-- Inspector Stats -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="text-center">
                                    <div class="text-lg font-bold text-gray-900">{{ number_format($inspector->rating, 1) }}/5</div>
                                    <div class="text-xs text-gray-500">Rating</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-lg font-bold text-gray-900">{{ $inspector->total_inspections }}</div>
                                    <div class="text-xs text-gray-500">Total Jobs</div>
                                </div>
                            </div>

                            <!-- Current Assignments (Placeholder) -->
                            <div class="border-t border-gray-200 pt-4">
                                <div class="text-xs font-medium text-gray-500 mb-2">TODAY'S ASSIGNMENTS</div>
                                <div class="text-center py-3">
                                    <div class="text-sm text-gray-400">No assignments</div>
                                    <div class="text-xs text-gray-400">Available for new requests</div>
                                </div>
                            </div>

                            <!-- Specializations -->
                            @if($inspector->specializations && count($inspector->specializations) > 0)
                                <div class="border-t border-gray-200 pt-4">
                                    <div class="text-xs font-medium text-gray-500 mb-2">SPECIALIZATIONS</div>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach(array_slice($inspector->specializations, 0, 3) as $specialization)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                                {{ ucfirst(str_replace('_', ' ', $specialization)) }}
                                            </span>
                                        @endforeach
                                        @if(count($inspector->specializations) > 3)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                +{{ count($inspector->specializations) - 3 }} more
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="border-t border-gray-200 pt-4 flex space-x-2">
                                <a href="{{ route('admin.inspectors.show', $inspector) }}" 
                                   class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                    View Details
                                </a>
                                <button onclick="toggleAvailability({{ $inspector->id }})" 
                                        class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-transparent text-xs font-medium rounded text-white 
                                        {{ $inspector->availability_status === 'available' ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }}">
                                    {{ $inspector->availability_status === 'available' ? 'Set Busy' : 'Set Available' }}
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No active inspectors</h3>
                    <p class="mt-1 text-sm text-gray-500">All inspectors are currently offline or there are no inspectors in the system.</p>
                    <div class="mt-6">
                        <a href="{{ route('admin.inspectors.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Add New Inspector
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Future Implementation Notice -->
    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Enhanced Features Coming Soon</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>Once inspection requests are implemented, this page will show:</p>
                    <ul class="mt-1 list-disc list-inside space-y-1">
                        <li>Real-time assignment tracking</li>
                        <li>Inspector workload distribution</li>
                        <li>Location-based assignment optimization</li>
                        <li>Performance analytics and metrics</li>
                        <li>Automated assignment suggestions</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function toggleAvailability(inspectorId) {
    if (!confirm('Are you sure you want to change this inspector\'s availability status?')) {
        return;
    }

    fetch(`/admin/inspectors/${inspectorId}/toggle-availability`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to update availability status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating availability status');
    });
}

function refreshAssignments() {
    location.reload();
}

// Auto-refresh every 60 seconds
setInterval(function() {
    console.log('Auto-refreshing assignments...');
    location.reload();
}, 60000);
</script>
@endpush
@endsection