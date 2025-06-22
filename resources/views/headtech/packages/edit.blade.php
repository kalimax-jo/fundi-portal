@extends('layouts.headtech')

@section('title', 'Edit Package')

@section('page-header')
    <div class="md:flex md:items-center md:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                Edit Package: {{ $package->display_name }}
            </h2>
            <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
                <div class="mt-2 flex items-center text-sm text-gray-500">
                    <svg class="mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Update package information and services
                </div>
            </div>
        </div>
        <div class="mt-4 flex space-x-3 md:ml-4 md:mt-0">
            <a href="{{ route('headtech.packages.show', $package) }}" 
               class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                </svg>
                View Package
            </a>
            <a href="{{ route('headtech.packages.index') }}" 
               class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Packages
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <form method="POST" action="{{ route('headtech.packages.update', $package) }}" class="space-y-8">
        @csrf
        @method('PUT')
        
        <!-- Basic Information -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Basic Information</h3>
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Package Name *</label>
                        <input type="text" name="name" id="name" 
                               value="{{ old('name', $package->name) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               placeholder="e.g., A_CHECK">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="display_name" class="block text-sm font-medium text-gray-700">Display Name *</label>
                        <input type="text" name="display_name" id="display_name" 
                               value="{{ old('display_name', $package->display_name) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               placeholder="e.g., A-Check Package">
                        @error('display_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                  placeholder="Describe what this package includes...">{{ old('description', $package->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Pricing Information -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Pricing Information</h3>
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div>
                        <label for="is_custom_quote" class="flex items-center">
                            <input type="checkbox" name="is_custom_quote" id="is_custom_quote" value="1"
                                   {{ old('is_custom_quote', $package->is_custom_quote) ? 'checked' : '' }}
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Custom Quote Required</span>
                        </label>
                        @error('is_custom_quote')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="price_fields" class="{{ old('is_custom_quote', $package->is_custom_quote) ? 'hidden' : '' }}">
                        <label for="price" class="block text-sm font-medium text-gray-700">Price (RWF) *</label>
                        <input type="number" name="price" id="price" step="0.01" min="0"
                               value="{{ old('price', $package->price) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               placeholder="0.00">
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700">Currency</label>
                        <select name="currency" id="currency" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="RWF" {{ old('currency', $package->currency) === 'RWF' ? 'selected' : '' }}>RWF</option>
                            <option value="USD" {{ old('currency', $package->currency) === 'USD' ? 'selected' : '' }}>USD</option>
                            <option value="EUR" {{ old('currency', $package->currency) === 'EUR' ? 'selected' : '' }}>EUR</option>
                        </select>
                        @error('currency')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="duration_hours" class="block text-sm font-medium text-gray-700">Duration (Hours)</label>
                        <input type="number" name="duration_hours" id="duration_hours" min="1" max="24"
                               value="{{ old('duration_hours', $package->duration_hours) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('duration_hours')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Target Clients -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Target Clients</h3>
                
                <div>
                    <label for="target_client_type" class="block text-sm font-medium text-gray-700">Client Type *</label>
                    <select name="target_client_type" id="target_client_type" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @foreach($clientTypes as $value => $label)
                            <option value="{{ $value }}" {{ old('target_client_type', $package->target_client_type) === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('target_client_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Service Management -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Manage Services</h3>
                <p class="text-sm text-gray-500 mb-6">Select the services to include in this package.</p>
                
                <div class="space-y-6">
                    @foreach($services->groupBy('category') as $category => $categoryServices)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="text-md font-semibold text-gray-800 mb-3 capitalize">{{ str_replace('_', ' ', $category) }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($categoryServices as $service)
                                    <label for="service-{{ $service->id }}" class="flex items-center p-3 rounded-lg border hover:bg-gray-50 transition-colors cursor-pointer {{ in_array($service->id, $packageServiceIds) ? 'bg-indigo-50 border-indigo-300' : 'bg-white' }}">
                                        <input type="checkbox" 
                                               name="services[]" 
                                               id="service-{{ $service->id }}" 
                                               value="{{ $service->id }}"
                                               {{ in_array($service->id, $packageServiceIds) ? 'checked' : '' }}
                                               class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <span class="ml-3 text-sm font-medium text-gray-800">{{ $service->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Status</h3>
                
                <div>
                    <label for="is_active" class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                               {{ old('is_active', $package->is_active) ? 'checked' : '' }}
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">Active Package</span>
                    </label>
                    @error('is_active')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('headtech.packages.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                Update Package
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.getElementById('is_custom_quote').addEventListener('change', function() {
    const priceFields = document.getElementById('price_fields');
    const priceInput = document.getElementById('price');
    
    if (this.checked) {
        priceFields.classList.add('hidden');
        priceInput.removeAttribute('required');
        priceInput.value = '';
    } else {
        priceFields.classList.remove('hidden');
        priceInput.setAttribute('required', 'required');
    }
});
</script>
@endpush

@endsection 