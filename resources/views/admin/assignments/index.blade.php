@extends('layouts.admin')

@section('title', 'Assignment Workflow')

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Assignment Workflow
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                Assign pending inspection requests to available inspectors
            </div>
        </div>
    </div>
    <div class="mt-4 flex space-x-3 md:ml-4 md:mt-0">
        <a href="{{ route('admin.inspectors.assignments') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 010-1.06L10.94 8.25H4a.75.75 0 010-1.5h6.94L7.72 3.53a.75.75 0 011.06-1.06l4.5 4.5a.75.75 0 010 1.06l-4.5 4.5a.75.75 0 01-1.06 0z" clip-rule="evenodd" />
            </svg>
            Back to Overview
        </a>
        @if($pendingRequests->count() > 0 && $inspectors->where('availability_status', 'available')->count() > 0)
            <button type="button" onclick="autoAssignRequests()" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 2L3 7v11a2 2 0 002 2h4v-6h2v6h4a2 2 0 002-2V7l-7-5z" />
                </svg>
                Auto-Assign All
            </button>
        @endif
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Assignment Statistics -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5">
        <!-- Pending Requests -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-orange-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending Requests</dt>
                            <dd class="text-lg font-medium text-gray-900" id="pending-count">{{ $stats['pending_requests'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Inspectors -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Available Inspectors</dt>
                            <dd class="text-lg font-medium text-gray-900" id="available-count">{{ $stats['available_inspectors'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Urgent Requests -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Urgent Requests</dt>
                            <dd class="text-lg font-medium text-gray-900" id="urgent-count">{{ $stats['urgent_requests'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Busy Inspectors -->
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
                            <dt class="text-sm font-medium text-gray-500 truncate">Busy Inspectors</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['busy_inspectors'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assigned Today -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Assigned Today</dt>
                            <dd class="text-lg font-medium text-gray-900" id="assigned-today">{{ $stats['assigned_today'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignment Interface -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        
        <!-- Pending Requests Column -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                    Pending Inspection Requests
                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800" id="pending-badge">
                        {{ $pendingRequests->count() }} pending
                    </span>
                </h3>
                
                @if($pendingRequests->count() > 0)
                    <div class="space-y-4" id="pending-requests">
                        @foreach($pendingRequests as $request)
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 cursor-pointer request-item" 
                                 data-request-id="{{ $request->id }}" 
                                 onclick="selectRequest(this)">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center">
                                            <h4 class="text-sm font-medium text-gray-900">{{ $request->request_number }}</h4>
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                                {{ $request->urgency === 'emergency' ? 'bg-red-100 text-red-800' : 
                                                   ($request->urgency === 'urgent' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                                {{ ucfirst($request->urgency) }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">{{ $request->package->display_name ?? 'Standard Inspection' }}</p>
                                        <p class="text-sm text-gray-500">{{ $request->property->address }}</p>
                                        <div class="mt-2 flex items-center text-xs text-gray-500">
                                            <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Requested {{ $request->created_at->diffForHumans() }}
                                        </div>
                                        @if($request->preferred_date)
                                            <div class="mt-1 flex items-center text-xs text-blue-600">
                                                <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                Preferred: {{ $request->preferred_date->format('M d, Y') }}
                                                </div>
                                        @endif
                                    </div>
                                    <div class="ml-4 text-right">
                                        <div class="text-sm font-medium text-gray-900">
                                            @if($request->isBusinessRequest() && $request->businessPartner)
                                                {{ $request->businessPartner->name }}
                                            @else
                                                {{ $request->requester->full_name }}
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $request->isBusinessRequest() ? 'Business Partner' : 'Individual Client' }}
                                        </div>
                                        @if($request->loan_amount)
                                            <div class="text-xs text-green-600 mt-1">
                                                Loan: ${{ number_format($request->loan_amount) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- No Pending Requests -->
                    <div class="text-center py-8" id="no-requests">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">All caught up!</h3>
                        <p class="mt-1 text-sm text-gray-500">No pending requests to assign.</p>
                        <div class="mt-4">
                            <a href="{{ route('admin.inspection-requests.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm">
                                View all inspection requests →
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Available Inspectors Column -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                    Available Inspectors
                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        {{ $inspectors->where('availability_status', 'available')->count() }} available
                    </span>
                </h3>
                
                @php $availableInspectors = $inspectors->where('availability_status', 'available'); @endphp
                
                @if($availableInspectors->count() > 0)
                    <div class="space-y-3" id="available-inspectors">
                        @foreach($availableInspectors as $inspector)
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 cursor-pointer inspector-item" 
                                 data-inspector-id="{{ $inspector->id }}" 
                                 onclick="selectInspector(this)">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        @if($inspector->user->profile_photo)
                                            <img class="h-8 w-8 rounded-full" src="{{ Storage::url($inspector->user->profile_photo) }}" alt="{{ $inspector->user->full_name }}">
                                        @else
                                            <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                                <svg class="h-4 w-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">{{ $inspector->user->full_name }}</p>
                                            <p class="text-xs text-gray-500">{{ $inspector->inspector_code }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="flex items-center">
                                            <span class="text-sm font-medium text-gray-900">{{ number_format($inspector->rating, 1) }}</span>
                                            <svg class="ml-1 h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        </div>
                                        <p class="text-xs text-gray-500">{{ $inspector->total_inspections }} jobs</p>
                                        <p class="text-xs text-gray-500">{{ $inspector->experience_years }} years exp</p>
                                    </div>
                                </div>
                                
                                <!-- Specializations -->
                                @if($inspector->specializations && count($inspector->specializations) > 0)
                                    <div class="mt-2 flex flex-wrap gap-1">
                                        @foreach(array_slice($inspector->specializations, 0, 2) as $specialization)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                                {{ ucfirst(str_replace('_', ' ', $specialization)) }}
                                            </span>
                                        @endforeach
                                        @if(count($inspector->specializations) > 2)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                +{{ count($inspector->specializations) - 2 }}
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                <!-- Current Workload -->
                                @php
                                    $currentWorkload = $inspector->inspectionRequests()
                                        ->whereIn('status', ['assigned', 'in_progress'])
                                        ->count();
                                @endphp
                                @if($currentWorkload > 0)
                                    <div class="mt-2 text-xs text-gray-500">
                                        Current workload: {{ $currentWorkload }} assignment{{ $currentWorkload > 1 ? 's' : '' }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- No Available Inspectors -->
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No Available Inspectors</h3>
                        <p class="mt-1 text-sm text-gray-500">All inspectors are currently busy or offline.</p>
                        <div class="mt-4">
                            <a href="{{ route('admin.inspectors.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm">
                                Manage inspectors →
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Assignment Action Panel -->
    <div class="bg-white shadow rounded-lg" id="assignment-panel" style="display: none;">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Make Assignment</h3>
                <button type="button" onclick="clearSelection()" class="text-sm text-gray-500 hover:text-gray-700">Clear Selection</button>
            </div>
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <!-- Selected Request -->
                <div class="border-l-4 border-blue-400 bg-blue-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Selected Request</h3>
                            <div class="mt-2 text-sm text-blue-700" id="selected-request-details">
                                Please select a request from the left panel
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Selected Inspector -->
                <div class="border-l-4 border-green-400 bg-green-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-800">Selected Inspector</h3>
                            <div class="mt-2 text-sm text-green-700" id="selected-inspector-details">
                                Please select an inspector from the right panel
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scheduling Options -->
            <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="scheduled_date" class="block text-sm font-medium text-gray-700">Scheduled Date</label>
                    <input type="date" name="scheduled_date" id="scheduled_date" 
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           class="mt-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm">
                </div>
                <div>
                    <label for="scheduled_time" class="block text-sm font-medium text-gray-700">Scheduled Time</label>
                    <select name="scheduled_time" id="scheduled_time" 
                            class="mt-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm">
                        <option value="">Select time</option>
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
            <div class="mt-6">
                <label for="assignment_notes" class="block text-sm font-medium text-gray-700">Assignment Notes (Optional)</label>
                <textarea name="assignment_notes" id="assignment_notes" rows="3" 
                          placeholder="Add any special instructions or notes for this assignment..."
                          class="mt-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm"></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="clearSelection()" 
                        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancel
                </button>
                <button type="button" onclick="makeAssignment()" id="assign-button" disabled
                        class="bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    Assign Inspector
                </button>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
let selectedRequest = null;
let selectedInspector = null;

function selectRequest(element) {
    // Clear previous selections
    document.querySelectorAll('.request-item').forEach(item => {
        item.classList.remove('ring-2', 'ring-blue-500', 'bg-blue-50');
    });
    
    // Select current request
    element.classList.add('ring-2', 'ring-blue-500', 'bg-blue-50');
    selectedRequest = element.dataset.requestId;
    
    // Update selected request details
    const requestNumber = element.querySelector('h4').textContent;
    const packageType = element.querySelector('.text-gray-600').textContent;
    const location = element.querySelector('.text-gray-500').textContent;
    const clientName = element.querySelector('.text-gray-900').textContent;
    
    document.getElementById('selected-request-details').innerHTML = `
        <strong>${requestNumber}</strong><br>
        ${packageType}<br>
        📍 ${location}<br>
        👤 ${clientName}
    `;
    
    checkCanAssign();
}

function selectInspector(element) {
    // Clear previous selections
    document.querySelectorAll('.inspector-item').forEach(item => {
        item.classList.remove('ring-2', 'ring-green-500', 'bg-green-50');
    });
    
    // Select current inspector
    element.classList.add('ring-2', 'ring-green-500', 'bg-green-50');
    selectedInspector = element.dataset.inspectorId;
    
    // Update selected inspector details
    const inspectorName = element.querySelector('.text-gray-900').textContent;
    const inspectorCode = element.querySelector('.text-gray-500').textContent;
    const rating = element.querySelector('.text-right .text-gray-900').textContent;
    const experience = element.querySelectorAll('.text-gray-500')[2].textContent;
    
    document.getElementById('selected-inspector-details').innerHTML = `
        <strong>${inspectorName}</strong><br>
        ${inspectorCode}<br>
        ⭐ ${rating} rating<br>
        📅 ${experience}
    `;
    
    checkCanAssign();
}

function checkCanAssign() {
    const canAssign = selectedRequest && selectedInspector;
    document.getElementById('assign-button').disabled = !canAssign;
    
    if (canAssign) {
        document.getElementById('assignment-panel').style.display = 'block';
        document.getElementById('assignment-panel').scrollIntoView({ behavior: 'smooth' });
    }
}

function clearSelection() {
    selectedRequest = null;
    selectedInspector = null;
    
    // Clear visual selections
    document.querySelectorAll('.request-item').forEach(item => {
        item.classList.remove('ring-2', 'ring-blue-500', 'bg-blue-50');
    });
    
    document.querySelectorAll('.inspector-item').forEach(item => {
        item.classList.remove('ring-2', 'ring-green-500', 'bg-green-50');
    });
    
    // Reset details
    document.getElementById('selected-request-details').innerHTML = 'Please select a request from the left panel';
    document.getElementById('selected-inspector-details').innerHTML = 'Please select an inspector from the right panel';
    
    // Hide assignment panel
    document.getElementById('assignment-panel').style.display = 'none';
    
    // Clear form
    document.getElementById('scheduled_date').value = '';
    document.getElementById('scheduled_time').value = '';
    document.getElementById('assignment_notes').value = '';
}

function makeAssignment() {
    if (!selectedRequest || !selectedInspector) {
        showNotification('Please select both a request and an inspector', 'error');
        return;
    }
    
    const assignButton = document.getElementById('assign-button');
    assignButton.disabled = true;
    assignButton.innerHTML = `
        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Assigning...
    `;
    
    // Collect assignment data
    const assignmentData = {
        request_id: selectedRequest,
        inspector_id: selectedInspector,
        scheduled_date: document.getElementById('scheduled_date').value,
        scheduled_time: document.getElementById('scheduled_time').value,
        notes: document.getElementById('assignment_notes').value
    };
    
    // Make assignment API call
    fetch(`/admin/assignments/assign`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(assignmentData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Assignment successful!', 'success');
            
            // Remove assigned request from pending list
            const requestElement = document.querySelector(`[data-request-id="${selectedRequest}"]`);
            if (requestElement) {
                requestElement.remove();
            }
            
            // Update counters
            updateCounters();
            
            // Clear selection
            clearSelection();
            
            // Check if no more requests
            checkEmptyState();
            
        } else {
            showNotification(data.message || 'Assignment failed', 'error');
        }
    })
    .catch(error => {
        console.error('Assignment error:', error);
        showNotification('An error occurred during assignment', 'error');
    })
    .finally(() => {
        // Reset button
        assignButton.disabled = false;
        assignButton.innerHTML = 'Assign Inspector';
    });
}

function autoAssignRequests() {
    if (!confirm('This will automatically assign all pending requests to the best available inspectors. Continue?')) {
        return;
    }
    
    const button = event.target;
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = `
        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Auto-Assigning...
    `;
    
    fetch('/admin/assignments/auto-assign', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(`Successfully assigned ${data.assigned_count} requests`, 'success');
            
            // Refresh the page to show updated data
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showNotification(data.message || 'Auto-assignment failed', 'error');
        }
    })
    .catch(error => {
        console.error('Auto-assignment error:', error);
        showNotification('An error occurred during auto-assignment', 'error');
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = originalText;
    });
}

function updateCounters() {
    // Update pending count
    const pendingCount = document.querySelectorAll('.request-item').length;
    document.getElementById('pending-count').textContent = pendingCount;
    document.getElementById('pending-badge').textContent = `${pendingCount} pending`;
    
    // Update assigned today (increment by 1)
    const assignedToday = document.getElementById('assigned-today');
    const currentCount = parseInt(assignedToday.textContent);
    assignedToday.textContent = currentCount + 1;
}

function checkEmptyState() {
    const requestsContainer = document.getElementById('pending-requests');
    const noRequestsState = document.getElementById('no-requests');
    
    if (document.querySelectorAll('.request-item').length === 0) {
        if (requestsContainer) requestsContainer.style.display = 'none';
        if (noRequestsState) noRequestsState.style.display = 'block';
    }
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden z-50`;
    
    const iconColor = type === 'success' ? 'text-green-400' : type === 'error' ? 'text-red-400' : 'text-blue-400';
    const textColor = type === 'success' ? 'text-green-800' : type === 'error' ? 'text-red-800' : 'text-blue-800';
    
    const icon = type === 'success' 
        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'
        : type === 'error'
        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />'
        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';
    
    notification.innerHTML = `
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 ${iconColor}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        ${icon}
                    </svg>
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-medium ${textColor}">${message}</p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-600 focus:outline-none" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Set default scheduled date to tomorrow
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('scheduled_date').value = tomorrow.toISOString().split('T')[0];
    
    // Set default time
    document.getElementById('scheduled_time').value = '09:00';
});

// Auto-refresh every 60 seconds
setInterval(function() {
    console.log('Checking for updates...');
    // Could implement partial refresh here
}, 60000);
</script>
@endpush
@endsection