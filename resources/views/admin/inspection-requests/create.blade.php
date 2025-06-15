{{-- File Path: resources/views/admin/inspection-requests/create.blade.php --}}

@extends('layouts.admin')

@section('title', 'Create Inspection Request')

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
                                    {{ $user->full_name }} ({{ $user->email }})
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
                            <select name="business_partner_id" id="business_partner_id" onchange="loadPartnerUsers()"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Choose a business partner...</option>
                                @foreach($businessPartners as $partner)
                                <option value="{{ $partner->id }}" {{ old('business_partner_id') == $partner->id ? 'selected' : '' }}>
                                    {{ $partner->name }} ({{ $partner->type }})
                                </option>
                                @endforeach
                            </select>
                            @error('business_partner_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Business Partner User -->
                        <div id="partner_user_field" class="col-span-6 sm:col-span-3 hidden">
                            <label for="partner_user_id" class="block text-sm font-medium text-gray-700">Partner User *</label>
                            <select name="partner_user_id" id="partner_user_id"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Select business partner first...</option>
                            </select>
                            @error('partner_user_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Property -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="property_id" class="block text-sm font-medium text-gray-700">Property *</label>
                            <select name="property_id" id="property_id" required
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Choose a property...</option>
                                @foreach($properties as $property)
                                <option value="{{ $property->id }}" {{ old('property_id') == $property->id ? 'selected' : '' }}>
                                    {{ $property->property_code }} - {{ $property->address }}
                                </option>
                                @endforeach
                            </select>
                            @error('property_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
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
                                        data-duration="{{ $package->duration_hours }}"
                                        {{ old('package_id') == $package->id ? 'selected' : '' }}>
                                    {{ $package->display_name }} - {{ number_format($package->price) }} RWF
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
                            <select name="purpose" id="purpose" required
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Select Purpose</option>
                                <option value="rental" {{ old('purpose') == 'rental' ? 'selected' : '' }}>Rental</option>
                                <option value="sale" {{ old('purpose') == 'sale' ? 'selected' : '' }}>Sale</option>
                                <option value="purchase" {{ old('purpose') == 'purchase' ? 'selected' : '' }}>Purchase</option>
                                <option value="loan_collateral" {{ old('purpose') == 'loan_collateral' ? 'selected' : '' }}>Loan Collateral</option>
                                <option value="insurance" {{ old('purpose') == 'insurance' ? 'selected' : '' }}>Insurance</option>
                                <option value="maintenance" {{ old('purpose') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="other" {{ old('purpose') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('purpose')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Urgency -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="urgency" class="block text-sm font-medium text-gray-700">Urgency</label>
                            <select name="urgency" id="urgency"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="normal" {{ old('urgency') == 'normal' ? 'selected' : '' }}>Normal</option>
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

        <!-- Scheduling Information -->
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
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   value="{{ old('preferred_date') }}"
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('preferred_date')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Preferred Time Slot -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="preferred_time_slot" class="block text-sm font-medium text-gray-700">Preferred Time</label>
                            <select name="preferred_time_slot" id="preferred_time_slot"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="flexible" {{ old('preferred_time_slot') == 'flexible' ? 'selected' : '' }}>Flexible</option>
                                <option value="morning" {{ old('preferred_time_slot') == 'morning' ? 'selected' : '' }}>Morning (8AM - 12PM)</option>
                                <option value="afternoon" {{ old('preferred_time_slot') == 'afternoon' ? 'selected' : '' }}>Afternoon (12PM - 5PM)</option>
                                <option value="evening" {{ old('preferred_time_slot') == 'evening' ? 'selected' : '' }}>Evening (5PM - 8PM)</option>
                            </select>
                            @error('preferred_time_slot')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Special Instructions -->
                        <div class="col-span-6">
                            <label for="special_instructions" class="block text-sm font-medium text-gray-700">Special Instructions</label>
                            <textarea name="special_instructions" id="special_instructions" rows="3"
                                      class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                      placeholder="Any special requirements or instructions for the inspector...">{{ old('special_instructions') }}</textarea>
                            @error('special_instructions')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loan Information (for business partners) -->
        <div id="loan_information" class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6 hidden">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Loan Information</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Information about the loan for which this inspection is required.
                    </p>
                </div>
                <div class="mt-5 md:col-span-2 md:mt-0">
                    <div class="grid grid-cols-6 gap-6">
                        <!-- Loan Amount -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="loan_amount" class="block text-sm font-medium text-gray-700">Loan Amount (RWF)</label>
                            <input type="number" name="loan_amount" id="loan_amount" min="0" step="1000"
                                   value="{{ old('loan_amount') }}"
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
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('loan_reference')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Applicant Name -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="applicant_name" class="block text-sm font-medium text-gray-700">Applicant Name</label>
                            <input type="text" name="applicant_name" id="applicant_name" 
                                   value="{{ old('applicant_name') }}"
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('applicant_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Applicant Phone -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="applicant_phone" class="block text-sm font-medium text-gray-700">Applicant Phone</label>
                            <input type="text" name="applicant_phone" id="applicant_phone" 
                                   value="{{ old('applicant_phone') }}"
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('applicant_phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Package Information Display -->
        <div id="package_info" class="bg-blue-50 border border-blue-200 rounded-lg p-4 hidden">
            <h4 class="text-sm font-medium text-blue-900">Package Details</h4>
            <div class="mt-2 text-sm text-blue-700">
                <p><strong>Price:</strong> <span id="package_price">-</span> RWF</p>
                <p><strong>Duration:</strong> <span id="package_duration">-</span> hours</p>
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
// Business partner users data
const businessPartnerUsers = @json($businessPartnerUsers);

function toggleRequesterFields() {
    const requesterType = document.getElementById('requester_type').value;
    const individualField = document.getElementById('individual_user_field');
    const businessPartnerField = document.getElementById('business_partner_field');
    const partnerUserField = document.getElementById('partner_user_field');
    const loanInfo = document.getElementById('loan_information');
    
    // Hide all fields first
    individualField.classList.add('hidden');
    businessPartnerField.classList.add('hidden');
    partnerUserField.classList.add('hidden');
    loanInfo.classList.add('hidden');
    
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
        loanInfo.classList.remove('hidden');
        document.getElementById('business_partner_id').setAttribute('required', 'required');
        document.getElementById('partner_user_id').setAttribute('required', 'required');
    }
}

function loadPartnerUsers() {
    const partnerId = document.getElementById('business_partner_id').value;
    const userSelect = document.getElementById('partner_user_id');
    
    // Clear existing options
    userSelect.innerHTML = '<option value="">Loading users...</option>';
    
    if (partnerId && businessPartnerUsers[partnerId]) {
        userSelect.innerHTML = '<option value="">Choose a user...</option>';
        businessPartnerUsers[partnerId].forEach(user => {
            const option = document.createElement('option');
            option.value = user.id;
            option.textContent = `${user.full_name} (${user.email})`;
            userSelect.appendChild(option);
        });
    } else {
        userSelect.innerHTML = '<option value="">Select business partner first...</option>';
    }
}

function updatePackageInfo() {
    const packageSelect = document.getElementById('package_id');
    const selectedOption = packageSelect.options[packageSelect.selectedIndex];
    const packageInfo = document.getElementById('package_info');
    const priceSpan = document.getElementById('package_price');
    const durationSpan = document.getElementById('package_duration');
    
    if (selectedOption.value) {
        const price = selectedOption.dataset.price;
        const duration = selectedOption.dataset.duration;
        
        priceSpan.textContent = new Intl.NumberFormat().format(price);
        durationSpan.textContent = duration;
        packageInfo.classList.remove('hidden');
    } else {
        packageInfo.classList.add('hidden');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleRequesterFields();
    updatePackageInfo();
});
</script>
@endpush

@endsection