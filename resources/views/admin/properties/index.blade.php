{{-- File Path: resources/views/admin/properties/index.blade.php --}}

@extends('layouts.admin')

@section('title', 'Properties Management')

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Properties Management
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-indigo-600">
                <svg class="mr-1.5 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
                {{ $stats['total_properties'] }} total properties
            </div>
        </div>
    </div>
    <div class="mt-4 flex space-x-3 md:ml-4 md:mt-0">
        <a href="{{ route('admin.properties.create') }}" 
           class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
            </svg>
            Add Property
        </a>
    </div>
</div>
@endsection

@section('content')
<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5 mb-8">
    <!-- Total Properties -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Properties</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_properties']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Residential -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Residential</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['residential_properties']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Commercial -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Commercial</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['commercial_properties']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Need Inspection -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Need Inspection</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['properties_needing_inspection']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Recently Inspected -->
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
                        <dt class="text-sm font-medium text-gray-500 truncate">Recent Inspections</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['properties_with_recent_inspection']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="bg-white shadow rounded-lg mb-6">
    <div class="px-4 py-5 sm:p-6">
        <form method="GET" action="{{ route('admin.properties.index') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <!-- Search -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       placeholder="Property code, owner, address..."
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>

            <!-- Property Type -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700">Property Type</label>
                <select name="type" id="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">All Types</option>
                    @foreach($propertyTypes as $key => $value)
                        <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- District -->
            <div>
                <label for="district" class="block text-sm font-medium text-gray-700">District</label>
                <select name="district" id="district" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">All Districts</option>
                    @foreach($districts as $district)
                        <option value="{{ $district }}" {{ request('district') === $district ? 'selected' : '' }}>
                            {{ $district }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Inspection Status -->
            <div>
                <label for="needs_inspection" class="block text-sm font-medium text-gray-700">Inspection Status</label>
                <select name="needs_inspection" id="needs_inspection" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">All Properties</option>
                    <option value="true" {{ request('needs_inspection') === 'true' ? 'selected' : '' }}>
                        Needs Inspection
                    </option>
                </select>
            </div>

            <!-- Actions -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Filter
                </button>
                <a href="{{ route('admin.properties.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Properties Table -->
<div class="bg-white shadow overflow-hidden sm:rounded-md">
    <div class="px-4 py-5 sm:px-6">
        <div class="flex justify-between items-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Properties</h3>
            <div class="flex items-center space-x-2">
                <!-- Sort Options -->
                <select onchange="updateSort(this.value)" class="text-sm border-gray-300 rounded-md">
                    <option value="created_at-desc" {{ request('sort') === 'created_at' && request('direction') === 'desc' ? 'selected' : '' }}>Newest First</option>
                    <option value="created_at-asc" {{ request('sort') === 'created_at' && request('direction') === 'asc' ? 'selected' : '' }}>Oldest First</option>
                    <option value="name-asc" {{ request('sort') === 'name' && request('direction') === 'asc' ? 'selected' : '' }}>Owner A-Z</option>
                    <option value="name-desc" {{ request('sort') === 'name' && request('direction') === 'desc' ? 'selected' : '' }}>Owner Z-A</option>
                    <option value="type-asc" {{ request('sort') === 'type' && request('direction') === 'asc' ? 'selected' : '' }}>Type A-Z</option>
                    <option value="location-asc" {{ request('sort') === 'location' && request('direction') === 'asc' ? 'selected' : '' }}>Location A-Z</option>
                    <option value="last_inspection-desc" {{ request('sort') === 'last_inspection' && request('direction') === 'desc' ? 'selected' : '' }}>Recently Inspected</option>
                    <option value="last_inspection-asc" {{ request('sort') === 'last_inspection' && request('direction') === 'asc' ? 'selected' : '' }}>Needs Inspection</option>
                </select>
            </div>
        </div>
    </div>

    <ul class="divide-y divide-gray-200">
        @forelse($properties as $property)
        <li class="px-4 py-4 sm:px-6 hover:bg-gray-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center min-w-0 flex-1">
                    <!-- Property Icon -->
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-lg flex items-center justify-center
                            {{ $property->property_type === 'residential' ? 'bg-blue-100' : 
                               ($property->property_type === 'commercial' ? 'bg-green-100' : 
                               ($property->property_type === 'industrial' ? 'bg-yellow-100' : 'bg-purple-100')) }}">
                            @if($property->property_type === 'residential')
                                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                            @elseif($property->property_type === 'commercial')
                                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            @elseif($property->property_type === 'industrial')
                                <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                </svg>
                            @else
                                <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" />
                                </svg>
                            @endif
                        </div>
                    </div>

                    <!-- Property Details -->
                    <div class="ml-4 min-w-0 flex-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-indigo-600 truncate">
                                    {{ $property->property_code }}
                                </p>
                                <p class="text-sm text-gray-900 font-medium">
                                    {{ $property->owner_name ?: 'Unknown Owner' }}
                                </p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <!-- Property Type Badge -->
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $property->property_type === 'residential' ? 'bg-blue-100 text-blue-800' : 
                                       ($property->property_type === 'commercial' ? 'bg-green-100 text-green-800' : 
                                       ($property->property_type === 'industrial' ? 'bg-yellow-100 text-yellow-800' : 'bg-purple-100 text-purple-800')) }}">
                                    {{ $property->getTypeDisplayName() }}
                                </span>
                                
                                <!-- Inspection Status Badge -->
                                @if($property->needsInspection())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Needs Inspection
                                    </span>
                                @elseif($property->last_inspection_date && $property->getMonthsSinceLastInspection() <= 6)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Recently Inspected
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mt-2 flex items-center text-sm text-gray-500">
                            <!-- Address -->
                            <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <p class="truncate">{{ $property->address }}</p>
                            
                            <span class="mx-2">•</span>
                            
                            <!-- District -->
                            <p>{{ $property->district }}</p>
                            
                            @if($property->total_area_sqm)
                                <span class="mx-2">•</span>
                                <p>{{ number_format($property->total_area_sqm) }} m²</p>
                            @endif
                            
                            @if($property->last_inspection_date)
                                <span class="mx-2">•</span>
                                <p>Last inspected {{ $property->last_inspection_date->diffForHumans() }}</p>
                            @else
                                <span class="mx-2">•</span>
                                <p class="text-red-600">Never inspected</p>
                            @endif
                        </div>

                        <!-- Property Specifications -->
                        @if($property->bedrooms_count || $property->bathrooms_count || $property->floors_count > 1)
                        <div class="mt-2 flex items-center text-sm text-gray-500">
                            @if($property->bedrooms_count)
                                <span class="inline-flex items-center">
                                    <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                                    </svg>
                                    {{ $property->bedrooms_count }} bed{{ $property->bedrooms_count !== 1 ? 's' : '' }}
                                </span>
                            @endif
                            
                            @if($property->bathrooms_count)
                                <span class="mx-2">•</span>
                                <span class="inline-flex items-center">
                                    <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                                    </svg>
                                    {{ $property->bathrooms_count }} bath{{ $property->bathrooms_count !== 1 ? 's' : '' }}
                                </span>
                            @endif
                            
                            @if($property->floors_count > 1)
                                <span class="mx-2">•</span>
                                <span>{{ $property->floors_count }} floor{{ $property->floors_count !== 1 ? 's' : '' }}</span>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="ml-4 flex-shrink-0 flex items-center space-x-2">
                    <!-- View Button -->
                    <a href="{{ route('admin.properties.show', $property) }}" 
                       class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                        View
                    </a>

                    <!-- Edit Button -->
                    <a href="{{ route('admin.properties.edit', $property) }}" 
                       class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-indigo-600 hover:bg-indigo-700">
                        Edit
                    </a>

                    <!-- Inspection History -->
                    <a href="{{ route('admin.properties.inspection-history', $property) }}" 
                       class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700">
                        History
                    </a>

                    <!-- Delete Button -->
                    <button onclick="deleteProperty({{ $property->id }})" 
                            class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700">
                        Delete
                    </button>
                </div>
            </div>
        </li>
        @empty
        <li class="px-4 py-8 text-center">
            <div class="text-sm text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <p class="mt-2">No properties found</p>
                <p class="mt-1">
                    <a href="{{ route('admin.properties.create') }}" class="text-indigo-600 hover:text-indigo-500">Add the first property</a>
                </p>
            </div>
        </li>
        @endforelse
    </ul>

    <!-- Pagination -->
    @if($properties->hasPages())
    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
        {{ $properties->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
function updateSort(value) {
    const [sort, direction] = value.split('-');
    const url = new URL(window.location);
    url.searchParams.set('sort', sort);
    url.searchParams.set('direction', direction);
    window.location = url;
}

function deleteProperty(propertyId) {
    if (!confirm('Are you sure you want to delete this property? This action cannot be undone.')) {
        return;
    }

    fetch(`/admin/properties/${propertyId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the property.');
    });
}
</script>
@endpush

@endsection