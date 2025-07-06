@extends('layouts.headtech')

@section('title', 'My Activity')

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            My Activity
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <svg class="mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M2 3.5A1.5 1.5 0 013.5 2h1.148a1.5 1.5 0 011.465 1.175l.716 3.223a1.5 1.5 0 01-1.052 1.767l-.933.267c-.41.117-.643.555-.48.95a11.542 11.542 0 006.254 6.254c.395.163.833-.07.95-.48l.267-.933a1.5 1.5 0 011.767-1.052l3.223.716A1.5 1.5 0 0118 15.352V16.5A1.5 1.5 0 0116.5 18h-13A1.5 1.5 0 012 16.5v-13z" clip-rule="evenodd" />
                </svg>
                Oversee and manage all inspection requests
            </div>
        </div>
    </div>
    <div class="mt-4 flex md:ml-4 md:mt-0">
        <a href="{{ route('headtech.inspection-requests.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
            </svg>
            New Request
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">My Activity</h1>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded shadow p-4 flex items-center">
            <svg class="w-6 h-6 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            <div>
                <div class="text-xs text-gray-500">Total Requests</div>
                <div class="text-lg font-bold text-gray-900">{{ $stats['total_requests'] ?? 0 }}</div>
            </div>
        </div>
        <div class="bg-white rounded shadow p-4 flex items-center">
            <svg class="w-6 h-6 text-yellow-400 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L8.28 10.5a.75.75 0 00-1.06 1.061l1.5 1.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
            <div>
                <div class="text-xs text-gray-500">Pending</div>
                <div class="text-lg font-bold text-yellow-600">{{ $stats['pending_requests'] ?? 0 }}</div>
            </div>
        </div>
        <div class="bg-white rounded shadow p-4 flex items-center">
            <svg class="w-6 h-6 text-blue-400 mr-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zM6 8a2 2 0 11-4 0 2 2 0 014 0zM1.49 15.326a.78.78 0 01-.358-.442 3 3 0 014.308-3.516 6.484 6.484 0 00-1.905 3.959c-.023.222-.014.442.025.654a4.97 4.97 0 01-2.07-.655z" /></svg>
            <div>
                <div class="text-xs text-gray-500">Assigned</div>
                <div class="text-lg font-bold text-blue-600">{{ $stats['assigned_requests'] ?? 0 }}</div>
            </div>
        </div>
        <div class="bg-white rounded shadow p-4 flex items-center">
            <svg class="w-6 h-6 text-indigo-400 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9.5 9.293 10.793a1 1 0 101.414 1.414l2-2a1 1 0 000-1.414z" clip-rule="evenodd" /></svg>
            <div>
                <div class="text-xs text-gray-500">In Progress</div>
                <div class="text-lg font-bold text-indigo-600">{{ $stats['in_progress_requests'] ?? 0 }}</div>
            </div>
        </div>
        <div class="bg-white rounded shadow p-4 flex items-center">
            <svg class="w-6 h-6 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
            <div>
                <div class="text-xs text-gray-500">Completed</div>
                <div class="text-lg font-bold text-green-600">{{ $stats['completed_requests'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    {{-- Recent Activity Section --}}
    @if(isset($recentActivity) && $recentActivity->count() > 0)
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-semibold mb-4">Recent Activity</h3>
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @foreach($recentActivity as $activity)
                    <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex-shrink-0 text-xl">
                            {{ $activity->getActivityIcon() }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm {{ $activity->getActivityColorClass() }} font-medium">
                                {{ $activity->getActivityDescription() }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $activity->getTimeElapsed() }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Activity Table --}}
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <div class="px-4 py-5 sm:p-6">
                @if(session('success'))
                    <div class="rounded-md bg-green-50 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.06 0l4-5.5z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
                @if(session('error'))
                    <div class="rounded-md bg-red-50 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
                @if($errors->any())
                    <div class="rounded-md bg-red-50 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <ul class="text-sm text-red-800 list-disc pl-5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                @if($activities->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client & Package</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status & Urgency</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned Inspector</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($activities as $request)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($request->property)
                                            <div class="text-sm font-medium text-gray-900">{{ $request->property->address }}</div>
                                            <div class="text-sm text-gray-500">{{ $request->property->getFullLocationAttribute() }}</div>
            @else
                                            <div class="text-sm text-red-500">Property details missing</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $request->requester->full_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $request->package->display_name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $request->status_color }}">
                                            {{ $request->status_text }}
                                        </span>
                                        @if($request->urgency !== 'normal')
                                            <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 capitalize">
                                                {{ $request->urgency }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div>Requested: {{ $request->created_at->format('M d, Y') }}</div>
                                        @if($request->scheduled_date)
                                            <div>Scheduled: {{ \Carbon\Carbon::parse($request->scheduled_date)->format('M d, Y') }}</div>
            @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($request->inspector && $request->inspector->user)
                                            <div>
                                                <div class="font-semibold">{{ $request->inspector->user->full_name }}</div>
                                                <div class="text-xs text-gray-600">ID: {{ $request->inspector->inspector_code ?? $request->inspector->id }}</div>
        </div>
                                        @elseif($request->inspector)
                                            <div class="text-sm text-yellow-600">Inspector user details missing</div>
                                            <div class="text-sm text-gray-500">ID: {{ $request->inspector->id }}</div>
                                        @else
                                            <span class="text-sm text-gray-500 italic">Not Assigned</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex flex-col gap-2 items-end">
                                        <a href="{{ route('headtech.inspection-requests.show', $request->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-800 rounded hover:bg-gray-200 text-sm font-medium">View Details</a>
                                        @if($request->status === 'pending')
                                            <button type="button" onclick="openAssignModal({{ $request->id }})" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm font-medium">Assign Inspector</button>
                                        @elseif(in_array($request->status, ['assigned', 'in_progress']))
                                            <button type="button" onclick="openAssignModal({{ $request->id }})" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium">Re-Assign Inspector</button>
                                        @endif
                                        <!-- Assign Inspector Modal -->
                                        <div id="assign-modal-{{ $request->id }}" class="fixed z-50 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeAssignModal({{ $request->id }})"></div>
                                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                                                    <div class="sm:flex sm:items-start">
                                                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                                            <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                        </div>
                                                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">{{ in_array($request->status, ['assigned', 'in_progress']) ? 'Re-Assign Inspector' : 'Assign Inspector' }}</h3>
                                                            <div class="mt-2">
                                                                <form action="{{ route('headtech.inspection-requests.' . (in_array($request->status, ['assigned', 'in_progress']) ? 'reassign' : 'assign'), $request->id) }}" method="POST" class="space-y-3">
                                                                    @csrf
                                                                    <label for="inspector_id_modal_{{ $request->id }}" class="block text-xs font-medium text-gray-700">Inspector</label>
                                                                    <select name="inspector_id" id="inspector_id_modal_{{ $request->id }}" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" @if($allInspectors->count() == 0) disabled @endif required>
                                                                        @if($allInspectors->count() == 0)
                                                                            <option>No inspectors available</option>
                                                                        @else
                                                                            <option value="">-- Select Inspector --</option>
                                                                            @foreach ($allInspectors as $inspector)
                                                                                <option value="{{ $inspector->id }}">{{ $inspector->user->full_name }}</option>
                                                                            @endforeach
                                                                        @endif
                                                                    </select>
                                                                    <label for="scheduled_date_modal_{{ $request->id }}" class="block text-xs font-medium text-gray-700">Scheduled Date</label>
                                                                    <input type="date" name="scheduled_date" id="scheduled_date_modal_{{ $request->id }}" class="mt-1 block w-full border-gray-300 rounded-md" required>
                                                                    <label for="scheduled_time_modal_{{ $request->id }}" class="block text-xs font-medium text-gray-700">Scheduled Time</label>
                                                                    <input type="time" name="scheduled_time" id="scheduled_time_modal_{{ $request->id }}" class="mt-1 block w-full border-gray-300 rounded-md" required>
                                                                    <div class="mt-4 flex justify-end">
                                                                        <button type="button" onclick="closeAssignModal({{ $request->id }})" class="mr-2 px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancel</button>
                                                                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 disabled:opacity-50" @if($allInspectors->count() == 0) disabled @endif>{{ in_array($request->status, ['assigned', 'in_progress']) ? 'Re-Assign' : 'Assign' }}</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                        @endforeach
                            </tbody>
                        </table>
                </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No activities found</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new inspection request.</p>
                </div>
                @endif
                </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Prevent any potential refresh loops
document.addEventListener('DOMContentLoaded', function() {
    console.log('Assignments page loaded successfully');
    
    // Prevent form resubmission on refresh
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        const dropdowns = document.querySelectorAll('[id^="dropdown-"]');
        dropdowns.forEach(function(dropdown) {
            if (!dropdown.contains(event.target) && !event.target.closest('button[onclick*="toggleDropdown"]')) {
                dropdown.classList.add('hidden');
            }
        });
    });
});

// Function to toggle dropdown
function toggleDropdown(requestId) {
    const dropdown = document.getElementById('dropdown-' + requestId);
    const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
    
    // Close all other dropdowns
    allDropdowns.forEach(function(d) {
        if (d.id !== 'dropdown-' + requestId) {
            d.classList.add('hidden');
        }
    });
    
    // Toggle current dropdown
    dropdown.classList.toggle('hidden');
}

function openAssignModal(requestId) {
    document.getElementById('assign-modal-' + requestId).classList.remove('hidden');
    // Also close dropdown if open
    const dropdown = document.getElementById('dropdown-' + requestId);
    if (dropdown) dropdown.classList.add('hidden');
}
function closeAssignModal(requestId) {
    document.getElementById('assign-modal-' + requestId).classList.add('hidden');
}
</script>
@endpush 