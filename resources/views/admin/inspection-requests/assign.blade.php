{{-- File Path: resources/views/admin/inspection-requests/assign.blade.php --}}

@extends('layouts.admin')

@section('title', 'Assign Inspectors')

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Assign Inspectors
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <a href="{{ route('admin.inspection-requests.index') }}" class="text-indigo-600 hover:text-indigo-500">All Requests</a>
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
        <a href="{{ route('admin.inspection-requests.pending') }}" 
           class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 010-1.06L10.94 8.25H4a.75.75 0 010-1.5h6.94L7.72 3.53a.75.75 0 011.06-1.06l4.5 4.5a.75.75 0 010 1.06l-4.5 4.5a.75.75 0 01-1.06 0z" clip-rule="evenodd" />
            </svg>
            View Pending
        </a>
        <button onclick="bulkAssignMode()" 
                class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
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

    @if($stats['pending_requests'] > 0 && $stats['available_inspectors'] > 0)
    <!-- Assignment Interface -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pending Requests Column -->
        <div class="bg-white shadow rounded-lg">
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
                         class="request-card p-4 border rounded-lg cursor-move hover:bg-gray-50 transition-colors duration-200"
                         draggable="true"
                         ondragstart="dragStart(event)"
                         data-request-id="{{ $request->id }}"
                         data-request-data="{{ json_encode([
                             'id' => $request->id,
                             'number' => $request->request_number,
                             'requester' => $request->requester->full_name,
                             'property' => $request->property->address,
                             'package' => $request->package->display_name,
                             'urgency' => $request->urgency,
                             'preferred_date' => $request->preferred_date ? $request->preferred_date->format('Y-m-d') : null,
                             'preferred_time' => $request->preferred_time_slot,
                             'business_partner' => $request->businessPartner ? $request->businessPartner->name : null
                         ]) }}">
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full flex items-center justify-center
                                        {{ $request->urgency === 'emergency' ? 'bg-red-100' : 
                                           ($request->urgency === 'urgent' ? 'bg-yellow-100' : 'bg-gray-100') }}">
                                        <span class="text-xs font-medium
                                            {{ $request->urgency === 'emergency' ? 'text-red-600' : 
                                               ($request->urgency === 'urgent' ? 'text-yellow-600' : 'text-gray-600') }}">
                                            {{ strtoupper(substr($request->request_number, -3)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $request->request_number }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $request->requester->full_name }}
                                        @if($request->businessPartner)
                                        <span class="text-indigo-600">• {{ $request->businessPartner->name }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                {{ $request->urgency === 'emergency' ? 'bg-red-100 text-red-800' : 
                                   ($request->urgency === 'urgent' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($request->urgency) }}
                            </span>
                        </div>
                        
                        <div class="mt-2">
                            <p class="text-xs text-gray-600">
                                {{ Str::limit($request->property->address, 50) }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ $request->package->display_name }}
                                @if($request->preferred_date)
                                • Preferred: {{ $request->preferred_date->format('M j') }}
                                @endif
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Available Inspectors Column -->
        <div class="bg-white shadow rounded-lg">
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
                    <div class="inspector-zone p-4 border-2 border-dashed border-gray-300 rounded-lg min-h-[80px] transition-all duration-200"
                         ondrop="drop(event)"
                         ondragover="allowDrop(event)"
                         ondragenter="dragEnter(event)"
                         ondragleave="dragLeave(event)"
                         data-inspector-id="{{ $inspector->id }}">
                        
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                    <span class="text-sm font-medium text-green-800">
                                        {{ strtoupper(substr($inspector->user->first_name, 0, 1) . substr($inspector->user->last_name, 0, 1)) }}
                                    </span>
                                </div>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $inspector->user->full_name }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $inspector->inspector_code }} • 
                                    Rating: {{ number_format($inspector->rating, 1) }}/5.0 •
                                    {{ $inspector->total_inspections }} inspections
                                </p>
                                <div class="mt-1">
                                    @if($inspector->specializations)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach(array_slice($inspector->specializations, 0, 2) as $specialization)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ ucfirst($specialization) }}
                                        </span>
                                        @endforeach
                                        @if(count($inspector->specializations) > 2)
                                        <span class="text-xs text-gray-400">+{{ count($inspector->specializations) - 2 }} more</span>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Available
                                </span>
                            </div>
                        </div>
                        
                        <!-- Drop Zone Indicator -->
                        <div class="drop-indicator hidden mt-3 p-2 bg-indigo-50 border border-indigo-200 rounded text-center">
                            <p class="text-sm text-indigo-600">Drop request here to assign</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @else
    <!-- No Assignment Possible -->
    <div class="bg-white shadow rounded-lg">
        <div class="text-center py-12">
            @if($stats['pending_requests'] === 0)
            <svg class="mx-auto h-12 w-12 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No pending requests!</h3>
            <p class="mt-1 text-sm text-gray-500">All inspection requests have been assigned.</p>
            @elseif($stats['available_inspectors'] === 0)
            <svg class="mx-auto h-12 w-12 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No available inspectors</h3>
            <p class="mt-1 text-sm text-gray-500">All inspectors are currently busy or offline.</p>
            @endif
            <div class="mt-6">
                <a href="{{ route('admin.inspection-requests.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    View All Requests
                </a>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Assignment Modal -->
