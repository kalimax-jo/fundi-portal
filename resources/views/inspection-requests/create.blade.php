{{-- File Path: resources/views/admin/inspection-requests/create.blade.php --}}

@extends('layouts.admin')

@section('title', 'Create Inspection Request')

@push('styles')
<style>
.property-search-container {
    position: relative;
}

.property-search-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 1000;
    background: white;
    border: 1px solid #d1d5db;
    border-top: none;
    border-radius: 0 0 0.375rem 0.375rem;
    max-height: 300px;
    overflow-y: auto;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.property-search-item {
    padding: 0.75rem 1rem;
    cursor: pointer;
    border-bottom: 1px solid #f3f4f6;
}

.property-search-item:hover {
    background-color: #f9fafb;
}

.property-search-item:last-child {
    border-bottom: none;
}

.auto-filled {
    background-color: #f0fdf4 !important;
    border-color: #22c55e !important;
    transition: all 0.3s ease;
}

.loading-spinner {
    border: 2px solid #f3f3f3;
    border-top: 2px solid #3498db;
    border-radius: 50%;
    width: 16px;
    height: 16px;
    animation: spin 1s linear infinite;
    display: inline-block;
    margin-right: 8px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.selected-property-info {
    background-color: #e0f2fe;
    border-color: #0ea5e9;
    padding: 1rem;
    border-radius: 0.5rem;
    margin-top: 0.5rem;
}
</style>
@endpush

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Create Inspection Request
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <a href="{{ route('admin.inspection-requests.index') }}" class="text-indigo-600 hover:text-indigo-500">Inspection Requests</a>
                <span class="mx-2">/</span>
                <span>Create Request</span>
            </div>
        </div>
    </div>
    <div class="mt-4 flex space-x-3 md:ml-4 md:mt-0">
        <a href="{{ route('admin.inspection-requests.index') }}" 
           class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 010-1.06L10.94 8.25H4a.75.75 0 010-1.5h6.94L7.72 3.53a.75.75 0 011.06-1.06l4.5 4.5a.75.75 0 010 1.06l-4.5 4.5a.75.75 0 01-1.06 0z" clip-rule="evenodd" />
            </svg>
            Back to Requests
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.inspection-requests.store') }}" method="POST" class="space-y-8">
        @csrf

        <!-- Basic Information -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Basic Information</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Basic details about the inspection request.
                    </p>
                </div>
                <div class="mt-5 md:col-span-2 md:mt-0">
                    <div class="grid grid-cols-6 gap-6">
                        <!-- Requester Type -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="requester_type" class="block text-sm font-medium text-gray-700">Requester Type *</label>
                            <select name="requester_type" id="requester_type" required onchange="toggleRequesterFields()"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Select Requester Type</option>
                                <option value="individual" {{ old('requester_type') == 'individual' ? 'selected' : '' }}>Individual Client</option>
                                <option value="business_partner" {{ old('requester_type') == 'business_partner' ? 'selected' : '' }}>Business Partner</option>
                            </select>
                            @error('requester_type')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Individual User -->
                        <div id="individual_user_field" class="col-span-6 sm:col-span-3 hidden">
                            <label for="requester_user_id" class="block text-sm font-medium text-gray-700">Select User *</label>
                            <select name="requester_user_id" id="requester_user_id"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Choose a user...</option>
                                @foreach($individualUsers as $user)
                                <option value="{{ $user->id }}" {{ old('requester_user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->full_name }} - {{ $user->email }}
                                </option>
                                @endforeach
                            </select>
                            @error('requester_user_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Business Partner -->
                        <div id="business_partner_field" class="col-span-6 sm:col-span-3 hidden">
                            <label for="business_partner_id" class="block text-sm font-medium text-gray-700">Business Partner *</label>
                            <select name="business_partner_id" id="business_partner_id" onchange="updatePartnerUsers()"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Choose a partner...</option>
                                @foreach($businessPartners as $partner)
                                <option value="{{ $partner->id }}" {{ old('business_partner_id') == $partner->id ? 'selected' : '' }}>
                                    {{ $partner->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('business_partner_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Partner User -->
                        <div id="partner_user_field" class="col-span-6 sm:col-span-3 hidden">
                            <label for="partner_user_id" class="block text-sm font-medium text-gray-700">Partner User *</label>
                            <select name="partner_user_id" id="partner_user_id"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Choose a user...</option>
                            </select>
                            @error('partner_user_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Property Search -->
                        <div class="col-span-6">
                            <label for="property_search" class="block text-sm font-medium text-gray-700 mb-2">
                                🔍 Search Property *
                            </label>
                            <div class="property-search-container">
                                <div class="relative">
                                    <input type="text" 
                                           id="property_search" 
                                           placeholder="Type property code, owner name, address, or district to search..."
                                           class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <div id="search_loading" class="absolute inset-y-0 right-0 pr-3 flex items-center hidden">
                                        <div class="loading-spinner"></div>
                                    </div>
                                </div>
                                
                                <!-- Search Results -->
                                <div id="property_search_results" class="property-search-results hidden"></div>
                                
                                <!-- Hidden input for selected property -->
                                <input type="hidden" name="property_id" id="property_id" value="{{ old('property_id', request('property_id')) }}">
                            </div>
                            @error('property_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            
                            <!-- Selected Property Display -->
                            <div id="selected_property_info" class="selected-property-info hidden">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="text-sm font-medium text-blue-900">✅ Selected Property</h4>
                                        <div class="mt-2 text-sm text-blue-700" id="selected_property_details">
                                            <!-- Property details will be populated here -->
                                        </div>
                                    </div>
                                    <button type="button" 
                                            onclick="clearPropertySelection()" 
                                            class="text-xs text-blue-600 hover:text-blue-800 underline">
                                        Change
                                    </button>
                                </div>
                            </div>

                            <!-- Quick add property link -->
                            <p class="mt-2 text-xs text-gray-500">
                                💡 Can't find the property? 
                                <a href="{{ route('admin.properties.create') }}" 
                                   target="_blank"
                                   class="text-indigo-600 hover:text-indigo-500 underline">
                                    Add a new property
                                </a>
                                then refresh this page.
                            </p>
                        </div>

                        <!-- Inspection Package -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="package_id" class="block text-sm font-medium text-gray-700">Inspection Package *</label>
                            <select name="package_id" id="package_id" required onchange="updatePackageInfo()"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Choose a package...</option>
                                @foreach($packages as $package)
                                <option value="{{ $package->id }}" 
                                        data-price="{{ $package->price }}" 
                                        data-duration="{{ $package->estimated_duration }}"
                                        {{ old('package_id') == $package->id ? 'selected' : '' }}>
                                    {{ $package->name }} - {{ number_format($package->price) }} RWF
                                </option>
                                @endforeach
                            </select>
                            @error('package_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Purpose -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="purpose" class="block text-sm font-medium text-gray-700">Purpose *</label>
                            <select name="purpose" id="purpose" required onchange="toggleLoanFields()"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Select Purpose</option>
                                <option value="rental" {{ old('purpose') == 'rental' ? 'selected' : '' }}>Rental Agreement</option>
                                <option value="sale" {{ old('purpose') == 'sale' ? 'selected' : '' }}>Property Sale</option>
                                <option value="purchase" {{ old('purpose') == 'purchase' ? 'selected' : '' }}>Property Purchase</option>
                                <option value="loan_collateral" {{ old('purpose') == 'loan_collateral' ? 'selected' : '' }}>Loan Collateral</option>
                                <option value="insurance" {{ old('purpose') == 'insurance' ? 'selected' : '' }}>Insurance Assessment</option>
                                <option value="maintenance" {{ old('purpose') == 'maintenance' ? 'selected' : '' }}>Maintenance Check</option>
                                <option value="other" {{ old('purpose') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('purpose')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Urgency -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="urgency" class="block text-sm font-medium text-gray-700">Urgency *</label>
                            <select name="urgency" id="urgency" required
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="normal" {{ old('urgency', 'normal') == 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="urgent" {{ old('urgency') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                <option value="emergency" {{ old('urgency') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                            </select>
                            @error('urgency')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Applicant Information (Auto-filled) -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">📋 Applicant Information</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Contact details (auto-filled from property owner).
                    </p>
                    <div class="mt-3">
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   id="use_different_applicant" 
                                   onchange="toggleApplicantFields()"
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Use different applicant</span>
                        </label>
                    </div>
                </div>
                <div class="mt-5 md:col-span-2 md:mt-0">
                    <div class="grid grid-cols-6 gap-6">
                        <!-- Applicant Name -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="applicant_name" class="block text-sm font-medium text-gray-700">Applicant Name</label>
                            <input type="text" name="applicant_name" id="applicant_name" 
                                   value="{{ old('applicant_name') }}"
                                   readonly
                                   placeholder="Will auto-fill when property is selected"
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-50">
                            @error('applicant_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Applicant Phone -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="applicant_phone" class="block text-sm font-medium text-gray-700">Applicant Phone</label>
                            <input type="text" name="applicant_phone" id="applicant_phone" 
                                   value="{{ old('applicant_phone') }}"
                                   readonly
                                   placeholder="Will auto-fill when property is selected"
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-50">
                            @error('applicant_phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Auto-fill Notice -->
                    <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    ✨ <strong>Smart Auto-fill:</strong> When you select a property, applicant details automatically fill with the property owner's information. 
                                    Check "Use different applicant" if needed.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scheduling -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Scheduling</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Preferred date and time for the inspection.
                    </p>
                </div>
                <div class="mt-5 md:col-span-2 md:mt-0">
                    <div class="grid grid-cols-6 gap-6">
                        <!-- Preferred Date -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="preferred_date" class="block text-sm font-medium text-gray-700">Preferred Date</label>
                            <input type="date" name="preferred_date" id="preferred_date" 
                                   value="{{ old('preferred_date') }}"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('preferred_date')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Preferred Time -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="preferred_time_slot" class="block text-sm font-medium text-gray-700">Preferred Time</label>
                            <select name="preferred_time_slot" id="preferred_time_slot" required
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="flexible" {{ old('preferred_time_slot', 'flexible') == 'flexible' ? 'selected' : '' }}>Flexible</option>
                                <option value="morning" {{ old('preferred_time_slot') == 'morning' ? 'selected' : '' }}>Morning (8:00 - 12:00)</option>
                                <option value="afternoon" {{ old('preferred_time_slot') == 'afternoon' ? 'selected' : '' }}>Afternoon (12:00 - 17:00)</option>
                                <option value="evening" {{ old('preferred_time_slot') == 'evening' ? 'selected' : '' }}>Evening (17:00 - 20:00)</option>
                            </select>
                            @error('preferred_time_slot')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Special Instructions -->
                        <div class="col-span-6">
                            <label for="special_instructions" class="block text-sm font-medium text-gray-700">Special Instructions</label>
                            <textarea name="special_instructions" id="special_instructions" rows="3" 
                                      placeholder="Any special requirements or instructions for the inspector..."
                                      class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('special_instructions') }}</textarea>
                            @error('special_instructions')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loan Information (conditional) -->
        <div id="loan_information" class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6 hidden">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Loan Information</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Required for loan collateral inspections.
                    </p>
                </div>
                <div class="mt-5 md:col-span-2 md:mt-0">
                    <div class="grid grid-cols-6 gap-6">
                        <!-- Loan Amount -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="loan_amount" class="block text-sm font-medium text-gray-700">Loan Amount (RWF)</label>
                            <input type="number" name="loan_amount" id="loan_amount" 
                                   value="{{ old('loan_amount') }}"
                                   min="0" step="1000"
                                   placeholder="e.g., 10000000"
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('loan_amount')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Loan Reference -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="loan_reference" class="block text-sm font-medium text-gray-700">Loan Reference</label>
                            <input type="text" name="loan_reference" id="loan_reference" 
                                   value="{{ old('loan_reference') }}"
                                   placeholder="e.g., LN-2025-001"
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('loan_reference')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Package Information Display -->
        <div id="package_info" class="bg-green-50 border border-green-200 rounded-lg p-4 hidden">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-green-900">📦 Package Summary</h4>
                    <div class="mt-2 text-sm text-green-700">
                        <p><strong>Price:</strong> <span id="package_price">-</span> RWF</p>
                        <p><strong>Duration:</strong> <span id="package_duration">-</span> hours</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.inspection-requests.index') }}" 
               class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Cancel
            </a>
            <button type="submit" 
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Create Inspection Request
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Data from server
const businessPartnerUsers = @json($businessPartnerUsers);
let searchTimeout;
let selectedProperty = null;

// Initialize form
document.addEventListener('DOMContentLoaded', function() {
    // Check if property_id is pre-selected (from URL parameter)
    const propertyId = document.getElementById('property_id').value;
    if (propertyId) {
        // Load the property details for the pre-selected property
        loadPropertyDetails(propertyId);
    }
    
    // Initialize other fields
    toggleRequesterFields();
    toggleLoanFields();
    updatePackageInfo();
});

// Property search functionality
document.getElementById('property_search').addEventListener('input', function(e) {
    const query = e.target.value.trim();
    
    if (query.length < 2) {
        hideSearchResults();
        return;
    }
    
    // Clear previous timeout
    clearTimeout(searchTimeout);
    
    // Show loading
    showSearchLoading();
    
    // Debounce search
    searchTimeout = setTimeout(() => {
        searchProperties(query);
    }, 300);
});

// Hide search results when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.property-search-container')) {
        hideSearchResults();
    }
});

function searchProperties(query) {
    fetch(`/admin/properties/search?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            hideSearchLoading();
            displaySearchResults(data.properties || []);
        })
        .catch(error => {
            hideSearchLoading();
            console.error('Search error:', error);
            showSearchError();
        });
}

function displaySearchResults(properties) {
    const resultsContainer = document.getElementById('property_search_results');
    
    if (properties.length === 0) {
        resultsContainer.innerHTML = `
            <div class="property-search-item text-center text-gray-500 py-4">
                <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <p class="font-medium">No properties found</p>
                <p class="text-xs mt-1">Try a different search term</p>
            </div>
        `;
    } else {
        resultsContainer.innerHTML = properties.map(property => `
            <div class="property-search-item" onclick="selectProperty(${property.id}, '${property.property_code}', '${property.owner_name}', '${property.owner_phone || ''}', '${property.owner_email || ''}', '${property.address || ''}', '${property.district || ''}')">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                ${property.property_code}
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                ${property.property_type || 'N/A'}
                            </span>
                        </div>
                        <p class="font-medium text-gray-900">${property.owner_name}</p>
                        <p class="text-sm text-gray-600">${property.address || 'No address'}, ${property.district || ''}</p>
                        <p class="text-xs text-gray-500">📞 ${property.owner_phone || 'No phone'}</p>
                    </div>
                    <div class="flex-shrink-0 ml-4">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </div>
            </div>
        `).join('');
    }
    
    resultsContainer.classList.remove('hidden');
}

function selectProperty(id, code, ownerName, ownerPhone, ownerEmail, address, district) {
    selectedProperty = {
        id: id,
        code: code,
        owner_name: ownerName,
        owner_phone: ownerPhone,
        owner_email: ownerEmail,
        address: address,
        district: district
    };
    
    // Set hidden input
    document.getElementById('property_id').value = id;
    
    // Update search input
    document.getElementById('property_search').value = `${code} - ${ownerName}`;
    
    // Auto-fill applicant information
    autoFillApplicantInfo(ownerName, ownerPhone);
    
    // Show selected property info
    showSelectedPropertyInfo();
    
    // Hide search results
    hideSearchResults();
}

function autoFillApplicantInfo(ownerName, ownerPhone) {
    const applicantNameField = document.getElementById('applicant_name');
    const applicantPhoneField = document.getElementById('applicant_phone');
    
    // Only auto-fill if the checkbox is not checked (i.e., use owner details)
    if (!document.getElementById('use_different_applicant').checked) {
        // Fill the fields
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

function showSelectedPropertyInfo() {
    const container = document.getElementById('selected_property_info');
    const detailsContainer = document.getElementById('selected_property_details');
    
    detailsContainer.innerHTML = `
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <p class="font-medium">${selectedProperty.code}</p>
                <p class="text-xs">Property Code</p>
            </div>
            <div>
                <p class="font-medium">${selectedProperty.owner_name}</p>
                <p class="text-xs">Owner Name</p>
            </div>
            <div>
                <p class="font-medium">${selectedProperty.owner_phone || 'N/A'}</p>
                <p class="text-xs">Owner Phone</p>
            </div>
            <div>
                <p class="font-medium">${selectedProperty.address || 'N/A'}</p>
                <p class="text-xs">Address</p>
            </div>
        </div>
    `;
    
    container.classList.remove('hidden');
}

function clearPropertySelection() {
    selectedProperty = null;
    document.getElementById('property_id').value = '';
    document.getElementById('property_search').value = '';
    document.getElementById('selected_property_info').classList.add('hidden');
    
    // Clear applicant fields if not using different applicant
    if (!document.getElementById('use_different_applicant').checked) {
        document.getElementById('applicant_name').value = '';
        document.getElementById('applicant_phone').value = '';
    }
}

function loadPropertyDetails(propertyId) {
    fetch(`/admin/properties/${propertyId}/details`)
        .then(response => response.json())
        .then(property => {
            selectProperty(
                property.id,
                property.property_code,
                property.owner_name,
                property.owner_phone || '',
                property.owner_email || '',
                property.address || '',
                property.district || ''
            );
        })
        .catch(error => {
            console.error('Error loading property details:', error);
        });
}

function showSearchLoading() {
    document.getElementById('search_loading').classList.remove('hidden');
}

function hideSearchLoading() {
    document.getElementById('search_loading').classList.add('hidden');
}

function hideSearchResults() {
    document.getElementById('property_search_results').classList.add('hidden');
}

function showSearchError() {
    const resultsContainer = document.getElementById('property_search_results');
    resultsContainer.innerHTML = `
        <div class="property-search-item text-center text-red-500 py-4">
            <svg class="mx-auto h-8 w-8 text-red-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="font-medium">Search failed</p>
            <p class="text-xs mt-1">Please try again</p>
        </div>
    `;
    resultsContainer.classList.remove('hidden');
}

// Business partner users data
function toggleRequesterFields() {
    const requesterType = document.getElementById('requester_type').value;
    const individualField = document.getElementById('individual_user_field');
    const businessPartnerField = document.getElementById('business_partner_field');
    const partnerUserField = document.getElementById('partner_user_field');
    
    // Hide all fields first
    individualField.classList.add('hidden');
    businessPartnerField.classList.add('hidden');
    partnerUserField.classList.add('hidden');
    
    // Clear required attributes
    document.getElementById('requester_user_id').removeAttribute('required');
    document.getElementById('business_partner_id').removeAttribute('required');
    document.getElementById('partner_user_id').removeAttribute('required');
    
    if (requesterType === 'individual') {
        individualField.classList.remove('hidden');
        document.getElementById('requester_user_id').setAttribute('required', 'required');
    } else if (requesterType === 'business_partner') {
        businessPartnerField.classList.remove('hidden');
        partnerUserField.classList.remove('hidden');
        document.getElementById('business_partner_id').setAttribute('required', 'required');
        document.getElementById('partner_user_id').setAttribute('required', 'required');
    }
}

function updatePartnerUsers() {
    const partnerId = document.getElementById('business_partner_id').value;
    const partnerUserSelect = document.getElementById('partner_user_id');
    
    partnerUserSelect.innerHTML = '<option value="">Choose a user...</option>';
    
    if (partnerId && businessPartnerUsers[partnerId]) {
        businessPartnerUsers[partnerId].forEach(user => {
            const option = document.createElement('option');
            option.value = user.id;
            option.textContent = `${user.full_name} - ${user.email}`;
            partnerUserSelect.appendChild(option);
        });
    }
}

// Toggle applicant fields
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
    } else {
        // Make readonly and auto-fill from property
        nameField.setAttribute('readonly', 'readonly');
        phoneField.setAttribute('readonly', 'readonly');
        nameField.classList.remove('bg-white');
        phoneField.classList.remove('bg-white');
        nameField.classList.add('bg-gray-50');
        phoneField.classList.add('bg-gray-50');
        
        if (selectedProperty) {
            autoFillApplicantInfo(selectedProperty.owner_name, selectedProperty.owner_phone);
        } else {
            nameField.value = '';
            phoneField.value = '';
            nameField.placeholder = 'Will auto-fill when property is selected';
            phoneField.placeholder = 'Will auto-fill when property is selected';
        }
    }
}

// Toggle loan information fields
function toggleLoanFields() {
    const purpose = document.getElementById('purpose').value;
    const loanInfo = document.getElementById('loan_information');
    
    if (purpose === 'loan_collateral') {
        loanInfo.classList.remove('hidden');
    } else {
        loanInfo.classList.add('hidden');
    }
}

// Update package information display
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
        durationSpan.textContent = duration || '-';
        packageInfo.classList.remove('hidden');
    } else {
        packageInfo.classList.add('hidden');
    }
}

// Form validation before submit
document.querySelector('form').addEventListener('submit', function(e) {
    if (!document.getElementById('property_id').value) {
        e.preventDefault();
        alert('⚠️ Please select a property before submitting the form.');
        document.getElementById('property_search').focus();
        return false;
    }
});
</script>
@endpush

@endsection