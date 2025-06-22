@extends('layouts.headtech')

@section('title', 'Assignments')

@section('content')
<div class="py-8">
    <h1 class="text-2xl font-bold mb-6">Assignments</h1>
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded shadow p-4 flex flex-col items-center">
            <div class="text-sm text-gray-500">Pending Requests</div>
            <div class="text-2xl font-bold text-indigo-600">{{ $pendingRequests->count() }}</div>
        </div>
        <div class="bg-white rounded shadow p-4 flex flex-col items-center">
            <div class="text-sm text-gray-500">Total Inspectors</div>
            <div class="text-2xl font-bold text-gray-900">{{ $totalCount }}</div>
        </div>
        <div class="bg-white rounded shadow p-4 flex flex-col items-center">
            <div class="text-sm text-gray-500">Available</div>
            <div class="text-2xl font-bold text-green-600">{{ $availableCount }}</div>
        </div>
        <div class="bg-white rounded shadow p-4 flex flex-col items-center">
            <div class="text-sm text-gray-500">Busy</div>
            <div class="text-2xl font-bold text-yellow-500">{{ $busyCount }}</div>
        </div>
        <!-- Add more cards if needed for offline, etc. -->
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <!-- Pending Requests -->
        <div>
            <h2 class="text-lg font-semibold mb-4">Pending Requests ({{ $pendingRequests->count() }})</h2>
            @if($pendingRequests->count())
                <ul class="divide-y divide-gray-200 bg-white rounded shadow">
                    @foreach($pendingRequests as $request)
                        <li class="p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                            <div>
                                <div class="font-semibold text-indigo-700">{{ $request->request_number ?? 'Request #' . $request->id }}</div>
                                <div class="text-xs text-gray-500 mb-2">{{ $request->property->address ?? '-' }} | {{ ucfirst($request->urgency) }} | {{ $request->package->display_name ?? '-' }}</div>
                            </div>
                            <div class="flex gap-2 items-center">
                                <a href="{{ route('headtech.inspection-requests.show', $request->id) }}" class="inline-flex items-center px-2 py-2 bg-gray-100 hover:bg-gray-200 rounded text-gray-600" title="View Request">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <button class="assign-btn bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm"
                                    data-request-id="{{ $request->id }}"
                                    data-preferred-date="{{ $request->preferred_date ?? '' }}"
                                    data-preferred-time="{{ $request->preferred_time_slot ?? '' }}"
                                >Assign</button>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-gray-400">No pending requests.</div>
            @endif
        </div>
        <!-- All Inspectors -->
        <div>
            <h2 class="text-lg font-semibold mb-4">All Inspectors ({{ $totalCount }})</h2>
            @if($inspectors->count())
                <ul class="divide-y divide-gray-200 bg-white rounded shadow">
                    @foreach($inspectors as $inspector)
                        @php
                            $status = $inspector->availability_status;
                            $statusColor = match($status) {
                                'available' => 'bg-green-100 text-green-800',
                                'busy' => 'bg-yellow-100 text-yellow-800',
                                'offline' => 'bg-gray-100 text-gray-500',
                                default => 'bg-gray-100 text-gray-500',
                            };
                            $statusLabel = ucfirst($status);
                        @endphp
                        <li class="p-4 flex items-center gap-4">
                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center font-bold text-gray-700">
                                {{ $inspector->user->initials ?? substr($inspector->user->first_name,0,1).substr($inspector->user->last_name,0,1) }}
                            </div>
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">{{ $inspector->user->full_name ?? 'Inspector '.$inspector->id }}</div>
                                <div class="text-xs text-gray-500">{{ ucfirst($inspector->certification_level) }} Level</div>
                            </div>
                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $statusColor }}">{{ $statusLabel }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-gray-400">No inspectors found.</div>
            @endif
        </div>
    </div>
    <!-- Assignment Modal -->
    <div id="assignModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
            <button id="closeModal" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700">&times;</button>
            <h3 class="text-lg font-semibold mb-4">Assign Inspector</h3>
            <div id="clientSuggestion" class="mb-4 hidden">
                <div class="text-xs text-gray-500">Client preferred: <span id="suggestedDate" class="font-semibold"></span> <span id="suggestedTime" class="font-semibold"></span></div>
            </div>
            <div id="inspectorSuggestion" class="mb-4 hidden">
                <div class="text-xs text-blue-500">Suggested inspector availability: <span id="inspectorAvailableTimes">(coming soon)</span></div>
            </div>
            <form id="assignForm" method="POST" action="">
                @csrf
                <input type="hidden" name="request_id" id="modalRequestId">
                <div class="mb-4">
                    <label for="inspector_id" class="block text-sm font-medium text-gray-700 mb-1">Inspector</label>
                    <select name="inspector_id" id="modalInspectorId" class="w-full border rounded px-3 py-2">
                        @foreach($inspectors as $inspector)
                            <option value="{{ $inspector->id }}">{{ $inspector->user->full_name ?? 'Inspector '.$inspector->id }} ({{ ucfirst($inspector->availability_status) }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-1">Scheduled Date</label>
                    <input type="date" name="scheduled_date" id="modalScheduledDate" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-4">
                    <label for="scheduled_time" class="block text-sm font-medium text-gray-700 mb-1">Scheduled Time</label>
                    <input type="time" name="scheduled_time" id="modalScheduledTime" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
                    <textarea name="notes" id="modalNotes" class="w-full border rounded px-3 py-2"></textarea>
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Assign</button>
            </form>
        </div>
    </div>
</div>
<!-- Modal JS -->
<script>
    document.querySelectorAll('.assign-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const requestId = this.getAttribute('data-request-id');
            const preferredDate = this.getAttribute('data-preferred-date');
            const preferredTime = this.getAttribute('data-preferred-time');
            document.getElementById('modalRequestId').value = requestId;
            document.getElementById('assignForm').action = `/ht/inspection-requests/${requestId}/assign`;
            // Prefill date/time
            document.getElementById('modalScheduledDate').value = preferredDate || '';
            document.getElementById('modalScheduledTime').value = '';
            // Show client suggestion
            if (preferredDate || preferredTime) {
                document.getElementById('clientSuggestion').classList.remove('hidden');
                document.getElementById('suggestedDate').textContent = preferredDate ? preferredDate : '';
                document.getElementById('suggestedTime').textContent = preferredTime ? '(' + preferredTime + ')' : '';
            } else {
                document.getElementById('clientSuggestion').classList.add('hidden');
            }
            // Placeholder for inspector availability
            document.getElementById('inspectorSuggestion').classList.remove('hidden');
            document.getElementById('assignModal').classList.remove('hidden');
        });
    });
    document.getElementById('closeModal').addEventListener('click', function() {
        document.getElementById('assignModal').classList.add('hidden');
    });
    // Optional: close modal on outside click
    document.getElementById('assignModal').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
</script>
@endsection 