@extends('layouts.admin')

@section('title', 'Edit Inspector - ' . $inspector->user->full_name)

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Edit Inspector: {{ $inspector->user->full_name }}
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <a href="{{ route('admin.inspectors.index') }}" class="text-indigo-600 hover:text-indigo-500">Inspectors</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.inspectors.show', $inspector) }}" class="text-indigo-600 hover:text-indigo-500">{{ $inspector->user->full_name }}</a>
                <span class="mx-2">/</span>
                <span>Edit</span>
            </div>
        </div>
    </div>
    <div class="mt-4 flex space-x-3 md:ml-4 md:mt-0">
        <a href="{{ route('admin.inspectors.show', $inspector) }}" 
           class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 010-1.06L10.94 8.25H4a.75.75 0 010-1.5h6.94L7.72 3.53a.75.75 0 011.06-1.06l4.5 4.5a.75.75 0 010 1.06l-4.5 4.5a.75.75 0 01-1.06 0z" clip-rule="evenodd" />
            </svg>
            Back to Inspector
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.inspectors.update', $inspector) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- Personal Information -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Personal Information</h3>
                    <p class="mt-1 text-sm text-gray-500">Update the inspector's personal details and contact information.</p>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="grid grid-cols-6 gap-6">
                        <!-- First Name -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                            <input type="text" name="first_name" id="first_name" 
                                   value="{{ old('first_name', $inspector->user->first_name) }}"
                                   class="mt-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm @error('first_name') border-red-500 @enderror" 
                                   required>
                            @error('first_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                            <input type="text" name="last_name" id="last_name" 
                                   value="{{ old('last_name', $inspector->user->last_name) }}"
                                   class="mt-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm @error('last_name') border-red-500 @enderror" 
                                   required>
                            @error('last_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-span-6 sm:col-span-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input type="email" name="email" id="email" 
                                   value="{{ old('email', $inspector->user->email) }}"
                                   class="mt-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm @error('email') border-red-500 @enderror" 
                                   required>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="text" name="phone" id="phone" 
                                   value="{{ old('phone', $inspector->user->phone) }}"
                                   class="mt-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm @error('phone') border-red-500 @enderror" 
                                   required>
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Inspector Code (Read-only) -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="inspector_code" class="block text-sm font-medium text-gray-700">Inspector Code</label>
                            <input type="text" name="inspector_code" id="inspector_code" 
                                   value="{{ $inspector->inspector_code }}"
                                   class="mt-1 bg-gray-50 border border-gray-300 text-gray-500 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed" 
                                   readonly>
                            <p class="mt-1 text-xs text-gray-500">Inspector code cannot be changed</p>
                        </div>

                        <!-- Password (Optional) -->
                        <div class="col-span-6">
                            <label for="password" class="block text-sm font-medium text-gray-700">New Password (Optional)</label>
                            <input type="password" name="password" id="password" 
                                   placeholder="Enter new password to change"
                                   class="mt-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm @error('password') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">Leave blank to keep current password</p>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="col-span-6" id="password_confirmation_field" style="display: none;">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" 
                                   placeholder="Confirm new password"
                                   class="mt-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm">
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
                    <p class="mt-1 text-sm text-gray-500">Inspector's certification, experience, and professional details.</p>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="grid grid-cols-6 gap-6">
                        <!-- Certification Level -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="certification_level" class="block text-sm font-medium text-gray-700">Certification Level</label>
                            <select name="certification_level" id="certification_level" 
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('certification_level') border-red-500 @enderror" 
                                    required>
                                @foreach($certificationLevels as $level)
                                    <option value="{{ $level }}" {{ old('certification_level', $inspector->certification_level) === $level ? 'selected' : '' }}>
                                        {{ ucfirst($level) }} Inspector
                                    </option>
                                @endforeach
                            </select>
                            @error('certification_level')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Experience Years -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="experience_years" class="block text-sm font-medium text-gray-700">Years of Experience</label>
                            <input type="number" name="experience_years" id="experience_years" min="0" max="50" 
                                   value="{{ old('experience_years', $inspector->experience_years) }}"
                                   class="mt-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm @error('experience_years') border-red-500 @enderror" 
                                   required>
                            @error('experience_years')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Certification Expiry -->
                        <div class="col-span-6 sm:col-span-4">
                            <label for="certification_expiry" class="block text-sm font-medium text-gray-700">Certification Expiry Date</label>
                            <input type="date" name="certification_expiry" id="certification_expiry" 
                                   value="{{ old('certification_expiry', $inspector->certification_expiry ? $inspector->certification_expiry->format('Y-m-d') : '') }}"
                                   class="mt-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm @error('certification_expiry') border-red-500 @enderror">
                            @error('certification_expiry')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Availability Status -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="availability_status" class="block text-sm font-medium text-gray-700">Availability Status</label>
                            <select name="availability_status" id="availability_status" 
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('availability_status') border-red-500 @enderror" 
                                    required>
                                @foreach($availabilityStatuses as $status)
                                    <option value="{{ $status }}" {{ old('availability_status', $inspector->availability_status) === $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('availability_status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Specializations -->
                        <div class="col-span-6">
                            <fieldset>
                                <legend class="text-sm font-medium text-gray-700">Specializations</legend>
                                <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3">
                                    @foreach($specializations as $key => $value)
                                        <div class="flex items-start">
                                            <div class="flex items-center h-5">
                                                <input type="checkbox" 
                                                       name="specializations[]" 
                                                       id="specialization_{{ $key }}" 
                                                       value="{{ $key }}"
                                                       {{ in_array($key, old('specializations', $inspector->specializations ?? [])) ? 'checked' : '' }}
                                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="specialization_{{ $key }}" class="font-medium text-gray-700 cursor-pointer">{{ $value }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </fieldset>
                            @error('specializations')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Equipment Assigned -->
                        <div class="col-span-6">
                            <fieldset>
                                <legend class="text-sm font-medium text-gray-700">Equipment Assigned</legend>
                                <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3">
                                    @foreach($equipmentOptions as $key => $value)
                                        <div class="flex items-start">
                                            <div class="flex items-center h-5">
                                                <input type="checkbox" 
                                                       name="equipment_assigned[]" 
                                                       id="equipment_{{ $key }}" 
                                                       value="{{ $key }}"
                                                       {{ in_array($key, old('equipment_assigned', $inspector->equipment_assigned ?? [])) ? 'checked' : '' }}
                                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="equipment_{{ $key }}" class="font-medium text-gray-700 cursor-pointer">{{ $value }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </fieldset>
                            @error('equipment_assigned')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Statistics (Read-only) -->
        <div class="bg-gray-50 shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Current Statistics</h3>
                    <p class="mt-1 text-sm text-gray-500">Inspector's current performance metrics. These values are automatically updated.</p>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="grid grid-cols-2 gap-6 sm:grid-cols-3">
                        <!-- Rating -->
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ number_format($inspector->rating, 1) }}/5</div>
                            <div class="text-sm text-gray-500">Current Rating</div>
                        </div>

                        <!-- Total Inspections -->
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ $inspector->total_inspections }}</div>
                            <div class="text-sm text-gray-500">Total Inspections</div>
                        </div>

                        <!-- Member Since -->
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ $inspector->created_at->format('M Y') }}</div>
                            <div class="text-sm text-gray-500">Member Since</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.inspectors.show', $inspector) }}" 
               class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Cancel
            </a>
            <button type="submit" 
                    class="bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Update Inspector
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordField = document.getElementById('password');
    const confirmationField = document.getElementById('password_confirmation_field');
    
    // Show/hide password confirmation field
    passwordField.addEventListener('input', function() {
        if (this.value.length > 0) {
            confirmationField.style.display = 'block';
        } else {
            confirmationField.style.display = 'none';
            document.getElementById('password_confirmation').value = '';
        }
    });
});
</script>
@endpush
@endsection