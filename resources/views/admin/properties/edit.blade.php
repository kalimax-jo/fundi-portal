{{-- File Path: resources/views/admin/properties/edit.blade.php --}}

@extends('layouts.admin')

@section('title', 'Edit Property - ' . $property->property_code)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
      crossorigin=""/>
<style>
#map {
    height: 400px !important;
    width: 100% !important;
    border: 2px solid #d1d5db;
    border-radius: 8px;
    z-index: 1;
}
.leaflet-container {
    height: 400px !important;
    width: 100% !important;
}
</style>
@endpush

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Edit Property: {{ $property->property_code }}
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <a href="{{ route('admin.properties.index') }}" class="text-indigo-600 hover:text-indigo-500">Properties</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.properties.show', $property) }}" class="text-indigo-600 hover:text-indigo-500">{{ $property->property_code }}</a>
                <span class="mx-2">/</span>
                <span>Edit</span>
            </div>
        </div>
    </div>
    <div class="mt-4 flex space-x-3 md:ml-4 md:mt-0">
        <a href="{{ route('admin.properties.show', $property) }}" 
           class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            Back to Property
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    <form action="{{ route('admin.properties.update', $property) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Owner Information -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Owner Information</h3>
                <p class="text-sm text-gray-500 mb-6">Details about the property owner.</p>
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Owner Name -->
                    <div class="sm:col-span-2">
                        <label for="owner_name" class="block text-sm font-medium text-gray-700">Owner Name *</label>
                        <input type="text" 
                               name="owner_name" 
                               id="owner_name" 
                               value="{{ old('owner_name', $property->owner_name) }}" 
                               required
                               placeholder="Enter full owner name"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('owner_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Owner Phone -->
                    <div>
                        <label for="owner_phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input type="tel" 
                               name="owner_phone" 
                               id="owner_phone" 
                               value="{{ old('owner_phone', $property->owner_phone) }}"
                               placeholder="e.g., +250 788 123 456"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('owner_phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Owner Email -->
                    <div>
                        <label for="owner_email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input type="email" 
                               name="owner_email" 
                               id="owner_email" 
                               value="{{ old('owner_email', $property->owner_email) }}"
                               placeholder="owner@example.com"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('owner_email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Property Details -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Property Details</h3>
                <p class="text-sm text-gray-500 mb-6">Basic property information and type.</p>
                
                <!-- Property Code Display -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <label class="block text-sm font-medium text-gray-700">Property Code</label>
                    <p class="mt-1 text-lg font-mono font-semibold text-indigo-600">{{ $property->property_code }}</p>
                    <p class="text-xs text-gray-500">This code is automatically generated and cannot be changed.</p>
                </div>
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <!-- Property Type -->
                    <div>
                        <label for="property_type" class="block text-sm font-medium text-gray-700">Property Type *</label>
                        <select name="property_type" 
                                id="property_type" 
                                required 
                                onchange="updateSubtypes()"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Select Type</option>
                            @foreach($propertyTypes as $key => $value)
                                <option value="{{ $key }}" {{ old('property_type', $property->property_type) === $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                        @error('property_type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Property Subtype -->
                    <div>
                        <label for="property_subtype" class="block text-sm font-medium text-gray-700">Property Subtype</label>
                        <select name="property_subtype" 
                                id="property_subtype" 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Select Subtype</option>
                        </select>
                        @error('property_subtype')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Built Year -->
                    <div>
                        <label for="built_year" class="block text-sm font-medium text-gray-700">Year Built</label>
                        <input type="number" 
                               name="built_year" 
                               id="built_year" 
                               value="{{ old('built_year', $property->built_year) }}" 
                               min="1800" 
                               max="{{ date('Y') + 5 }}" 
                               placeholder="e.g., 2020"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('built_year')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Total Area -->
                    <div>
                        <label for="total_area_sqm" class="block text-sm font-medium text-gray-700">Total Area (m²)</label>
                        <input type="number" 
                               name="total_area_sqm" 
                               id="total_area_sqm" 
                               value="{{ old('total_area_sqm', $property->total_area_sqm) }}" 
                               step="0.01" 
                               min="0" 
                               placeholder="e.g., 150.5"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('total_area_sqm')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Market Value -->
                    <div class="sm:col-span-2">
                        <label for="market_value" class="block text-sm font-medium text-gray-700">Market Value (RWF)</label>
                        <input type="number" 
                               name="market_value" 
                               id="market_value" 
                               value="{{ old('market_value', $property->market_value) }}" 
                               step="0.01" 
                               min="0" 
                               placeholder="e.g., 50000000"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('market_value')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Location Information -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Location Information</h3>
                <p class="text-sm text-gray-500 mb-6">Property address and location details. Click on the map to update coordinates.</p>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Column: Address Fields -->
                    <div class="space-y-6">
                        <!-- Address -->
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700">Street Address *</label>
                            <textarea name="address" 
                                      id="address" 
                                      rows="3" 
                                      required
                                      placeholder="Enter the full street address..."
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('address', $property->address) }}</textarea>
                            @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <!-- Location Selectors -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- District -->
                            <div>
                                <label for="district" class="block text-sm font-medium text-gray-700">District *</label>
                                <select name="district" 
                                        id="district" 
                                        required 
                                        onchange="updateSectors()"
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Select District</option>
                                    @foreach($rwandaDistricts as $district => $sectors)
                                        <option value="{{ $district }}" {{ old('district', $property->district) === $district ? 'selected' : '' }}>
                                            {{ $district }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('district')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <!-- Sector -->
                            <div>
                                <label for="sector" class="block text-sm font-medium text-gray-700">Sector</label>
                                <select name="sector" 
                                        id="sector" 
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Select Sector</option>
                                </select>
                                @error('sector')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <!-- Cell -->
                            <div>
                                <label for="cell" class="block text-sm font-medium text-gray-700">Cell</label>
                                <input type="text" 
                                       name="cell" 
                                       id="cell" 
                                       value="{{ old('cell', $property->cell) }}"
                                       placeholder="Enter cell"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('cell')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <!-- GPS Coordinates -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="block text-sm font-medium text-gray-700">GPS Coordinates</label>
                                <div class="flex space-x-2">
                                    <button type="button" 
                                            onclick="getCurrentLocation()" 
                                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                        📍 Use My Location
                                    </button>
                                    <button type="button" 
                                            onclick="centerMapOnProperty()" 
                                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                        🏠 Show Property
                                    </button>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
                                    <input type="number" 
                                           name="latitude" 
                                           id="latitude" 
                                           value="{{ old('latitude', $property->latitude) }}" 
                                           step="0.00000001" 
                                           min="-90" 
                                           max="90" 
                                           readonly
                                           placeholder="Click map to set"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 sm:text-sm">
                                    @error('latitude')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
                                    <input type="number" 
                                           name="longitude" 
                                           id="longitude" 
                                           value="{{ old('longitude', $property->longitude) }}" 
                                           step="0.00000001" 
                                           min="-180" 
                                           max="180" 
                                           readonly
                                           placeholder="Click map to set"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 sm:text-sm">
                                    @error('longitude')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Interactive Map -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Interactive Map</label>
                        <div id="map"></div>
                        <p class="mt-2 text-sm text-gray-500">
                            💡 Click anywhere on the map to update the property location
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Property Specifications -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Property Specifications</h3>
                <p class="text-sm text-gray-500 mb-6">Detailed specifications of the property.</p>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <!-- Floors Count -->
                    <div>
                        <label for="floors_count" class="block text-sm font-medium text-gray-700">Number of Floors</label>
                        <input type="number" 
                               name="floors_count" 
                               id="floors_count" 
                               value="{{ old('floors_count', $property->floors_count ?? 1) }}" 
                               min="1" 
                               max="100" 
                               placeholder="e.g., 2"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('floors_count')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Bedrooms Count -->
                    <div>
                        <label for="bedrooms_count" class="block text-sm font-medium text-gray-700">Number of Bedrooms</label>
                        <input type="number" 
                               name="bedrooms_count" 
                               id="bedrooms_count" 
                               value="{{ old('bedrooms_count', $property->bedrooms_count) }}" 
                               min="0" 
                               max="50" 
                               placeholder="e.g., 3"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('bedrooms_count')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Bathrooms Count -->
                    <div>
                        <label for="bathrooms_count" class="block text-sm font-medium text-gray-700">Number of Bathrooms</label>
                        <input type="number" 
                               name="bathrooms_count" 
                               id="bathrooms_count" 
                               value="{{ old('bathrooms_count', $property->bathrooms_count) }}" 
                               min="0" 
                               max="50" 
                               placeholder="e.g., 2"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('bathrooms_count')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Additional Information</h3>
                <p class="text-sm text-gray-500 mb-6">Any additional notes about the property.</p>
                
                <div>
                    <label for="additional_notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                    <textarea name="additional_notes" 
                              id="additional_notes" 
                              rows="4"
                              placeholder="Any additional information about the property such as parking spaces, garden area, special features, etc..."
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('additional_notes', $property->additional_notes) }}</textarea>
                    @error('additional_notes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3 bg-gray-50 px-4 py-3 rounded-lg">
            <a href="{{ route('admin.properties.show', $property) }}" 
               class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" 
                    class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                Update Property
            </button>
        </div>
    </form>
</div>

<!-- Map Script -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
        crossorigin=""></script>

<script>
// Property subtypes mapping
const propertySubtypes = {
    residential: [
        { value: 'house', label: 'House' },
        { value: 'apartment', label: 'Apartment' },
        { value: 'villa', label: 'Villa' },
        { value: 'duplex', label: 'Duplex' },
        { value: 'townhouse', label: 'Townhouse' },
        { value: 'condo', label: 'Condominium' }
    ],
    commercial: [
        { value: 'office', label: 'Office Building' },
        { value: 'retail', label: 'Retail Space' },
        { value: 'restaurant', label: 'Restaurant' },
        { value: 'hotel', label: 'Hotel' },
        { value: 'shopping_center', label: 'Shopping Center' }
    ],
    industrial: [
        { value: 'warehouse', label: 'Warehouse' },
        { value: 'factory', label: 'Factory' },
        { value: 'manufacturing', label: 'Manufacturing Facility' }
    ],
    mixed: [
        { value: 'mixed_use', label: 'Mixed Use Building' }
    ]
};

// Rwanda districts and sectors data
const rwandaData = @json($rwandaDistricts);

// Map variables
let map;
let marker;

// Property coordinates
const propertyLat = {{ $property->latitude ?? 'null' }};
const propertyLng = {{ $property->longitude ?? 'null' }};

// Initialize map when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeMap();
    updateSubtypes();
    updateSectors();
    
    // Set existing coordinates if available
    if (propertyLat && propertyLng) {
        setMarker(propertyLat, propertyLng);
        map.setView([propertyLat, propertyLng], 15);
    }
});

function initializeMap() {
    // Use property location or Rwanda center
    let initialView = [-1.9403, 29.8739];
    let initialZoom = 9;
    
    if (propertyLat && propertyLng) {
        initialView = [propertyLat, propertyLng];
        initialZoom = 15;
    }
    
    map = L.map('map').setView(initialView, initialZoom);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);
    
    map.on('click', function(e) {
        setMarker(e.latlng.lat, e.latlng.lng);
    });
    
    setTimeout(function() {
        map.invalidateSize();
    }, 100);
}

function setMarker(lat, lng) {
    if (marker) {
        map.removeLayer(marker);
    }
    
    marker = L.marker([lat, lng]).addTo(map);
    marker.bindPopup(`<b>Property Location</b><br/>Lat: ${lat.toFixed(6)}<br/>Lng: ${lng.toFixed(6)}`).openPopup();
    
    document.getElementById('latitude').value = lat.toFixed(8);
    document.getElementById('longitude').value = lng.toFixed(8);
    
    // Visual feedback
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    
    latInput.style.backgroundColor = '#f0fdf4';
    lngInput.style.backgroundColor = '#f0fdf4';
    
    setTimeout(() => {
        latInput.style.backgroundColor = '#f9fafb';
        lngInput.style.backgroundColor = '#f9fafb';
    }, 2000);
}

function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            map.setView([lat, lng], 15);
            setMarker(lat, lng);
        }, function(error) {
            alert('Unable to get your current location. Please click on the map to set coordinates manually.');
        });
    } else {
        alert('Geolocation is not supported by this browser.');
    }
}

