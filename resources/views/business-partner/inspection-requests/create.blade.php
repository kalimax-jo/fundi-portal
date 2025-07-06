@extends('layouts.business-partner')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">New Inspection Request</h1>
                <p class="text-sm text-gray-600 mb-4">Submit a new inspection request for {{ $partner->name }}</p>
                <div class="flex items-center mb-6">
                    <div class="flex-1 h-1 bg-indigo-200 rounded-full">
                        <div id="stepper-bar" class="h-1 bg-indigo-600 rounded-full transition-all duration-300" style="width:33%"></div>
                    </div>
                    <div class="ml-4 text-xs text-gray-500" id="stepper-label">Step 1 of 3</div>
                </div>
                <form id="requestStepperForm" method="POST" action="{{ route('business-partner.inspection-requests.store') }}">
                    @csrf
                    @if(session('error'))
                        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
                    @endif
                    @if(session('success'))
                        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                            <ul class="list-disc pl-5">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <!-- Step 1: Property Selection -->
                    <div id="step-1">
                        <h2 class="text-lg font-semibold mb-4">1. Select or Create Property</h2>
                        <div class="mb-4 flex flex-col md:flex-row md:items-center md:space-x-4">
                            <input type="text" id="propertySearch" placeholder="Search property by code, address, or owner..." class="mb-2 md:mb-0 flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                            <a href="{{ route('business-partner.properties.create') }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">+ Create New Property</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200" id="propertyTable">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Owner</th>
                                        <th class="px-4 py-2"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @if($properties->count() === 0)
                                        <tr>
                                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                                <div class="flex flex-col items-center justify-center">
                                                    <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 48 48"><circle cx="24" cy="24" r="20" stroke-width="2"/><path stroke-width="2" d="M16 24h16M24 16v16"/></svg>
                                                    <span class="block mb-2">No properties found for your organization.</span>
                                                    <a href="{{ route('business-partner.properties.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">+ Create your first Property</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @else
                                        @foreach($properties as $property)
                                        <tr class="property-row cursor-pointer" data-id="{{ $property->id }}" data-owner-email="{{ $property->client_email ?? $property->owner_email ?? '-' }}">
                                            <td class="px-4 py-2">{{ $property->property_code }}</td>
                                            <td class="px-4 py-2">{{ $property->address }}</td>
                                            <td class="px-4 py-2">
                                                {{ $property->client_name ?? $property->owner_name ?? '-' }}
                                            </td>
                                            <td class="px-4 py-2 text-right">
                                                <input type="radio" name="property_id" value="{{ $property->id }}" class="property-radio" />
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div id="newPropertyForm" class="hidden mt-6 p-4 bg-gray-50 border rounded">
                            <h3 class="font-semibold mb-2">New Property Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Property Address</label>
                                    <input type="text" name="new_property_address" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Enter address">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Property Code</label>
                                    <input type="text" name="new_property_code" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Enter code">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">District</label>
                                    <input type="text" name="new_property_district" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="District">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Sector</label>
                                    <input type="text" name="new_property_sector" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Sector">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Cell</label>
                                    <input type="text" name="new_property_cell" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Cell">
                                </div>
                            </div>
                            <h3 class="font-semibold mt-4 mb-2">Property Owner (Client) Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Full Name</label>
                                    <input type="text" name="new_client_full_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Client name">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">National ID</label>
                                    <input type="text" name="new_client_national_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="National ID">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" name="new_client_email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Email">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                                    <input type="text" name="new_client_phone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Phone">
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end">
                            <button type="button" id="step1Next" class="px-6 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700" disabled>Next</button>
                        </div>
                    </div>
                    <!-- Step 2: Inspection Details -->
                    <div id="step-2" class="hidden">
                        <h2 class="text-lg font-semibold mb-4">2. Inspection Details</h2>
                        <div id="propertySummary" class="mb-4 p-4 bg-gray-50 border rounded hidden"></div>
                        <div class="mb-4">
                            <label for="client_id" class="block text-sm font-medium text-gray-700">Client (Optional)</label>
                            <select name="client_id" id="client_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">No specific client</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->full_name }} ({{ $client->national_id ?? 'No ID' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="preferred_date" class="block text-sm font-medium text-gray-700">Preferred Inspection Date</label>
                            <input type="date" name="preferred_date" id="preferred_date" min="{{ date('Y-m-d') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div class="mb-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                            <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Any special requirements or notes for the inspection..."></textarea>
                        </div>
                        <div class="mt-6 flex justify-between">
                            <button type="button" id="step2Back" class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Back</button>
                            <button type="button" id="step2Next" class="px-6 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Next</button>
                        </div>
                    </div>
                    <!-- Step 3: Review & Submit -->
                    <div id="step-3" class="hidden">
                        <h2 class="text-lg font-semibold mb-4">3. Review & Submit</h2>
                        <div id="reviewSummary" class="mb-6 p-4 bg-gray-50 border rounded"></div>
                        <div class="mt-6 flex justify-between">
                            <button type="button" id="step3Back" class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Back</button>
                            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">Submit Request</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Stepper logic and property search
let currentStep = 1;
const stepCount = 3;
const stepperBar = document.getElementById('stepper-bar');
const stepperLabel = document.getElementById('stepper-label');
function showStep(step) {
    for (let i = 1; i <= stepCount; i++) {
        document.getElementById('step-' + i).classList.add('hidden');
    }
    document.getElementById('step-' + step).classList.remove('hidden');
    stepperBar.style.width = (step * 100 / stepCount) + '%';
    stepperLabel.textContent = 'Step ' + step + ' of ' + stepCount;
}
// Property search
const propertySearch = document.getElementById('propertySearch');
const propertyRows = document.querySelectorAll('.property-row');
propertySearch.addEventListener('input', function() {
    const term = this.value.toLowerCase();
    propertyRows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(term) ? '' : 'none';
    });
});
// Property selection
let selectedPropertyId = null;
propertyRows.forEach(row => {
    row.addEventListener('click', function(e) {
        if (e.target.tagName !== 'INPUT') {
            this.querySelector('input[type=radio]').checked = true;
        }
        propertyRows.forEach(r => r.classList.remove('bg-indigo-50', 'ring-2', 'ring-indigo-400'));
        this.classList.add('bg-indigo-50', 'ring-2', 'ring-indigo-400');
        selectedPropertyId = this.getAttribute('data-id');
        document.getElementById('step1Next').disabled = false;
        document.getElementById('newPropertyForm').classList.add('hidden');

        // Autofill client dropdown with owner if available
        const ownerName = this.children[2].textContent.trim();
        const ownerEmail = this.getAttribute('data-owner-email');
        const clientSelect = document.getElementById('client_id');
        if (clientSelect) {
            // Remove previous auto-added option
            const autoOption = clientSelect.querySelector('option[data-auto-client]');
            if (autoOption) autoOption.remove();
            if (ownerName && ownerEmail) {
                // Add owner as an option if not already present
                let exists = false;
                for (let i = 0; i < clientSelect.options.length; i++) {
                    if (clientSelect.options[i].text.includes(ownerName) || clientSelect.options[i].value === ownerEmail) {
                        exists = true;
                        break;
                    }
                }
                if (!exists) {
                    const opt = document.createElement('option');
                    opt.value = '';
                    opt.text = ownerName + ' (' + ownerEmail + ')';
                    opt.setAttribute('data-auto-client', '1');
                    opt.disabled = true;
                    opt.selected = true;
                    clientSelect.insertBefore(opt, clientSelect.firstChild.nextSibling);
                }
            }
        }
    });
});
// Show new property form
const showNewPropertyFormBtn = document.getElementById('showNewPropertyForm');
if (showNewPropertyFormBtn) {
    showNewPropertyFormBtn.addEventListener('click', function() {
        console.log('Create New Property button clicked');
        propertyRows.forEach(r => r.querySelector('input[type=radio]').checked = false);
        propertyRows.forEach(r => r.classList.remove('bg-indigo-50', 'ring-2', 'ring-indigo-400'));
        selectedPropertyId = null;
        document.getElementById('step1Next').disabled = false;
        document.getElementById('newPropertyForm').classList.remove('hidden');
    });
} else {
    console.error('Create New Property button not found!');
}
// Step 1 Next
if (document.getElementById('step1Next')) {
    document.getElementById('step1Next').onclick = function() {
        if (!selectedPropertyId && document.getElementById('newPropertyForm').classList.contains('hidden')) {
            alert('Please select a property or create a new one.');
            return;
        }
        
        // Validate new property form if it's visible
        if (!selectedPropertyId && !document.getElementById('newPropertyForm').classList.contains('hidden')) {
            const requiredFields = [
                'new_property_address',
                'new_property_district', 
                'new_client_full_name',
                'new_client_email',
                'new_client_phone'
            ];
            
            let missingFields = [];
            requiredFields.forEach(field => {
                const value = document.querySelector(`input[name="${field}"]`).value.trim();
                if (!value) {
                    missingFields.push(field.replace('new_', '').replace('_', ' '));
                }
            });
            
            if (missingFields.length > 0) {
                alert('Please fill in all required fields: ' + missingFields.join(', '));
                return;
            }
        }
        
        showStep(2);
        currentStep = 2;
        // Show property summary if existing property
        if (selectedPropertyId) {
            const selectedRow = document.querySelector('.property-row[data-id="' + selectedPropertyId + '"]');
            document.getElementById('propertySummary').classList.remove('hidden');
            document.getElementById('propertySummary').innerHTML = '<strong>Property:</strong> ' + selectedRow.children[0].textContent + ' - ' + selectedRow.children[1].textContent + ' (' + selectedRow.children[2].textContent + ')';
        } else {
            document.getElementById('propertySummary').classList.add('hidden');
        }
    };
}
// Step 2 Back/Next
if (document.getElementById('step2Back')) {
    document.getElementById('step2Back').onclick = function() {
        showStep(1);
        currentStep = 1;
    };
}
if (document.getElementById('step2Next')) {
    const preferredDateInput = document.getElementById('preferred_date');
    const step2NextBtn = document.getElementById('step2Next');
    function checkStep2Next() {
        step2NextBtn.disabled = !preferredDateInput.value;
    }
    if (preferredDateInput) {
        preferredDateInput.addEventListener('input', checkStep2Next);
        checkStep2Next();
    }
    step2NextBtn.onclick = function() {
        if (!preferredDateInput.value) {
            alert('Please select a preferred inspection date.');
            return;
        }
        // Optionally validate other step 2 fields here
        showStep(3);
        currentStep = 3;
        // Fill review summary
        let summary = '';
        if (selectedPropertyId) {
            const selectedRow = document.querySelector('.property-row[data-id="' + selectedPropertyId + '"]');
            summary += '<div><strong>Property:</strong> ' + selectedRow.children[0].textContent + ' - ' + selectedRow.children[1].textContent + ' (' + selectedRow.children[2].textContent + ')</div>';
        } else {
            // New property
            const newAddress = document.querySelector('input[name="new_property_address"]').value;
            const newCode = document.querySelector('input[name="new_property_code"]').value;
            const newClient = document.querySelector('input[name="new_client_full_name"]').value;
            summary += '<div><strong>Property:</strong> ' + (newCode || 'Auto-generated') + ' - ' + newAddress + ' (' + newClient + ')</div>';
        }
        const clientSelect = document.getElementById('client_id');
        const selectedClient = clientSelect.options[clientSelect.selectedIndex];
        summary += '<div><strong>Client:</strong> ' + (selectedClient ? selectedClient.text : 'Not specified') + '</div>';
        summary += '<div><strong>Date:</strong> ' + preferredDateInput.value + '</div>';
        summary += '<div><strong>Notes:</strong> ' + document.getElementById('notes').value + '</div>';
        document.getElementById('reviewSummary').innerHTML = summary;
    };
}
// Step 3 Back
if (document.getElementById('step3Back')) {
    document.getElementById('step3Back').onclick = function() {
        showStep(2);
        currentStep = 2;
    };
}
showStep(1);

// Debug: Check if all elements exist
console.log('Debug: Checking elements...');
console.log('showNewPropertyForm button:', document.getElementById('showNewPropertyForm'));
console.log('newPropertyForm div:', document.getElementById('newPropertyForm'));
console.log('step1Next button:', document.getElementById('step1Next'));

// Fallback: Try to find button by text content if ID doesn't work
if (!document.getElementById('showNewPropertyForm')) {
    const buttons = document.querySelectorAll('button');
    buttons.forEach(button => {
        if (button.textContent.includes('Create New Property')) {
            console.log('Found Create New Property button by text content');
            button.addEventListener('click', function() {
                console.log('Create New Property button clicked (fallback)');
                propertyRows.forEach(r => r.querySelector('input[type=radio]').checked = false);
                propertyRows.forEach(r => r.classList.remove('bg-indigo-50', 'ring-2', 'ring-indigo-400'));
                selectedPropertyId = null;
                document.getElementById('step1Next').disabled = false;
                document.getElementById('newPropertyForm').classList.remove('hidden');
            });
        }
    });
}
</script>
@endpush
@endsection 