<div id="assignmentModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg max-w-lg w-full mx-auto shadow-xl">
        <form id="assignmentForm" method="POST">
            @csrf
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Assign Inspector</h3>
                
                <!-- Request Info -->
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <h4 class="font-medium text-gray-900" id="modalRequestNumber"></h4>
                    <p class="text-sm text-gray-600" id="modalRequester"></p>
                    <p class="text-sm text-gray-600" id="modalProperty"></p>
                </div>

                <!-- Inspector Info -->
                <div class="bg-blue-50 rounded-lg p-4 mb-4">
                    <h4 class="font-medium text-gray-900" id="modalInspectorName"></h4>
                    <p class="text-sm text-gray-600" id="modalInspectorCode"></p>
                </div>

                <!-- Schedule -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="scheduled_date" class="block text-sm font-medium text-gray-700">Inspection Date</label>
                        <input type="date" name="scheduled_date" id="scheduled_date" required
                               min="{{ date('Y-m-d') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="scheduled_time" class="block text-sm font-medium text-gray-700">Time</label>
                        <select name="scheduled_time" id="scheduled_time" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="08:00">8:00 AM</option>
                            <option value="09:00">9:00 AM</option>
                            <option value="10:00">10:00 AM</option>
                            <option value="11:00">11:00 AM</option>
                            <option value="14:00">2:00 PM</option>
                            <option value="15:00">3:00 PM</option>
                            <option value="16:00">4:00 PM</option>
                        </select>
                    </div>
                </div>

                <!-- Notes -->
                <div class="mb-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Assignment Notes (Optional)</label>
                    <textarea name="notes" id="notes" rows="3" 
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                              placeholder="Any special instructions for the inspector..."></textarea>
                </div>

                <input type="hidden" name="inspector_id" id="inspector_id">
            </div>
            
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="submit" 
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Assign Inspector
                </button>
                <button type="button" onclick="hideAssignmentModal()" 
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let draggedRequest = null;

function dragStart(e) {
    draggedRequest = e.target;
    e.target.style.opacity = '0.4';
    
    // Add visual feedback to all inspector zones
    document.querySelectorAll('.inspector-zone').forEach(zone => {
        zone.classList.add('border-indigo-300', 'bg-indigo-50');
        zone.querySelector('.drop-indicator').classList.remove('hidden');
    });
}

function allowDrop(e) {
    e.preventDefault();
}

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
    
    // Show assignment modal
    showAssignmentModal(requestData, zone, inspectorId);
    
    // Reset visual state
    resetDragState();
}

function showAssignmentModal(requestData, inspectorZone, inspectorId) {
    // Populate request info
    document.getElementById('modalRequestNumber').textContent = requestData.number;
    document.getElementById('modalRequester').textContent = requestData.requester + (requestData.business_partner ? ' via ' + requestData.business_partner : '');
    document.getElementById('modalProperty').textContent = requestData.property;
    
    // Populate inspector info
    const inspectorName = inspectorZone.querySelector('.text-gray-900').textContent;
    const inspectorCode = inspectorZone.querySelector('.text-gray-500').textContent.split(' • ')[0];
    document.getElementById('modalInspectorName').textContent = inspectorName;
    document.getElementById('modalInspectorCode').textContent = inspectorCode;
    
    // Set form data
    document.getElementById('inspector_id').value = inspectorId;
    document.getElementById('assignmentForm').action = `/admin/inspection-requests/${requestData.id}/assign-inspector`;
    
    // Set preferred date if available
    if (requestData.preferred_date) {
        document.getElementById('scheduled_date').value = requestData.preferred_date;
    }
    
    // Set preferred time based on time slot
    if (requestData.preferred_time === 'morning') {
        document.getElementById('scheduled_time').value = '09:00';
    } else if (requestData.preferred_time === 'afternoon') {
        document.getElementById('scheduled_time').value = '14:00';
    }
    
    // Show modal
    document.getElementById('assignmentModal').classList.remove('hidden');
}

function hideAssignmentModal() {
    document.getElementById('assignmentModal').classList.add('hidden');
    document.getElementById('assignmentForm').reset();
}

function resetDragState() {
    if (draggedRequest) {
        draggedRequest.style.opacity = '1';
        draggedRequest = null;
    }
    
    // Reset all inspector zones
    document.querySelectorAll('.inspector-zone').forEach(zone => {
        zone.classList.remove('border-indigo-300', 'bg-indigo-50', 'border-indigo-500', 'bg-indigo-100');
        zone.querySelector('.drop-indicator').classList.add('hidden');
    });
}

// Reset drag state when drag ends without drop
document.addEventListener('dragend', function(e) {
    if (e.target.classList.contains('request-card')) {
        resetDragState();
    }
});

// Handle form submission
document.getElementById('assignmentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const url = this.action;
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the assigned request from the pending list
            const requestId = url.split('/')[3];
            const requestElement = document.getElementById('request-' + requestId);
            if (requestElement) {
                requestElement.remove();
            }
            
            hideAssignmentModal();
            
            // Show success message
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
    });
});

function showNotification(message, type) {
    // Simple notification - you can enhance this
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

function bulkAssignMode() {
    alert('Bulk assignment feature coming soon! For now, use drag and drop to assign individual requests.');
}

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideAssignmentModal();
    }
});
</script>
@endpush

@endsection