function centerMapOnProperty() {
    if (propertyLat && propertyLng) {
        map.setView([propertyLat, propertyLng], 15);
        if (!marker) {
            setMarker(propertyLat, propertyLng);
        }
    } else {
        alert('No existing coordinates found for this property.');
    }
}

function updateSubtypes() {
    const propertyType = document.getElementById('property_type').value;
    const subtypeSelect = document.getElementById('property_subtype');
    const currentSubtype = '{{ old('property_subtype', $property->property_subtype) }}';
    
    subtypeSelect.innerHTML = '<option value="">Select Subtype</option>';
    
    if (propertyType && propertySubtypes[propertyType]) {
        propertySubtypes[propertyType].forEach(subtype => {
            const option = document.createElement('option');
            option.value = subtype.value;
            option.textContent = subtype.label;
            if (currentSubtype === subtype.value) {
                option.selected = true;
            }
            subtypeSelect.appendChild(option);
        });
    }
}

function updateSectors() {
    const district = document.getElementById('district').value;
    const sectorSelect = document.getElementById('sector');
    const currentSector = '{{ old('sector', $property->sector) }}';
    
    sectorSelect.innerHTML = '<option value="">Select Sector</option>';
    
    if (district && rwandaData[district]) {
        rwandaData[district].forEach(sector => {
            const option = document.createElement('option');
            option.value = sector;
            option.textContent = sector;
            if (currentSector === sector) {
                option.selected = true;
            }
            sectorSelect.appendChild(option);
        });
    }
}
</script>

@endsection