{{-- File Path: resources/views/admin/business-partners/edit.blade.php --}}

@extends('layouts.admin')

@section('title', 'Edit Business Partner - ' . $businessPartner->name)

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Edit Business Partner: {{ $businessPartner->name }}
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <a href="{{ route('admin.business-partners.index') }}" class="text-indigo-600 hover:text-indigo-500">Business Partners</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.business-partners.show', $businessPartner) }}" class="text-indigo-600 hover:text-indigo-500">{{ $businessPartner->name }}</a>
                <span class="mx-2">/</span>
                <span>Edit</span>
            </div>
        </div>
    </div>
    <div class="mt-4 flex space-x-3 md:ml-4 md:mt-0">
        <a href="{{ route('admin.business-partners.show', $businessPartner) }}" 
           class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 010-1.06L10.94 8.25H4a.75.75 0 010-1.5h6.94L7.72 3.53a.75.75 0 011.06-1.06l4.5 4.5a.75.75 0 010 1.06l-4.5 4.5a.75.75 0 01-1.06 0z" clip-rule="evenodd" />
            </svg>
            Back to Partner
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.business-partners.update', $businessPartner) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')

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
                        <!-- Company Name -->
                        <div class="col-span-6">
                            <label for="name" class="block text-sm font-medium text-gray-700">Company Name *</label>
                            <input type="text" name="name" id="name" required
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('name', $businessPartner->name) }}">
                            @error('name')
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
                                    <option value="{{ $value }}" {{ old('type', $businessPartner->type) == $value ? 'selected' : '' }}>
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
                                   value="{{ old('registration_number', $businessPartner->registration_number) }}">
                            @error('registration_number')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="email" class="block text-sm font-medium text-gray-700">Company Email *</label>
                            <input type="email" name="email" id="email" required
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('email', $businessPartner->email) }}">
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="tel" name="phone" id="phone"
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('phone', $businessPartner->phone) }}">
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
                                   value="{{ old('website', $businessPartner->website) }}">
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
                                      class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('address', $businessPartner->address) }}</textarea>
                            @error('address')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- City -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="city" class="block text-sm font-medium text-gray-700">City *</label>
                            <input type="text" name="city" id="city" required
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('city', $businessPartner->city) }}">
                            @error('city')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Country -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="country" class="block text-sm font-medium text-gray-700">Country *</label>
                            <input type="text" name="country" id="country" required
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('country', $businessPartner->country) }}">
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
                                   value="{{ old('contact_person', $businessPartner->contact_person) }}">
                            @error('contact_person')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contact Email -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="contact_email" class="block text-sm font-medium text-gray-700">Contact Email *</label>
                            <input type="email" name="contact_email" id="contact_email" required
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('contact_email', $businessPartner->contact_email) }}">
                            @error('contact_email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contact Phone -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="contact_phone" class="block text-sm font-medium text-gray-700">Contact Phone</label>
                            <input type="tel" name="contact_phone" id="contact_phone"
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('contact_phone', $businessPartner->contact_phone) }}">
                            @error('contact_phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Partnership Details -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Partnership Details</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Terms and conditions of the partnership agreement.
                    </p>
                </div>
                <div class="mt-5 md:col-span-2 md:mt-0">
                    <div class="grid grid-cols-6 gap-6">
                        <!-- Tier -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="tier" class="block text-sm font-medium text-gray-700">Partnership Tier *</label>
                            <select name="tier" id="tier" required
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Select Tier</option>
                                @foreach($tiers as $value => $label)
                                    <option value="{{ $value }}" {{ old('tier', $businessPartner->tier) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tier')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Discount Percentage -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="discount_percentage" class="block text-sm font-medium text-gray-700">Discount Percentage (%)</label>
                            <input type="number" name="discount_percentage" id="discount_percentage" min="0" max="50" step="0.01"
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('discount_percentage', $businessPartner->discount_percentage) }}">
                            @error('discount_percentage')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Billing Cycle -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="billing_cycle" class="block text-sm font-medium text-gray-700">Billing Cycle *</label>
                            <select name="billing_cycle" id="billing_cycle" required
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Select Billing Cycle</option>
                                <option value="monthly" {{ old('billing_cycle', $businessPartner->billing_cycle) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="quarterly" {{ old('billing_cycle', $businessPartner->billing_cycle) == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                <option value="annually" {{ old('billing_cycle', $businessPartner->billing_cycle) == 'annually' ? 'selected' : '' }}>Annually</option>
                            </select>
                            @error('billing_cycle')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Credit Limit -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="credit_limit" class="block text-sm font-medium text-gray-700">Credit Limit (RWF)</label>
                            <input type="number" name="credit_limit" id="credit_limit" min="0" step="0.01"
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('credit_limit', $businessPartner->credit_limit) }}">
                            @error('credit_limit')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Partnership Start Date -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="partnership_start_date" class="block text-sm font-medium text-gray-700">Partnership Start Date *</label>
                            <input type="date" name="partnership_start_date" id="partnership_start_date" required
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('partnership_start_date', $businessPartner->partnership_start_date->format('Y-m-d')) }}">
                            @error('partnership_start_date')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contract End Date -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="contract_end_date" class="block text-sm font-medium text-gray-700">Contract End Date</label>
                            <input type="date" name="contract_end_date" id="contract_end_date"
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('contract_end_date', $businessPartner->contract_end_date ? $businessPartner->contract_end_date->format('Y-m-d') : '') }}">
                            @error('contract_end_date')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Partnership Status -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="partnership_status" class="block text-sm font-medium text-gray-700">Partnership Status *</label>
                            <select name="partnership_status" id="partnership_status" required
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Select Status</option>
                                <option value="active" {{ old('partnership_status', $businessPartner->partnership_status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('partnership_status', $businessPartner->partnership_status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="suspended" {{ old('partnership_status', $businessPartner->partnership_status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                            @error('partnership_status')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="col-span-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Partnership Notes</label>
                            <textarea name="notes" id="notes" rows="3"
                                      class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                      placeholder="Any additional notes about this partnership...">{{ old('notes', $businessPartner->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System & Sync -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">System & Sync</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Configure deployment type and data synchronization settings.
                    </p>
                </div>
                <div class="mt-5 md:col-span-2 md:mt-0">
                    <div class="grid grid-cols-6 gap-6">

                        <!-- Deployment Type -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="deployment_type" class="block text-sm font-medium text-gray-700">Deployment Type</label>
                            <select name="deployment_type" id="deployment_type" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="centralized" {{ old('deployment_type', $businessPartner->deployment_type) == 'centralized' ? 'selected' : '' }}>Centralized</option>
                                <option value="dedicated" {{ old('deployment_type', $businessPartner->deployment_type) == 'dedicated' ? 'selected' : '' }}>Dedicated</option>
                            </select>
                        </div>

                        <!-- Failover Active -->
                        <div class="col-span-6 sm:col-span-3 flex items-center pt-6">
                            <div class="flex items-center">
                                <input id="failover_active" name="failover_active" type="checkbox" value="1" {{ old('failover_active', $businessPartner->failover_active) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <label for="failover_active" class="ml-2 block text-sm font-medium text-gray-900">Failover Active</label>
                            </div>
                        </div>

                        <!-- Sync Settings -->
                        <div id="sync-settings" class="col-span-6 grid grid-cols-6 gap-6" style="{{ old('deployment_type', $businessPartner->deployment_type) == 'dedicated' ? '' : 'display: none;' }}">
                            <!-- Sync URL -->
                            <div class="col-span-6">
                                <label for="sync_url" class="block text-sm font-medium text-gray-700">Sync URL</label>
                                <input type="url" name="sync_url" id="sync_url" value="{{ old('sync_url', $businessPartner->sync_url) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <!-- API Key -->
                            <div class="col-span-6 sm:col-span-3">
                                <label for="api_key" class="block text-sm font-medium text-gray-700">API Key</label>
                                <input type="text" name="api_key" id="api_key" value="{{ old('api_key', $businessPartner->api_key) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <!-- Sync Type -->
                            <div class="col-span-6 sm:col-span-3">
                                <label for="sync_type" class="block text-sm font-medium text-gray-700">Sync Type</label>
                                <select name="sync_type" id="sync_type" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="public_api" {{ old('sync_type', $businessPartner->sync_type) == 'public_api' ? 'selected' : '' }}>Public API</option>
                                    <option value="vpn" {{ old('sync_type', $businessPartner->sync_type) == 'vpn' ? 'selected' : '' }}>VPN</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Partnership Statistics (Read-only) -->
        <div class="bg-gray-50 shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Partnership Statistics</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Current partnership performance and activity summary.
                    </p>
                </div>
                <div class="mt-5 md:col-span-2 md:mt-0">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="bg-white p-4 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">Total Inspections</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $businessPartner->getTotalInspections() }}</dd>
                        </div>
                        <div class="bg-white p-4 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">This Month</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $businessPartner->getCurrentMonthInspections() }}</dd>
                        </div>
                        <div class="bg-white p-4 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">Total Spent</dt>
                            <dd class="mt-1 text-xl font-semibold text-gray-900">{{ number_format($businessPartner->getTotalAmountSpent()) }} RWF</dd>
                        </div>
                        <div class="bg-white p-4 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">Partnership Duration</dt>
                            <dd class="mt-1 text-xl font-semibold text-gray-900">{{ $businessPartner->getPartnershipDurationInMonths() }} months</dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="flex justify-between">
                <div>
                    <!-- Delete Button -->
                    <button type="button" onclick="deletePartner()" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete Partner
                    </button>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.business-partners.show', $businessPartner) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M7.707 10.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V6h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2h5v5.586l-1.293-1.293z" />
                        </svg>
                        Update Business Partner
                    </button>
                </div>
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
function deletePartner() {
    if (!confirm('Are you sure you want to delete this business partner? This action cannot be undone and will remove all associated data.')) {
        return;
    }

    if (!confirm('This will permanently delete the business partner and all related records. Are you absolutely sure?')) {
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.business-partners.destroy", $businessPartner) }}';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    
    const methodField = document.createElement('input');
    methodField.type = 'hidden';
    methodField.name = '_method';
    methodField.value = 'DELETE';
    
    form.appendChild(csrfToken);
    form.appendChild(methodField);
    document.body.appendChild(form);
    form.submit();
}

// Form validation and enhancement
document.addEventListener('DOMContentLoaded', function() {
    // Contract end date validation
    const startDateField = document.getElementById('partnership_start_date');
    const endDateField = document.getElementById('contract_end_date');
    
    function validateDates() {
        if (startDateField.value && endDateField.value) {
            const startDate = new Date(startDateField.value);
            const endDate = new Date(endDateField.value);
            
            if (endDate <= startDate) {
                endDateField.setCustomValidity('Contract end date must be after the partnership start date');
            } else {
                endDateField.setCustomValidity('');
            }
        }
    }
    
    startDateField.addEventListener('change', validateDates);
    endDateField.addEventListener('change', validateDates);
    
    // Tier-based discount suggestions
    const tierField = document.getElementById('tier');
    const discountField = document.getElementById('discount_percentage');
    
    const tierDiscounts = {
        'bronze': 2.5,
        'silver': 5.0,
        'gold': 7.5,
        'platinum': 10.0
    };
    
    tierField.addEventListener('change', function() {
        if (this.value && tierDiscounts[this.value]) {
            const suggestedDiscount = tierDiscounts[this.value];
            const currentDiscount = parseFloat(discountField.value) || 0;
            
            if (currentDiscount === 0 || confirm(`Would you like to set the discount to ${suggestedDiscount}% (recommended for ${this.value} tier)?`)) {
                discountField.value = suggestedDiscount;
            }
        }
    });
    
    // Real-time partnership duration calculation
    function updatePartnershipDuration() {
        if (startDateField.value) {
            const startDate = new Date(startDateField.value);
            const currentDate = new Date();
            const diffTime = Math.abs(currentDate - startDate);
            const diffMonths = Math.ceil(diffTime / (1000 * 60 * 60 * 24 * 30.44)); // Average days per month
            
            // You could display this somewhere if needed
            console.log(`Partnership duration: ${diffMonths} months`);
        }
    }
    
    startDateField.addEventListener('change', updatePartnershipDuration);
    
    // Form submission validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        // Additional validation before submission
        const requiredFields = form.querySelectorAll('[required]');
        let hasErrors = false;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('border-red-500');
                hasErrors = true;
            } else {
                field.classList.remove('border-red-500');
            }
        });
        
        if (hasErrors) {
            e.preventDefault();
            alert('Please fill in all required fields');
            return false;
        }
        
        // Show loading state
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Updating...
        `;
        
        // Restore button state if form submission fails (though this might not be reached)
        setTimeout(() => {
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        }, 10000);
    });
    
    // Auto-save draft functionality (optional)
    let autoSaveTimeout;
    const formInputs = form.querySelectorAll('input, textarea, select');
    
    formInputs.forEach(input => {
        input.addEventListener('input', function() {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(() => {
                // You could implement auto-save functionality here
                console.log('Auto-saving draft...');
            }, 2000);
        });
    });
    
    // Unsaved changes warning
    let formChanged = false;
    formInputs.forEach(input => {
        input.addEventListener('change', function() {
            formChanged = true;
        });
    });
    
    window.addEventListener('beforeunload', function(e) {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
            return e.returnValue;
        }
    });
    
    // Reset formChanged flag when form is submitted
    form.addEventListener('submit', function() {
        formChanged = false;
    });
    
    // Credit limit formatting
    const creditLimitField = document.getElementById('credit_limit');
    creditLimitField.addEventListener('input', function() {
        // Remove non-numeric characters except decimal point
        let value = this.value.replace(/[^\d.]/g, '');
        
        // Ensure only one decimal point
        const parts = value.split('.');
        if (parts.length > 2) {
            value = parts[0] + '.' + parts.slice(1).join('');
        }
        
        this.value = value;
    });
    
    // Phone number formatting
    const phoneFields = document.querySelectorAll('input[type="tel"]');
    phoneFields.forEach(field => {
        field.addEventListener('input', function() {
            // Basic phone number formatting for Rwanda (+250)
            let value = this.value.replace(/\D/g, '');
            
            if (value.startsWith('250')) {
                value = '+' + value;
            } else if (value.startsWith('0')) {
                value = '+250' + value.substring(1);
            } else if (value.length > 0 && !value.startsWith('250')) {
                value = '+250' + value;
            }
            
            this.value = value;
        });
    });
});

// Partnership status change confirmation
document.getElementById('partnership_status').addEventListener('change', function() {
    const currentStatus = '{{ $businessPartner->partnership_status }}';
    const newStatus = this.value;
    
    if (currentStatus !== newStatus) {
        if (newStatus === 'suspended') {
            if (!confirm('Suspending this partnership will prevent new inspection requests. Are you sure?')) {
                this.value = currentStatus;
                return;
            }
        } else if (newStatus === 'inactive') {
            if (!confirm('Setting partnership to inactive will disable most functionality. Are you sure?')) {
                this.value = currentStatus;
                return;
            }
        }
    }
});

// Discount percentage validation
document.getElementById('discount_percentage').addEventListener('input', function() {
    const value = parseFloat(this.value);
    if (value > 50) {
        alert('Discount percentage cannot exceed 50%');
        this.value = 50;
    } else if (value < 0) {
        alert('Discount percentage cannot be negative');
        this.value = 0;
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const deploymentType = document.getElementById('deployment_type');
    const syncSettings = document.getElementById('sync-settings');

    deploymentType.addEventListener('change', function() {
        if (this.value === 'dedicated') {
            syncSettings.style.display = 'grid';
        } else {
            syncSettings.style.display = 'none';
        }
    });

    // Trigger change on load to set initial state
    deploymentType.dispatchEvent(new Event('change'));
});
</script>
@endpush

@endsection