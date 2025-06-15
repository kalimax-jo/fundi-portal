{{-- File Path: resources/views/admin/properties/show.blade.php --}}

@extends('layouts.admin')

@section('title', 'Property Details - ' . $property->property_code)

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Property: {{ $property->property_code }}
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <a href="{{ route('admin.properties.index') }}" class="text-indigo-600 hover:text-indigo-500">Properties</a>
                <span class="mx-2">/</span>
                <span>{{ $property->property_code }}</span>
            </div>
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <svg class="mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                {{ $property->district }}{{ $property->sector ? ', ' . $property->sector : '' }}
            </div>
        </div>
    </div>
    <div class="mt-4 flex space-x-3 md:ml-4 md:mt-0">
        <a href="{{ route('admin.properties.edit', $property) }}" 
           class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z" />
            </svg>
            Edit Property
        </a>
        <a href="{{ route('admin.properties.inspection-history', $property) }}" 
           class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L8.23 10.661a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
            </svg>
            Inspection History
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Property Overview Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Inspections -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Inspections</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['total_inspections'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed Inspections -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Completed</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['completed_inspections'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Property Age -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Property Age</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ $stats['property_age'] ? $stats['property_age'] . ' years' : 'Unknown' }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Last Inspection -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 {{ $property->needsInspection() ? 'text-red-400' : 'text-green-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Last Inspection</dt>
                            <dd class="text-sm font-medium {{ $property->needsInspection() ? 'text-red-600' : 'text-gray-900' }}">
                                @if($property->last_inspection_date)
                                    {{ $stats['months_since_last_inspection'] }} months ago
                                @else
                                    Never inspected
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Property Information -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Property Information</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Complete details about this property.</p>
        </div>
        <div class="border-t border-gray-200">
            <dl>
                <!-- Owner Information -->
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Owner Information</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="space-y-1">
                            <p class="font-medium">{{ $property->owner_name ?: 'Not specified' }}</p>
                            @if($property->owner_phone)
                                <p class="text-gray-600">📞 {{ $property->owner_phone }}</p>
                            @endif
                            @if($property->owner_email)
                                <p class="text-gray-600">✉️ {{ $property->owner_email }}</p>
                            @endif
                        </div>
                    </dd>
                </div>

                <!-- Property Type -->
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Property Type</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $property->property_type === 'residential' ? 'bg-blue-100 text-blue-800' : 
                                   ($property->property_type === 'commercial' ? 'bg-green-100 text-green-800' : 
                                   ($property->property_type === 'industrial' ? 'bg-yellow-100 text-yellow-800' : 'bg-purple-100 text-purple-800')) }}">
                                {{ $property->getTypeDisplayName() }}
                            </span>
                            @if($property->property_subtype)
                                <span class="text-gray-600">• {{ $property->getSubtypeDisplayName() }}</span>
                            @endif
                        </div>
                    </dd>
                </div>

                <!-- Address -->
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Address</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="space-y-1">
                            <p>{{ $property->address }}</p>
                            <p class="text-gray-600">
                                {{ $property->district }}{{ $property->sector ? ', ' . $property->sector : '' }}{{ $property->cell ? ', ' . $property->cell : '' }}
                            </p>
                            @if($property->latitude && $property->longitude)
                                <p class="text-gray-600">
                                    📍 {{ $property->latitude }}, {{ $property->longitude }}
                                    <a href="https://maps.google.com?q={{ $property->latitude }},{{ $property->longitude }}" 
                                       target="_blank" class="text-indigo-600 hover:text-indigo-500 ml-2">View on Map</a>
                                </p>
                            @endif
                        </div>
                    </dd>
                </div>

                <!-- Property Specifications -->
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Specifications</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="grid grid-cols-2 gap-4">
                            @if($property->built_year)
                                <div>
                                    <span class="font-medium">Year Built:</span> {{ $property->built_year }}
                                </div>
                            @endif
                            @if($property->total_area_sqm)
                                <div>
                                    <span class="font-medium">Total Area:</span> {{ number_format($property->total_area_sqm) }} m²
                                </div>
                            @endif
                            @if($property->floors_count)
                                <div>
                                    <span class="font-medium">Floors:</span> {{ $property->floors_count }}
                                </div>
                            @endif
                            @if($property->bedrooms_count)
                                <div>
                                    <span class="font-medium">Bedrooms:</span> {{ $property->bedrooms_count }}
                                </div>
                            @endif
                            @if($property->bathrooms_count)
                                <div>
                                    <span class="font-medium">Bathrooms:</span> {{ $property->bathrooms_count }}
                                </div>
                            @endif
                            @if($property->market_value)
                                <div>
                                    <span class="font-medium">Market Value:</span> RWF {{ number_format($property->market_value) }}
                                </div>
                            @endif
                            @if($stats['value_per_sqm'])
                                <div>
                                    <span class="font-medium">Value per m²:</span> RWF {{ number_format($stats['value_per_sqm']) }}
                                </div>
                            @endif
                        </div>
                    </dd>
                </div>

                <!-- Additional Notes -->
                @if($property->additional_notes)
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Additional Notes</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $property->additional_notes }}
                    </dd>
                </div>
                @endif

                <!-- Inspection Status -->
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Inspection Status</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="space-y-2">
                            @if($property->needsInspection())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    🚨 Needs Inspection
                                </span>
                                <p class="text-sm text-gray-600">
                                    This property hasn't been inspected in over 12 months or has never been inspected.
                                </p>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ✅ Recently Inspected
                                </span>
                                @if($property->last_inspection_date)
                                    <p class="text-sm text-gray-600">
                                        Last inspected on {{ $property->last_inspection_date->format('F j, Y') }} 
                                        ({{ $stats['months_since_last_inspection'] }} months ago)
                                    </p>
                                @endif
                            @endif
                        </div>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Recent Inspection Requests -->
    @if($property->inspectionRequests->count() > 0)
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Inspection Requests</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Latest inspection requests for this property.</p>
        </div>
        <div class="border-t border-gray-200">
            <ul class="divide-y divide-gray-200">
                @foreach($property->inspectionRequests->take(5) as $inspection)
                <li class="px-4 py-4 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center min-w-0 flex-1">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-indigo-600 truncate">
                                    {{ $inspection->request_number }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ $inspection->package->name ?? 'N/A' }} • 
                                    {{ ucfirst($inspection->purpose) }} • 
                                    {{ $inspection->created_at->format('M j, Y') }}
                                </p>
                            </div>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex items-center space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $inspection->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($inspection->status === 'assigned' ? 'bg-blue-100 text-blue-800' : 
                                   ($inspection->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ ucfirst($inspection->status) }}
                            </span>
                            <a href="{{ route('admin.inspection-requests.show', $inspection) }}" 
                               class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                                View
                            </a>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
            @if($property->inspectionRequests->count() > 5)
            <div class="px-4 py-3 sm:px-6 border-t border-gray-200">
                <a href="{{ route('admin.properties.inspection-history', $property) }}" 
                   class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                    View all {{ $property->inspectionRequests->count() }} inspection requests →
                </a>
            </div>
            @endif
        </div>
    </div>
    @else
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Inspection History</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">No inspection requests found for this property.</p>
        </div>
        <div class="border-t border-gray-200 px-4 py-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No inspections yet</h3>
            <p class="mt-1 text-sm text-gray-500">This property has not been inspected yet.</p>
            <div class="mt-6">
                <a href="{{ route('admin.inspection-requests.create') }}?property_id={{ $property->id }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Schedule Inspection
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Quick Actions</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Common actions for this property.</p>
        </div>
        <div class="border-t border-gray-200">
            <ul class="divide-y divide-gray-200">
                <li>
                    <a href="{{ route('admin.properties.edit', $property) }}" 
                       class="block px-4 py-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-600">Edit Property Details</p>
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.properties.inspection-history', $property) }}" 
                       class="block px-4 py-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-600">View Full Inspection History</p>
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.inspection-requests.create') }}?property_id={{ $property->id }}" 
                       class="block px-4 py-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-600">Schedule New Inspection</p>
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </a>
                </li>
                @if($property->latitude && $property->longitude)
                <li>
                    <a href="https://maps.google.com?q={{ $property->latitude }},{{ $property->longitude }}" 
                       target="_blank" class="block px-4 py-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-600">View on Google Maps</p>
                            <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </div>
                    </a>
                </li>
                @endif
                <li>
                    <button onclick="verifyProperty({{ $property->id }})" 
                            class="w-full text-left px-4 py-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-600">Verify Property Information</p>
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </button>
                </li>
                <li>
                    <button onclick="printProperty()" 
                            class="w-full text-left px-4 py-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-600">Print Property Details</p>
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
function verifyProperty(propertyId) {
    if (!confirm('Are you sure you want to verify this property information?')) {
        return;
    }

    fetch(`/admin/properties/${propertyId}/verify`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Property verified successfully!');
            // You can add visual feedback here
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while verifying the property.');
    });
}

