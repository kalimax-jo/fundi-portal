{{-- File Path: resources/views/inspection-requests/create.blade.php --}}
{{-- This version works with your current form without needing API changes --}}

@extends('layouts.app')

@section('title', 'Request Inspection')

@push('styles')
<style>
.auto-filled {
    background-color: #f0fdf4 !important;
    border-color: #22c55e !important;
    transition: all 0.3s ease;
}

.property-info-display {
    background-color: #e0f2fe;
    border: 1px solid #0ea5e9;
    border-radius: 0.375rem;
    padding: 0.75rem;
    margin-top: 0.5rem;
}

.search-highlight {
    background-color: #fef3c7;
    padding: 2px 4px;
    border-radius: 2px;
    font-weight: 500;
}
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <h2 class="text-xl font-semibold mb-4">Request Inspection</h2>
    
    <form method="POST" action="{{ route('inspection-requests.store') }}" class="space-y-6">
        @csrf

        @if(isset($isIndividual) && !$isIndividual)
            <!-- Enhanced Property Selection -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="property_id" class="block text-sm font-medium text-gray-700">
                        Property <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center space-x-4">
                        <input type="text" 
                               id="property_filter" 
                               placeholder="Type to filter properties..."
                               class="text-sm px-3 py-1 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                               style="min-width: 200px;">
                        <button type="button" id="clear_filter" class="text-xs text-blue-600 hover:text-blue-800 hidden">
                            Clear Filter
                        </button>
                    </div>
                </div>
                
                <select name="property_id" id="property_id" required class="mt-1 block w-full border-gray-300 rounded-md" onchange="handlePropertySelection()">
                    <option value="">Choose property...</option>
                    @foreach($properties as $property)
                        <option value="{{ $property->id }}" 
                                data-owner-name="{{ $property->owner_name }}"
                                data-owner-phone="{{ $property->owner_phone }}"
                                data-owner-email="{{ $property->owner_email }}"
                                data-address="{{ $property->address }}"
                                data-district="{{ $property->district }}"
                                data-property-type="{{ $property->property_type }}"
                                data-search-text="{{ strtolower($property->property_code . ' ' . $property->owner_name . ' ' . $property->address . ' ' . $property->district) }}"
                                {{ old('property_id') == $property->id ? 'selected' : '' }}>
                            {{ $property->property_code }} - {{ $property->owner_name }} ({{ $property->address }})
                        </option>
                    @endforeach
                </select>
                @error('property_id')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                
                <!-- Selected Property Info -->
                <div id="selected_property_info" class="property-info-display hidden">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="font-medium text-blue-900">Selected Property Details</h4>
                            <div id="property_details" class="mt-1 text-sm text-blue-700 space-y-1">
                                <!-- Property details will be populated here -->
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Auto-filled ✓
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Auto-filled Applicant Information -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h3 class="text-sm font-medium text-blue-900">Applicant Information</h3>
                        <p class="text-xs text-blue-700 mt-1">Auto-filled from property owner details</p>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="use_different_applicant" 
                               onchange="toggleApplicantFields()"
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="use_different_applicant" class="ml-2 text-xs text-blue-700">
                            Use different applicant
                        </label>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="applicant_name" class="block text-xs font-medium text-gray-700">Applicant Name</label>
                        <input type="text" 
                               name="applicant_name" 
                               id="applicant_name" 
                               value="{{ old('applicant_name') }}"
                               readonly
                               placeholder="Will be auto-filled when property is selected"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-sm">
                        @error('applicant_name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="applicant_phone" class="block text-xs font-medium text-gray-700">Applicant Phone</label>
                        <input type="text" 
                               name="applicant_phone" 
                               id="applicant_phone" 
                               value="{{ old('applicant_phone') }}"
                               readonly
                               placeholder="Will be auto-filled when property is selected"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-sm">
                        @error('applicant_phone')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <!-- Auto-fill Status -->
                <div id="autofill_status" class="mt-3 p-2 bg-blue-100 border border-blue-300 rounded hidden">
                    <div class="flex items-center">
                        <svg class="h-4 w-4 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <p class="text-xs text-blue-700">
                            <strong>Auto-filled successfully!</strong> Applicant details populated from property owner.
                        </p>
                    </div>
                </div>
            </div>

        @else
            <!-- Individual User Property Fields -->
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700">Property Address</label>
                <input type="text" name="address" id="address" value="{{ old('address') }}" class="mt-1 block w-full border-gray-300 rounded-md" required>
                @error('address')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="district" class="block text-sm font-medium text-gray-700">District</label>
                <select name="district" id="district" required class="mt-1 block w-full border-gray-300 rounded-md">
                    <option value="">Choose district...</option>
                    @foreach($districts as $district)
                        <option value="{{ $district }}" {{ old('district') == $district ? 'selected' : '' }}>{{ $district }}</option>
                    @endforeach
                </select>
                @error('district')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="property_type" class="block text-sm font-medium text-gray-700">Property Type</label>
                <select name="property_type" id="property_type" required class="mt-1 block w-full border-gray-300 rounded-md">
                    <option value="">Choose type...</option>
                    @foreach($propertyTypes as $type => $label)
                        <option value="{{ $type }}" {{ old('property_type') == $type ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('property_type')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        @endif

        <!-- Package Selection -->
        <div>
            <label for="package_id" class="block text-sm font-medium text-gray-700">Inspection Package</label>
            <select name="package_id" id="package_id" required class="mt-1 block w-full border-gray-300 rounded-md" onchange="updatePackageInfo()">
                <option value="">Choose a package...</option>
                @foreach($packages as $package)
                    <option value="{{ $package->id }}" 
                            data-price="{{ $package->price }}" 
                            data-duration="{{ $package->estimated_duration ?? 2 }}"
                            {{ old('package_id') == $package->id ? 'selected' : '' }}>
                        {{ $package->display_name ?? $package->name }} - {{ number_format($package->price) }} RWF
                    </option>
                @endforeach
            </select>
            @error('package_id')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            
            <!-- Package Info Display -->
            <div id="package_info" class="mt-2 p-3 bg-green-50 border border-green-200 rounded-md hidden">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-900">Package Details</p>
                        <div class="mt-1 text-sm text-green-700">
                            <p><strong>Price:</strong> <span id="package_price">-</span> RWF</p>
                            <p><strong>Estimated Duration:</strong> <span id="package_duration">-</span> hours</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purpose -->
        <div>
            <label for="purpose" class="block text-sm font-medium text-gray-700">Purpose</label>
            <select name="purpose" id="purpose" required class="mt-1 block w-full border-gray-300 rounded-md" onchange="toggleLoanFields()">
                <option value="">Select purpose</option>
                <option value="rental" {{ old('purpose') == 'rental' ? 'selected' : '' }}>Rental</option>
                <option value="sale" {{ old('purpose') == 'sale' ? 'selected' : '' }}>Sale</option>
                <option value="purchase" {{ old('purpose') == 'purchase' ? 'selected' : '' }}>Purchase</option>
                <option value="loan_collateral" {{ old('purpose') == 'loan_collateral' ? 'selected' : '' }}>Loan Collateral</option>
                <option value="insurance" {{ old('purpose') == 'insurance' ? 'selected' : '' }}>Insurance</option>
                <option value="maintenance" {{ old('purpose') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                <option value="other" {{ old('purpose') == 'other' ? 'selected' : '' }}>Other</option>
            </select>
            @error('purpose')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <!-- Urgency -->
        <div>
            <label for="urgency" class="block text-sm font-medium text-gray-700">Urgency</label>
            <select name="urgency" id="urgency" required class="mt-1 block w-full border-gray-300 rounded-md">
                <option value="normal" {{ old('urgency', 'normal') == 'normal' ? 'selected' : '' }}>Normal</option>
                <option value="urgent" {{ old('urgency') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                <option value="emergency" {{ old('urgency') == 'emergency' ? 'selected' : '' }}>Emergency</option>
            </select>
            @error('urgency')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <!-- Scheduling -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="preferred_date" class="block text-sm font-medium text-gray-700">Preferred Date</label>
                <input type="date" 
                       name="preferred_date" 
                       id="preferred_date" 
                       value="{{ old('preferred_date') }}"
                       min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md">
                @error('preferred_date')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="preferred_time_slot" class="block text-sm font-medium text-gray-700">Preferred Time</label>
                <select name="preferred_time_slot" id="preferred_time_slot" required class="mt-1 block w-full border-gray-300 rounded-md">
                    <option value="flexible" {{ old('preferred_time_slot', 'flexible') == 'flexible' ? 'selected' : '' }}>Flexible</option>
                    <option value="morning" {{ old('preferred_time_slot') == 'morning' ? 'selected' : '' }}>Morning (8:00 - 12:00)</option>
                    <option value="afternoon" {{ old('preferred_time_slot') == 'afternoon' ? 'selected' : '' }}>Afternoon (12:00 - 17:00)</option>
                    <option value="evening" {{ old('preferred_time_slot') == 'evening' ? 'selected' : '' }}>Evening (17:00 - 20:00)</option>
                </select>
                @error('preferred_time_slot')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <!-- Loan Information (conditionally shown) -->
        <div id="loan_information" class="hidden">
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <h3 class="text-sm font-medium text-yellow-900 mb-3">Loan Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="loan_amount" class="block text-sm font-medium text-gray-700">Loan Amount (RWF)</label>
                        <input type="number" 
                               name="loan_amount" 
                               id="loan_amount" 
                               value="{{ old('loan_amount') }}"
                               min="0"
                               step="1000"
                               placeholder="e.g., 10000000"
                               class="mt-1 block w-full border-gray-300 rounded-md">
                        @error('loan_amount')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="loan_reference" class="block text-sm font-medium text-gray-700">Loan Reference</label>
                        <input type="text" 
                               name="loan_reference" 
                               id="loan_reference" 
                               value="{{ old('loan_reference') }}"
                               placeholder="e.g., LN-2025-001"
                               class="mt-1 block w-full border-gray-300 rounded-md">
                        @error('loan_reference')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Special Instructions -->
        <div>
            <label for="special_instructions" class="block text-sm font-medium text-gray-700">Special Instructions</label>
            <textarea name="special_instructions" 
                      id="special_instructions" 
                      rows="3" 
                      placeholder="Any special requirements or instructions for the inspector..."
                      class="mt-1 block w-full border-gray-300 rounded-md">{{ old('special_instructions') }}</textarea>
            @error('special_instructions')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('dashboard') }}" 
               class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cancel
            </a>
            <button type="submit" 
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Create Inspection Request
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
const isIndividual = {{ $isIndividual ? 'true' : 'false' }};

// Initialize form
document.addEventListener('DOMContentLoaded', function() {
    if (!isIndividual) {
        setupPropertyFilter();
        
        // Auto-fill if there's a pre-selected value
        const propertySelect = document.getElementById('property_id');
        if (propertySelect.value) {
            handlePropertySelection();
        }
    }
    
    updatePackageInfo();
    toggleLoanFields();
});

// Property filtering functionality
function setupPropertyFilter() {
    const filterInput = document.getElementById('property_filter');
    const propertySelect = document.getElementById('property_id');
    const clearButton = document.getElementById('clear_filter');
    const allOptions = Array.from(propertySelect.options);

    filterInput.addEventListener('input', function() {
        const filterText = this.value.toLowerCase();
        
        if (filterText.length === 0) {
            // Show all options
            showAllOptions();
            clearButton.classList.add('hidden');
            return;
        }
        
        clearButton.classList.remove('hidden');
        
        // Clear current options (except first)
        propertySelect.innerHTML = '<option value="">Choose property...</option>';
        
        // Filter and add matching options
        const matchingOptions = allOptions.slice(1).filter(option => {
            const searchText = option.getAttribute('data-search-text') || '';
            return searchText.includes(filterText);
        });
        
        matchingOptions.forEach(option => {
            // Highlight matching text
            const originalText = option.textContent;
            const highlightedText = highlightSearchTerm(originalText, filterText);
            
            const newOption = option.cloneNode(true);
            newOption.innerHTML = highlightedText;
            propertySelect.appendChild(newOption);
        });
        
        if (matchingOptions.length === 0) {
            const noResultOption = document.createElement('option');
            noResultOption.value = '';
            noResultOption.textContent = 'No properties found matching your search';
            noResultOption.disabled = true;
            propertySelect.appendChild(noResultOption);
        }
    });
    
    clearButton.addEventListener('click', function() {
        filterInput.value = '';
        showAllOptions();
        this.classList.add('hidden');
        filterInput.focus();
    });
    
    function showAllOptions() {
        propertySelect.innerHTML = '';
        allOptions.forEach(option => {
            propertySelect.appendChild(option.cloneNode(true));
        });
    }
    
    function highlightSearchTerm(text, term) {
        if (!term) return text;
        const regex = new RegExp(`(${term})`, 'gi');
        return text.replace(regex, '<span class="search-highlight">$1</span>');
    }
}

// Handle property selection and auto-fill
function handlePropertySelection() {
    const select = document.getElementById('property_id');
    const selectedOption = select.options[select.selectedIndex];
    
    if (!selectedOption.value) {
        clearApplicantInfo();
        hidePropertyInfo();
        return;
    }
    
    // Get property data
    const propertyData = {
        code: selectedOption.textContent.split(' - ')[0],
        ownerName: selectedOption.getAttribute('data-owner-name'),
        ownerPhone: selectedOption.getAttribute('data-owner-phone'),
        ownerEmail: selectedOption.getAttribute('data-owner-email'),
        address: selectedOption.getAttribute('data-address'),
        district: selectedOption.getAttribute('data-district'),
        propertyType: selectedOption.getAttribute('data-property-type')
    };
    
    // Auto-fill applicant information
    autoFillApplicantInfo(propertyData.ownerName, propertyData.ownerPhone);
    
    // Show property information
    showPropertyInfo(propertyData);
    
    // Show auto-fill status
    showAutoFillStatus();
}

function autoFillApplicantInfo(ownerName, ownerPhone) {
    const applicantNameField = document.getElementById('applicant_name');
    const applicantPhoneField = document.getElementById('applicant_phone');
    
    if (applicantNameField && applicantPhoneField) {
        applicantNameField.value = ownerName || '';
        applicantPhoneField.value = ownerPhone || '';
        
        // Add visual feedback
        applicantNameField.classList.add('auto-filled');
        applicantPhoneField.classList.add('auto-filled');
        
        // Remove visual feedback after 3 seconds
        setTimeout(() => {
            applicantNameField.classList.remove('auto-filled');
            applicantPhoneField.classList.remove('auto-filled');
        }, 3000);
    }
}

function clearApplicantInfo() {
    const applicantNameField = document.getElementById('applicant_name');
    const applicantPhoneField = document.getElementById('applicant_phone');
    
    if (applicantNameField && applicantPhoneField) {
        applicantNameField.value = '';
        applicantPhoneField.value = '';
        applicantNameField.placeholder = 'Will be auto-filled when property is selected';
        applicantPhoneField.placeholder = 'Will be auto-filled when property is selected';
    }
    
    hideAutoFillStatus();
}

function showPropertyInfo(propertyData) {
    const container = document.getElementById('selected_property_info');
    const detailsContainer = document.getElementById('property_details');
    
    detailsContainer.innerHTML = `
        <div class="space-y-1">
            <p><strong>Code:</strong> ${propertyData.code}</p>
            <p><strong>Owner:</strong> ${propertyData.ownerName}</p>
            <p><strong>Phone:</strong> ${propertyData.ownerPhone || 'Not provided'}</p>
            <p><strong>Email:</strong> ${propertyData.ownerEmail || 'Not provided'}</p>
            <p><strong>Address:</strong> ${propertyData.address || 'Not provided'}</p>
            <p><strong>District:</strong> ${propertyData.district || 'Not provided'}</p>
            <p><strong>Type:</strong> ${propertyData.propertyType || 'Not specified'}</p>
        </div>
    `;
    
    container.classList.remove('hidden');
}

function hidePropertyInfo() {
    document.getElementById('selected_property_info').classList.add('hidden');
}

function showAutoFillStatus() {
    document.getElementById('autofill_status').classList.remove('hidden');
    
    // Hide after 5 seconds
    setTimeout(() => {
        hideAutoFillStatus();
    }, 5000);
}

function hideAutoFillStatus() {
    document.getElementById('autofill_status').classList.add('hidden');
}

function toggleApplicantFields() {
    const checkbox = document.getElementById('use_different_applicant');
    const nameField = document.getElementById('applicant_name');
    const phoneField = document.getElementById('applicant_phone');
    
    if (checkbox.checked) {
        // Allow editing
        nameField.removeAttribute('readonly');
        phoneField.removeAttribute('readonly');
        nameField.classList.remove('bg-gray-50');
        phoneField.classList.remove('bg-gray-50');
        nameField.classList.add('bg-white');
        phoneField.classList.add('bg-white');
        nameField.placeholder = 'Enter applicant name';
        phoneField.placeholder = 'Enter applicant phone';
        nameField.focus();
    } else {
        // Make readonly and auto-fill from property
        nameField.setAttribute('readonly', 'readonly');
        phoneField.setAttribute('readonly', 'readonly');
        nameField.classList.remove('bg-white');
        phoneField.classList.remove('bg-white');
        nameField.classList.add('bg-gray-50');
        phoneField.classList.add('bg-gray-50');
        
        // Re-auto-fill from selected property
        const propertySelect = document.getElementById('property_id');
        if (propertySelect.value) {
            handlePropertySelection();
        } else {
            clearApplicantInfo();
        }
    }
}

function toggleLoanFields() {
    const purpose = document.getElementById('purpose').value;
    const loanInfo = document.getElementById('loan_information');
    
    if (purpose === 'loan_collateral') {
        loanInfo.classList.remove('hidden');
    } else {
        loanInfo.classList.add('hidden');
    }
}

function updatePackageInfo() {
    const packageSelect = document.getElementById('package_id');
    const packageInfo = document.getElementById('package_info');
    const priceSpan = document.getElementById('package_price');
    const durationSpan = document.getElementById('package_duration');
    
    if (packageSelect.value) {
        const selectedOption = packageSelect.options[packageSelect.selectedIndex];
        const price = selectedOption.getAttribute('data-price');
        const duration = selectedOption.getAttribute('data-duration');
        
        priceSpan.textContent = price ? new Intl.NumberFormat().format(price) : '-';
        durationSpan.textContent = duration || '2';
        packageInfo.classList.remove('hidden');
    } else {
        packageInfo.classList.add('hidden');
    }
}

// Form validation before submit
document.querySelector('form').addEventListener('submit', function(e) {
    if (!isIndividual) {
        const propertyId = document.getElementById('property_id').value;
        if (!propertyId) {
            e.preventDefault();
            alert('Please select a property before submitting the form.');
            document.getElementById('property_id').focus();
            return false;
        }
        
        // Check if applicant fields are filled
        const applicantName = document.getElementById('applicant_name').value.trim();
        const applicantPhone = document.getElementById('applicant_phone').value.trim();
        
        if (!applicantName || !applicantPhone) {
            e.preventDefault();
            alert('Applicant name and phone are required. Please select a property with owner details or use different applicant option.');
            return false;
        }
    }
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + F to focus on property filter
    if ((e.ctrlKey || e.metaKey) && e.key === 'f' && !isIndividual) {
        e.preventDefault();
        document.getElementById('property_filter').focus();
    }
});

// Initialize tooltips or help text
if (!isIndividual) {
    // Add helpful hints
    const propertySelect = document.getElementById('property_id');
    propertySelect.addEventListener('focus', function() {
        if (!this.value) {
            console.log('Tip: Use the filter box above to quickly find properties');
        }
    });
}
</script>
@endpush

@endsection