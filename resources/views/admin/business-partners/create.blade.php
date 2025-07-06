{{-- File Path: resources/views/admin/business-partners/create.blade.php --}}

@extends('layouts.admin')

@section('title', 'Add Business Partner')

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Add Business Partner
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <a href="{{ route('admin.business-partners.index') }}" class="text-indigo-600 hover:text-indigo-500">Business Partners</a>
                <span class="mx-2">/</span>
                <span>Add Partner</span>
            </div>
        </div>
    </div>
    <div class="mt-4 flex space-x-3 md:ml-4 md:mt-0">
        <a href="{{ route('admin.business-partners.index') }}" 
           class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 010-1.06L10.94 8.25H4a.75.75 0 010-1.5h6.94L7.72 3.53a.75.75 0 011.06-1.06l4.5 4.5a.75.75 0 010 1.06l-4.5 4.5a.75.75 0 01-1.06 0z" clip-rule="evenodd" />
            </svg>
            Back to Partners
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.business-partners.store') }}" method="POST" class="space-y-8">
        @csrf

        <!-- Company Information -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Company Information</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Basic information about the business partner organization.
                    </p>
                </div>
                <div class="mt-5 md:col-span-2 md:mt-0">
                    <div class="grid grid-cols-6 gap-6">
                        <!-- Business Partner Name -->
                        <div class="col-span-6">
                            <label for="name" class="block text-sm font-medium text-gray-700">Business Partner Name *</label>
                            <input type="text" name="name" id="name" required
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('name') }}"
                                   oninput="generateSubdomainSuggestion()">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Subdomain -->
                        <div class="col-span-6">
                            <label for="subdomain" class="block text-sm font-medium text-gray-700">Subdomain</label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <input type="text" name="subdomain" id="subdomain"
                                       class="flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-l-md"
                                       placeholder="partner-name"
                                       value="{{ old('subdomain') }}"
                                       oninput="generateSubdomainSuggestion()">
                                <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                    .fundi.info
                                </span>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Leave empty to auto-generate from business name</p>
                            <div id="subdomain-suggestion" class="mt-1 text-sm text-blue-600 hidden"></div>
                            <div id="subdomain-status" class="mt-1 text-sm hidden"></div>
                            @error('subdomain')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="type" class="block text-sm font-medium text-gray-700">Business Type *</label>
                            <select name="type" id="type" required
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Select Type</option>
                                @foreach($partnerTypes as $value => $label)
                                    <option value="{{ $value }}" {{ old('type') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Registration Number -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="registration_number" class="block text-sm font-medium text-gray-700">Registration Number</label>
                            <input type="text" name="registration_number" id="registration_number"
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('registration_number') }}">
                            @error('registration_number')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="email" class="block text-sm font-medium text-gray-700">Company Email *</label>
                            <input type="email" name="email" id="email" required
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('email') }}">
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="tel" name="phone" id="phone"
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('phone') }}">
                            @error('phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Website -->
                        <div class="col-span-6">
                            <label for="website" class="block text-sm font-medium text-gray-700">Website</label>
                            <input type="url" name="website" id="website"
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   placeholder="https://example.com"
                                   value="{{ old('website') }}">
                            @error('website')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Address Information -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Address Information</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Physical location and address details.
                    </p>
                </div>
                <div class="mt-5 md:col-span-2 md:mt-0">
                    <div class="grid grid-cols-6 gap-6">
                        <!-- Address -->
                        <div class="col-span-6">
                            <label for="address" class="block text-sm font-medium text-gray-700">Street Address *</label>
                            <textarea name="address" id="address" rows="3" required
                                      class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('address') }}</textarea>
                            @error('address')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- City -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="city" class="block text-sm font-medium text-gray-700">City *</label>
                            <input type="text" name="city" id="city" required
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('city') }}">
                            @error('city')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Country -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="country" class="block text-sm font-medium text-gray-700">Country *</label>
                            <input type="text" name="country" id="country" required
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('country', 'Rwanda') }}">
                            @error('country')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Person Information -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Primary Contact</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Main contact person for this business partner.
                    </p>
                </div>
                <div class="mt-5 md:col-span-2 md:mt-0">
                    <div class="grid grid-cols-6 gap-6">
                        <!-- Contact Person Name -->
                        <div class="col-span-6">
                            <label for="contact_person" class="block text-sm font-medium text-gray-700">Contact Person Name *</label>
                            <input type="text" name="contact_person" id="contact_person" required
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('contact_person') }}">
                            @error('contact_person')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contact Email -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="contact_email" class="block text-sm font-medium text-gray-700">Contact Email *</label>
                            <input type="email" name="contact_email" id="contact_email" required
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('contact_email') }}">
                            @error('contact_email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contact Phone -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="contact_phone" class="block text-sm font-medium text-gray-700">Contact Phone</label>
                            <input type="tel" name="contact_phone" id="contact_phone"
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('contact_phone') }}">
                            @error('contact_phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Primary Contact User Account -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">User Account</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Create a user account for the primary contact to access the system.
                    </p>
                </div>
                <div class="mt-5 md:col-span-2 md:mt-0">
                    <div class="grid grid-cols-6 gap-6">
                        <!-- First Name -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="primary_contact_first_name" class="block text-sm font-medium text-gray-700">First Name *</label>
                            <input type="text" name="primary_contact_first_name" id="primary_contact_first_name" required
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('primary_contact_first_name') }}">
                            @error('primary_contact_first_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="primary_contact_last_name" class="block text-sm font-medium text-gray-700">Last Name *</label>
                            <input type="text" name="primary_contact_last_name" id="primary_contact_last_name" required
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('primary_contact_last_name') }}">
                            @error('primary_contact_last_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="primary_contact_email" class="block text-sm font-medium text-gray-700">Login Email *</label>
                            <input type="email" name="primary_contact_email" id="primary_contact_email" required
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('primary_contact_email') }}">
                            @error('primary_contact_email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="primary_contact_phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="tel" name="primary_contact_phone" id="primary_contact_phone"
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('primary_contact_phone') }}">
                            @error('primary_contact_phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="primary_contact_password" class="block text-sm font-medium text-gray-700">Password *</label>
                            <input type="password" name="primary_contact_password" id="primary_contact_password" required
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('primary_contact_password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="primary_contact_password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password *</label>
                            <input type="password" name="primary_contact_password_confirmation" id="primary_contact_password_confirmation" required
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.business-partners.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Create Business Partner
                </button>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
/* Ensure all form inputs are visible and styled correctly */
input[type="text"],
input[type="email"],
input[type="tel"],
input[type="url"],
input[type="number"],
input[type="date"],
input[type="password"],
textarea,
select {
    color: #374151 !important;
    background-color: #ffffff !important;
    border: 1px solid #d1d5db !important;
    border-radius: 0.375rem !important;
    padding: 0.5rem 0.75rem !important;
    font-size: 0.875rem !important;
    line-height: 1.25rem !important;
}

input[type="text"]:focus,
input[type="email"]:focus,
input[type="tel"]:focus,
input[type="url"]:focus,
input[type="number"]:focus,
input[type="date"]:focus,
input[type="password"]:focus,
textarea:focus,
select:focus {
    outline: none !important;
    border-color: #6366f1 !important;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1) !important;
}

/* Ensure placeholder text is visible */
input::placeholder,
textarea::placeholder {
    color: #9ca3af !important;
    opacity: 1 !important;
}

/* Fix any potential text color inheritance issues */
.form-input,
.form-textarea,
.form-select {
    color: #374151 !important;
    background-color: #ffffff !important;
}

/* Ensure labels are visible */
label {
    color: #374151 !important;
    font-weight: 500 !important;
}

/* Error message styling */
.text-red-600 {
    color: #dc2626 !important;
}
</style>
@endpush

@push('scripts')
<script>
// Subdomain generation and validation
function generateSubdomainSuggestion() {
    const nameField = document.getElementById('name');
    const subdomainField = document.getElementById('subdomain');
    const suggestionDiv = document.getElementById('subdomain-suggestion');
    const statusDiv = document.getElementById('subdomain-status');
    
    // If subdomain field is empty, generate suggestion from name
    if (!subdomainField.value && nameField.value) {
        const suggestedSubdomain = nameField.value
            .toLowerCase()
            .replace(/[^a-z0-9]/g, '')
            .substring(0, 20);
        
        // Make the suggestion clickable
        suggestionDiv.innerHTML = `Suggested subdomain: <span class="cursor-pointer underline hover:text-blue-800" onclick="useSuggestedSubdomain('${suggestedSubdomain}')">${suggestedSubdomain}.fundi.info</span>`;
        suggestionDiv.classList.remove('hidden');
        suggestionDiv.classList.add('text-blue-600');
    } else if (subdomainField.value) {
        // Check if subdomain is valid
        const subdomain = subdomainField.value;
        const isValid = /^[a-z0-9-]+$/.test(subdomain);
        
        if (!isValid) {
            statusDiv.textContent = 'Subdomain can only contain lowercase letters, numbers, and hyphens';
            statusDiv.classList.remove('hidden', 'text-green-600');
            statusDiv.classList.add('text-red-600');
        } else {
            // Check availability via AJAX
            checkSubdomainAvailability(subdomain);
        }
    } else {
        suggestionDiv.classList.add('hidden');
        statusDiv.classList.add('hidden');
    }
}

// Function to use the suggested subdomain
function useSuggestedSubdomain(subdomain) {
    const subdomainField = document.getElementById('subdomain');
    subdomainField.value = subdomain;
    
    // Trigger the availability check
    checkSubdomainAvailability(subdomain);
    
    // Hide the suggestion since we're now using it
    const suggestionDiv = document.getElementById('subdomain-suggestion');
    suggestionDiv.classList.add('hidden');
}

function checkSubdomainAvailability(subdomain) {
    const statusDiv = document.getElementById('subdomain-status');
    
    // Show loading state
    statusDiv.textContent = 'Checking availability...';
    statusDiv.classList.remove('hidden', 'text-red-600', 'text-green-600');
    statusDiv.classList.add('text-yellow-600');
    
    // Make AJAX request to check availability
    fetch(`/admin/business-partners/check-subdomain?subdomain=${subdomain}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.available) {
            statusDiv.textContent = '✓ Subdomain is available';
            statusDiv.classList.remove('hidden', 'text-red-600', 'text-yellow-600');
            statusDiv.classList.add('text-green-600');
        } else {
            statusDiv.textContent = '✗ Subdomain is already taken';
            statusDiv.classList.remove('hidden', 'text-green-600', 'text-yellow-600');
            statusDiv.classList.add('text-red-600');
        }
    })
    .catch(error => {
        statusDiv.textContent = 'Error checking availability';
        statusDiv.classList.remove('hidden', 'text-green-600', 'text-yellow-600');
        statusDiv.classList.add('text-red-600');
    });
}

// Auto-fill contact email with primary contact email when it changes
document.getElementById('primary_contact_email').addEventListener('input', function(e) {
    const contactEmailField = document.getElementById('contact_email');
    if (!contactEmailField.value) {
        contactEmailField.value = e.target.value;
    }
});

// Auto-fill contact person with primary contact name when they change
function updateContactPerson() {
    const firstName = document.getElementById('primary_contact_first_name').value;
    const lastName = document.getElementById('primary_contact_last_name').value;
    const contactPersonField = document.getElementById('contact_person');
    
    if (!contactPersonField.value && firstName && lastName) {
        contactPersonField.value = firstName + ' ' + lastName;
    }
}

document.getElementById('primary_contact_first_name').addEventListener('input', updateContactPerson);
document.getElementById('primary_contact_last_name').addEventListener('input', updateContactPerson);

// Auto-fill contact phone with primary contact phone when it changes
document.getElementById('primary_contact_phone').addEventListener('input', function(e) {
    const contactPhoneField = document.getElementById('contact_phone');
    if (!contactPhoneField.value) {
        contactPhoneField.value = e.target.value;
    }
});

// Trigger subdomain suggestion when name field changes
document.getElementById('name').addEventListener('input', generateSubdomainSuggestion);
</script>
@endpush

@endsection