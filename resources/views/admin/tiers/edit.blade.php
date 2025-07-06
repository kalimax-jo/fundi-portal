@extends('layouts.admin')

@section('title', 'Edit Tier')

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <div class="flex items-center">
            <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-yellow-100 mr-3">
                <svg class="h-6 w-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 17.75l-6.16 3.24 1.18-6.88L2 9.76l6.92-1.01L12 2.5l3.08 6.25L22 9.76l-5.02 4.35 1.18 6.88z" />
                </svg>
            </span>
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                Edit Tier
            </h2>
        </div>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                Update partner tier details, quota, price, and allowed packages
            </div>
        </div>
    </div>
    <div class="mt-4 flex md:ml-4 md:mt-0">
        <a href="{{ route('admin.tiers.index') }}"
           class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to Tiers
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="bg-white shadow rounded-lg p-6 mb-8 w-full">
    <form method="POST" action="{{ route('admin.tiers.update', $tier) }}" class="space-y-8 w-full">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Tier Name *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $tier->name) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                       placeholder="e.g., Bronze">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="request_quota" class="block text-sm font-medium text-gray-700">Request Quota *</label>
                <input type="number" name="request_quota" id="request_quota" min="1" value="{{ old('request_quota', $tier->request_quota) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                       placeholder="e.g., 20">
                @error('request_quota')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700">Price (RWF) *</label>
                <input type="number" name="price" id="price" step="0.01" min="0" value="{{ old('price', $tier->price) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                       placeholder="0.00">
                @error('price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="md:col-span-2">
                <label for="inspection_packages" class="block text-sm font-medium text-gray-700">Allowed Packages *</label>
                <select name="inspection_packages[]" id="inspection_packages" multiple
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @foreach($packages as $package)
                        <option value="{{ $package->id }}" {{ in_array($package->id, old('inspection_packages', $tier->inspectionPackages->pluck('id')->toArray())) ? 'selected' : '' }}>
                            {{ $package->display_name ?? $package->name }}
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Hold Ctrl (Windows) or Command (Mac) to select multiple packages.</p>
                @error('inspection_packages')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="flex justify-end">
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Update Tier
            </button>
        </div>
    </form>
</div>
@endsection 