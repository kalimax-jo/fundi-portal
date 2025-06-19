@extends('layouts.headtech')

@section('title', 'Assign Inspectors')

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Assign Inspectors
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <a href="{{ route('headtech.inspection-requests.index') }}" class="text-indigo-600 hover:text-indigo-500">All Requests</a>
                <span class="mx-2">/</span>
                <span>Assignment</span>
            </div>
            <div class="mt-2 flex items-center text-sm text-indigo-600">
                <svg class="mr-1.5 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zM6 8a2 2 0 11-4 0 2 2 0 014 0zM1.49 15.326a.78.78 0 01-.358-.442 3 3 0 014.308-3.516 6.484 6.484 0 00-1.905 3.959c-.023.222-.014.442.025.654a4.97 4.97 0 01-2.07-.655z" />
                </svg>
                {{ $stats['pending_requests'] }} pending • {{ $stats['available_inspectors'] }} available inspectors
            </div>
        </div>
    </div>
    <div class="mt-4 flex space-x-3 md:ml-4 md:mt-0">
        <a href="{{ route('headtech.inspection-requests.index', ['status' => 'pending']) }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 010-1.06L10.94 8.25H4a.75.75 0 010-1.5h6.94L7.72 3.53a.75.75 0 011.06-1.06l4.5 4.5a.75.75 0 010 1.06l-4.5 4.5a.75.75 0 01-1.06 0z" clip-rule="evenodd" />
            </svg>
            View Pending
        </a>
        <button class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
            </svg>
            Bulk Assign
        </button>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Assignment Statistics -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L8.28 10.5a.75.75 0 00-1.06 1.061l1.5 1.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending Requests</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['pending_requests'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zM6 8a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Available Inspectors</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['available_inspectors'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Urgent Requests</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['urgent_requests'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($stats['pending_requests'] === 0)
        <div class="bg-white shadow rounded-lg p-8 text-center text-gray-500 text-lg">
            <svg class="mx-auto h-12 w-12 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 118 0v2m-4 4a4 4 0 01-4-4H5a2 2 0 01-2-2V7a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-2a4 4 0 01-4 4z" /></svg>
            No pending inspection requests to assign.
        </div>
    @elseif($stats['available_inspectors'] === 0)
        <div class="bg-white shadow rounded-lg p-8 text-center text-gray-500 text-lg">
            <svg class="mx-auto h-12 w-12 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
            No available inspectors at the moment.
        </div>
    @else
    <!-- Assignment Interface -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Pending Requests Column -->
        <div class="bg-white shadow rounded-lg lg:col-span-2">
            <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Pending Requests
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Drag and drop requests to assign them to inspectors
                </p>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <div class="space-y-4 max-h-96 overflow-y-auto">
                    @foreach($pendingRequests as $request)
                    <div id="request-{{ $request->id }}" 
                         class="request-card p-4 border rounded-lg cursor-move hover:bg-indigo-50 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                         draggable="true"
                         tabindex="0"
                         ondragstart="dragStart(event)"
                         onkeydown="if(event.key==='Enter'){keyboardAssign(event, {{ $request->id }})}"
                         data-request-id="{{ $request->id }}"
                         data-request-data='@json([
                             'id' => $request->id,
                             'number' => $request->request_number,
                             'requester' => $request->requester->full_name ?? '',
                             'property' => $request->property->address ?? '',
                             'package' => $request->package->display_name ?? '',
                             'urgency' => $request->urgency,
                             'preferred_date' => $request->preferred_date ? $request->preferred_date->format('Y-m-d') : null,
                             'preferred_time' => $request->preferred_time_slot,
                             'business_partner' => $request->businessPartner ? $request->businessPartner->name : null
                         ])'>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                                        {{ $request->urgency === 'emergency' ? 'bg-red-100 text-red-800' : ($request->urgency === 'urgent' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ ucfirst($request->urgency) }}
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold text-gray-900">
                                        {{ $request->request_number }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $request->requester->full_name ?? '' }}
                                        @if($request->businessPartner)
                                        <span class="text-indigo-600">• {{ $request->businessPartner->name }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400">{{ $request->preferred_date ? $request->preferred_date->format('M j') : '' }}</span>
                        </div>
                        <div class="mt-2 flex items-center gap-2">
                            <span class="text-xs text-gray-600">
                                {{ Str::limit($request->property->address ?? '', 50) }}
                            </span>
                            <span class="text-xs text-gray-500">{{ $request->package->display_name ?? '' }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <!-- Inspector List Panel -->
        <div class="bg-white shadow rounded-lg lg:col-span-1 sticky top-4 h-fit">
            <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Available Inspectors
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Drop requests here to assign them
                </p>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <div class="space-y-4 max-h-96 overflow-y-auto">
                    @foreach($availableInspectors as $inspector)
                    <div class="inspector-zone p-4 border-2 border-dashed border-gray-300 rounded-lg min-h-[80px] transition-all duration-200 bg-white hover:bg-indigo-50 focus:bg-indigo-100 focus:outline-none"
                         tabindex="0"
                         ondrop="drop(event)"
                         ondragover="allowDrop(event)"
                         ondragenter="dragEnter(event)"
                         ondragleave="dragLeave(event)"
                         onclick="keyboardAssignInspector({{ $inspector->id }})"
                         data-inspector-id="{{ $inspector->id }}">
                        <div class="flex items-center gap-3">
                            <div>
                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">{{ $inspector->user->full_name }}</div>
                                <div class="text-xs text-gray-500">{{ $inspector->certification_level }} • {{ $inspector->user->email }}</div>
                            </div>
                        </div>
                        <div class="drop-indicator hidden text-green-600 mt-2">Drop request here to assign</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <!-- Assignment Modal -->
    <div id="assignmentModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg max-w-lg w-full mx-auto shadow-xl">
            <form id="assignmentForm" method="POST">
                @csrf
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Assign Inspector</h3>
                    <div class="flex gap-4">
                        <div class="flex-1 bg-gray-50 rounded-lg p-4 mb-4">
                            <h4 class="font-medium text-gray-900" id="modalRequestNumber"></h4>
                            <p class="text-sm text-gray-600" id="modalProperty"></p>
                        </div>
                        <div class="flex-1 bg-blue-50 rounded-lg p-4 mb-4 flex flex-col items-center justify-center">
                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center mb-2">
                                <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <h4 class="font-medium text-gray-900" id="modalInspectorName"></h4>
                        </div>
                    </div>
                    <input type="hidden" name="inspector_id" id="inspector_id">
                    <input type="hidden" name="request_id" id="request_id">
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" id="assignBtn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <span id="assignBtnText">Assign Inspector</span>
                        <svg id="assignSpinner" class="hidden animate-spin ml-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                    </button>
                    <button type="button" onclick="hideAssignmentModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
let draggedRequest = null;
let selectedInspectorId = null;

function dragStart(e) {
    draggedRequest = e.target;
    e.target.style.opacity = '0.4';
    document.querySelectorAll('.inspector-zone').forEach(zone => {
        zone.classList.add('border-indigo-300', 'bg-indigo-50');
        zone.querySelector('.drop-indicator').classList.remove('hidden');
    });
}
function allowDrop(e) { e.preventDefault(); }
function dragEnter(e) {
    e.preventDefault();
    if (e.target.classList.contains('inspector-zone') || e.target.closest('.inspector-zone')) {
        const zone = e.target.classList.contains('inspector-zone') ? e.target : e.target.closest('.inspector-zone');
        zone.classList.add('border-indigo-500', 'bg-indigo-100');
    }
}
function dragLeave(e) {
    if (e.target.classList.contains('inspector-zone') || e.target.closest('.inspector-zone')) {
        const zone = e.target.classList.contains('inspector-zone') ? e.target : e.target.closest('.inspector-zone');
        zone.classList.remove('border-indigo-500', 'bg-indigo-100');
    }
}
function drop(e) {
    e.preventDefault();
    if (!draggedRequest) return;
    const zone = e.target.classList.contains('inspector-zone') ? e.target : e.target.closest('.inspector-zone');
    if (!zone) return;
    const inspectorId = zone.dataset.inspectorId;
    const requestData = JSON.parse(draggedRequest.dataset.requestData);
    showAssignmentModal(requestData, zone, inspectorId);
    resetDragState();
}
function showAssignmentModal(requestData, inspectorZone, inspectorId) {
    document.getElementById('modalRequestNumber').textContent = '#' + requestData.number;
    document.getElementById('modalProperty').textContent = requestData.property;
    document.getElementById('modalInspectorName').textContent = inspectorZone.querySelector('.text-gray-900').textContent;
    document.getElementById('inspector_id').value = inspectorId;
    document.getElementById('request_id').value = requestData.id;
    document.getElementById('assignmentForm').action = `/head-tech/inspection-requests/${requestData.id}/assign`;
    document.getElementById('assignmentModal').classList.remove('hidden');
    selectedInspectorId = inspectorId;
}
function hideAssignmentModal() {
    document.getElementById('assignmentModal').classList.add('hidden');
    document.getElementById('assignmentForm').reset();
    selectedInspectorId = null;
}
function resetDragState() {
    document.querySelectorAll('.inspector-zone').forEach(zone => {
        zone.classList.remove('border-indigo-300', 'bg-indigo-50', 'border-indigo-500', 'bg-indigo-100');
        zone.querySelector('.drop-indicator').classList.add('hidden');
    });
    if (draggedRequest) draggedRequest.style.opacity = '1';
}
// Keyboard accessibility: Enter to assign
function keyboardAssign(event, requestId) {
    event.preventDefault();
    // Focus first inspector zone
    const firstZone = document.querySelector('.inspector-zone');
    if (firstZone) firstZone.focus();
}
function keyboardAssignInspector(inspectorId) {
    if (draggedRequest) {
        const requestData = JSON.parse(draggedRequest.dataset.requestData);
        const inspectorZone = document.querySelector(`.inspector-zone[data-inspector-id='${inspectorId}']`);
        showAssignmentModal(requestData, inspectorZone, inspectorId);
        resetDragState();
    }
}
// AJAX assignment (admin style)
const assignForm = document.getElementById('assignmentForm');
if (assignForm) {
    assignForm.addEventListener('submit', function(e) {
        e.preventDefault();
        document.getElementById('assignBtnText').classList.add('hidden');
        document.getElementById('assignSpinner').classList.remove('hidden');
        const formData = new FormData(this);
        const url = this.action;
        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the assigned request from the pending list
                const requestId = formData.get('request_id');
                const requestElement = document.getElementById('request-' + requestId);
                if (requestElement) {
                    requestElement.remove();
                }
                hideAssignmentModal();
                showNotification('Inspector assigned successfully!', 'success');
                // Refresh page if no more pending requests
                const remainingRequests = document.querySelectorAll('.request-card').length;
                if (remainingRequests === 0) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }
            } else {
                showNotification(data.message || 'Failed to assign inspector', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while assigning inspector', 'error');
        })
        .finally(() => {
            document.getElementById('assignBtnText').classList.remove('hidden');
            document.getElementById('assignSpinner').classList.add('hidden');
        });
    });
}
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white`;
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
// Close modal with Escape key
// (optional: add bulkAssignMode if you want)
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideAssignmentModal();
    }
});
</script>
@endpush 