function printProperty() {
    // Create a print-friendly version
    const printWindow = window.open('', '_blank');
    const propertyContent = document.querySelector('.space-y-6').cloneNode(true);
    
    // Remove action buttons and other non-essential elements for printing
    const actionsToRemove = propertyContent.querySelectorAll('button, .quick-actions, .mt-4.flex.space-x-3');
    actionsToRemove.forEach(element => element.remove());
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Property Details - {{ $property->property_code }}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .bg-white { background: white; }
                .shadow { box-shadow: none; border: 1px solid #ddd; }
                .rounded-lg { border-radius: 8px; }
                .px-4, .px-6 { padding-left: 1rem; padding-right: 1rem; }
                .py-5 { padding-top: 1.25rem; padding-bottom: 1.25rem; }
                .text-lg { font-size: 1.125rem; }
                .font-medium { font-weight: 500; }
                .text-sm { font-size: 0.875rem; }
                .text-gray-500 { color: #6b7280; }
                .text-gray-900 { color: #111827; }
                .grid { display: grid; }
                .grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
                .gap-4 { gap: 1rem; }
                .space-y-1 > * + * { margin-top: 0.25rem; }
                .space-y-2 > * + * { margin-top: 0.5rem; }
                @media print {
                    .no-print { display: none !important; }
                }
            </style>
        </head>
        <body>
            <h1>Property Details - {{ $property->property_code }}</h1>
            <p>Generated on: ${new Date().toLocaleDateString()}</p>
            ${propertyContent.innerHTML}
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.print();
}
</script>
@endpush

@endsection