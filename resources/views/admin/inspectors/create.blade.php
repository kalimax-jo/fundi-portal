@extends('layouts.admin')

@section('title', 'Add Inspector')

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Add New Inspector
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <a href="{{ route('admin.inspectors.index') }}" class="text-indigo-600 hover:text-indigo-500">Inspectors</a>
                <span class="mx-2">/</span>
                <span>Add Inspector</span>
            </div>
        </div>
    </div>
    <div class="mt-4 flex md:ml-4 md:mt-0">
        <a href="{{ route('admin.inspectors.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 010-1.06L10.94 8.25H4a.75.75 0 010-1.5h6.94L7.72 3.53a.75.75 0 011.06-1.06l4.5 4.5a.75.75 0 010 1.06l-4.5 4.5a.75.75 0 01-1.06 0z" clip-rule="evenodd" />
            </svg>
            Back to Inspectors
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.inspectors.store') }}" method="POST" class="space-y-8">
        @csrf

        <!-- Personal Information -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Personal Information</h3>
                    <p class="mt-1 text-sm text-gray-500">Inspector's personal details and contact information.</p>
                </div>
                <div class="mt-5 space-y-6 md:col-span-2 md:mt-0">
                    <div class="grid grid-cols-6 gap-6">
                        <!-- First Name -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                            <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required
                                   placeholder="Enter first name"
                                   class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-sm shadow-sm placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('first_name') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            @error('first_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                            <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                                   placeholder="Enter last name"
                                   class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-sm shadow-sm placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('last_name') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            @error('last_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-span-6 sm:col-span-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                   placeholder="Enter email address"
                                   class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-sm shadow-sm placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('email') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required
                                   placeholder="e.g., +250788123456"
                                   class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-sm shadow-sm placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('phone') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Security -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Account Security</h3>
                    <p class="mt-1 text-sm text-gray-500">Set login credentials for the inspector.</p>
                </div>
                <div class="mt-5 space-y-6 md:col-span-2 md:mt-0">
                    <div class="grid grid-cols-6 gap-6">
                        <!-- Password -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                            <input type="password" name="password" id="password" required
                                   placeholder="Enter password"
                                   class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-sm shadow-sm placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('password') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">Minimum 8 characters</p>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password *</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                   placeholder="Confirm password"
                                   class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-sm shadow-sm placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Professional Information -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Professional Information</h3>
                    <p class="mt-1 text-sm text-gray-500">Inspector's qualifications, experience, and certifications.</p>
                </div>
                <div class="mt-5 space-y-6 md:col-span-2 md:mt-0">
                    <div class="grid grid-cols-6 gap-6">
                        <!-- Certification Level -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="certification_level" class="block text-sm font-medium text-gray-700 mb-1">Certification Level *</label>
                            <select name="certification_level" id="certification_level" required
                                    class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-sm shadow-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('certification_level') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                <option value="">Select Level</option>
                                @foreach($certificationLevels as $level)
                                    <option value="{{ $level }}" {{ old('certification_level') === $level ? 'selected' : '' }}>
                                        {{ ucfirst($level) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('certification_level')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Experience Years -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="experience_years" class="block text-sm font-medium text-gray-700 mb-1">Years of Experience *</label>
                            <input type="number" name="experience_years" id="experience_years" value="{{ old('experience_years') }}" required
                                   min="0" max="50" placeholder="0"
                                   class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-sm shadow-sm placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('experience_years') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            @error('experience_years')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Certification Expiry -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="certification_expiry" class="block text-sm font-medium text-gray-700 mb-1">Certification Expiry Date *</label>
                            <input type="date" name="certification_expiry" id="certification_expiry" value="{{ old('certification_expiry') }}" required
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-sm shadow-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('certification_expiry') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            @error('certification_expiry')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Availability Status -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="availability_status" class="block text-sm font-medium text-gray-700 mb-1">Initial Availability *</label>
                            <select name="availability_status" id="availability_status" required
                                    class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-sm shadow-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('availability_status') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                <option value="">Select Status</option>
                                @foreach($availabilityStatuses as $status)
                                    <option value="{{ $status }}" {{ old('availability_status') === $status || $status === 'available' ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('availability_status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Specializations -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Specializations</h3>
                    <p class="mt-1 text-sm text-gray-500">Select the areas of expertise for this inspector.</p>
                </div>
                <div class="mt-5 md:col-span-2 md:mt-0">
                    @error('specializations')
                        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        </div>
                    @enderror

                    <fieldset class="border border-gray-200 rounded-lg p-4">
                        <legend class="text-sm font-medium text-gray-900 px-2">Areas of Expertise</legend>
                        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                            @foreach($specializations as $key => $value)
                            <div class="flex items-start p-3 border border-gray-100 rounded-lg hover:bg-gray-50">
                                <div class="flex items-center h-5">
                                    <input id="specialization_{{ $key }}" name="specializations[]" type="checkbox" value="{{ $key }}"
                                           {{ in_array($key, old('specializations', [])) ? 'checked' : '' }}
                                           class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="specialization_{{ $key }}" class="font-medium text-gray-700 cursor-pointer">{{ $value }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>

        <!-- Equipment Assignment -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Equipment Assignment</h3>
                    <p class="mt-1 text-sm text-gray-500">Assign equipment and tools to this inspector.</p>
                </div>
                <div class="mt-5 md:col-span-2 md:mt-0">
                    @error('equipment_assigned')
                        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        </div>
                    @enderror

                    <fieldset class="border border-gray-200 rounded-lg p-4">
                        <legend class="text-sm font-medium text-gray-900 px-2">Available Equipment</legend>
                        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                            @foreach($equipmentOptions as $key => $value)
                            <div class="flex items-start p-3 border border-gray-100 rounded-lg hover:bg-gray-50">
                                <div class="flex items-center h-5">
                                    <input id="equipment_{{ $key }}" name="equipment_assigned[]" type="checkbox" value="{{ $key }}"
                                           {{ in_array($key, old('equipment_assigned', [])) ? 'checked' : '' }}
                                           class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="equipment_{{ $key }}" class="font-medium text-gray-700 cursor-pointer">{{ $value }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>

        <!-- Preview Section -->
        <div class="bg-gray-50 shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Inspector Preview</h3>
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-900" id="preview-name">
                            <span class="text-gray-400">Name will appear here</span>
                        </p>
                        <p class="text-sm text-gray-500" id="preview-email">
                            <span class="text-gray-400">email@example.com</span>
                        </p>
                        <div class="mt-2 flex items-center space-x-4">
                            <div>
                                <span class="text-xs text-gray-500">Level: </span>
                                <span id="preview-certification" class="text-xs font-medium text-indigo-600">Not selected</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Experience: </span>
                                <span id="preview-experience" class="text-xs font-medium text-indigo-600">0 years</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Specializations: </span>
                                <span id="preview-specializations" class="text-xs font-medium text-indigo-600">0 selected</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.inspectors.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Cancel
            </a>
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Create Inspector
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Live preview functionality
    const firstNameInput = document.getElementById('first_name');
    const lastNameInput = document.getElementById('last_name');
    const emailInput = document.getElementById('email');
    const certificationInput = document.getElementById('certification_level');
    const experienceInput = document.getElementById('experience_years');
    const specializationCheckboxes = document.querySelectorAll('input[name="specializations[]"]');
    
    function updatePreview() {
        // Update name
        const firstName = firstNameInput.value || '';
        const lastName = lastNameInput.value || '';
        const fullName = (firstName + ' ' + lastName).trim() || 'Name will appear here';
        document.getElementById('preview-name').innerHTML = 
            fullName === 'Name will appear here' ? 
            '<span class="text-gray-400">' + fullName + '</span>' : fullName;
        
        // Update email
        const email = emailInput.value || 'email@example.com';
        document.getElementById('preview-email').innerHTML = 
            email === 'email@example.com' ? 
            '<span class="text-gray-400">' + email + '</span>' : email;
        
        // Update certification
        const certification = certificationInput.value || 'Not selected';
        document.getElementById('preview-certification').textContent = 
            certification === 'Not selected' ? certification : certification.charAt(0).toUpperCase() + certification.slice(1);
        
        // Update experience
        const experience = experienceInput.value || '0';
        document.getElementById('preview-experience').textContent = experience + ' years';
        
        // Update specializations count
        const checkedSpecializations = document.querySelectorAll('input[name="specializations[]"]:checked');
        const count = checkedSpecializations.length;
        document.getElementById('preview-specializations').textContent = 
            count + (count === 1 ? ' specialization selected' : ' specializations selected');
    }
    
    // Add event listeners for live preview
    firstNameInput.addEventListener('input', updatePreview);
    lastNameInput.addEventListener('input', updatePreview);
    emailInput.addEventListener('input', updatePreview);
    certificationInput.addEventListener('change', updatePreview);
    experienceInput.addEventListener('input', updatePreview);
    
    specializationCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updatePreview);
    });
    
    // Password confirmation validation
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    
    function validatePasswords() {
        if (passwordInput.value && confirmPasswordInput.value) {
            if (passwordInput.value !== confirmPasswordInput.value) {
                confirmPasswordInput.setCustomValidity('Passwords do not match');
            } else {
                confirmPasswordInput.setCustomValidity('');
            }
        } else {
            confirmPasswordInput.setCustomValidity('');
        }
    }
    
    passwordInput.addEventListener('input', validatePasswords);
    confirmPasswordInput.addEventListener('input', validatePasswords);
    
    // Initial preview update
    updatePreview();
});
</script>
@endpush
@endsection