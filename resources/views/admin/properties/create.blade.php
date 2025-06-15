{{-- File Path: resources/views/admin/properties/create.blade.php --}}

@extends('layouts.admin')

@section('title', 'Add New Property')

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
.coordinate-input {
    transition: background-color 0.3s ease;
}
.coordinate-updated {
    background-color: #f0fdf4 !important;
    border-color: #22c55e !important;
}
</style>
@endpush

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Add New Property
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <a href="{{ route('admin.properties.index') }}" class="text-indigo-600 hover:text-indigo-500">Properties</a>
                <span class="mx-2">/</span>
                <span>Add New</span>
            </div>
        </div>
    </div>
    <div class="mt-4 flex space-x-3 md:ml-4 md:mt-0">
        <a href="{{ route('admin.properties.index') }}" 
           class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            Back to Properties
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    <form action="{{ route('admin.properties.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Owner Information -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Owner Information</h3>
                <p class="text-sm text-gray-500 mb-6">Details about the property owner.</p>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Owner Name -->
                    <div>
                        <label for="owner_name" class="block text-sm font-medium text-gray-700">Owner Name *</label>
                        <input type="text" 
                               name="owner_name" 
                               id="owner_name" 
                               value="{{ old('owner_name') }}" 
                               required 
                               placeholder="Enter full name"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('owner_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Owner Phone -->
                    <div>
                        <label for="owner_phone" class="block text-sm font-medium text-gray-700">Owner Phone *</label>
                        <input type="tel" 
                               name="owner_phone" 
                               id="owner_phone" 
                               value="{{ old('owner_phone') }}" 
                               required 
                               placeholder="+250 XXX XXX XXX"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('owner_phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Owner Email -->
                    <div>
                        <label for="owner_email" class="block text-sm font-medium text-gray-700">Owner Email</label>
                        <input type="email" 
                               name="owner_email" 
                               id="owner_email" 
                               value="{{ old('owner_email') }}" 
                               placeholder="owner@example.com"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('owner_email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Property Basic Information -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Property Information</h3>
                <p class="text-sm text-gray-500 mb-6">Basic information about the property.</p>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Property Type -->
                    <div>
                        <label for="property_type" class="block text-sm font-medium text-gray-700">Property Type *</label>
                        <select name="property_type" 
                                id="property_type" 
                                required 
                                onchange="updateSubtypes()"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Select Type</option>
                            <option value="residential" {{ old('property_type') == 'residential' ? 'selected' : '' }}>Residential</option>
                            <option value="commercial" {{ old('property_type') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                            <option value="industrial" {{ old('property_type') == 'industrial' ? 'selected' : '' }}>Industrial</option>
                            <option value="mixed" {{ old('property_type') == 'mixed' ? 'selected' : '' }}>Mixed Use</option>
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
                               value="{{ old('built_year') }}" 
                               min="1900" 
                               max="{{ date('Y') + 5 }}" 
                               placeholder="e.g., 2020"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('built_year')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Location Information -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Location Information</h3>
                <p class="text-sm text-gray-500 mb-6">Property location and GPS coordinates.</p>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Column: Address Fields -->
                    <div class="space-y-6">
                        <!-- Province -->
                        <div>
                            <label for="province" class="block text-sm font-medium text-gray-700">Province *</label>
                            <select name="province" 
                                    id="province" 
                                    required 
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Select Province</option>
                                <option value="Kigali" {{ old('province') == 'Kigali' ? 'selected' : '' }}>Kigali</option>
                                <option value="Northern" {{ old('province') == 'Northern' ? 'selected' : '' }}>Northern</option>
                                <option value="Southern" {{ old('province') == 'Southern' ? 'selected' : '' }}>Southern</option>
                                <option value="Eastern" {{ old('province') == 'Eastern' ? 'selected' : '' }}>Eastern</option>
                                <option value="Western" {{ old('province') == 'Western' ? 'selected' : '' }}>Western</option>
                            </select>
                            @error('province')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <!-- District -->
                        <div>
                            <label for="district" class="block text-sm font-medium text-gray-700">District *</label>
                            <select name="district" 
                                    id="district" 
                                    required 
                                    onchange="updateSectors()"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Select District</option>
                                <option value="Gasabo" {{ old('district') == 'Gasabo' ? 'selected' : '' }}>Gasabo</option>
                                <option value="Kicukiro" {{ old('district') == 'Kicukiro' ? 'selected' : '' }}>Kicukiro</option>
                                <option value="Nyarugenge" {{ old('district') == 'Nyarugenge' ? 'selected' : '' }}>Nyarugenge</option>
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
                                   value="{{ old('cell') }}" 
                                   placeholder="Enter cell name"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('cell')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
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
                                            onclick="centerMapOnRwanda()" 
                                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                        🇷🇼 Center on Rwanda
                                    </button>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
                                    <input type="number" 
                                           name="latitude" 
                                           id="latitude" 
                                           value="{{ old('latitude') }}" 
                                           step="any" 
                                           min="-90" 
                                           max="90" 
                                           readonly
                                           placeholder="Click map to set"
                                           class="coordinate-input mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 sm:text-sm">
                                    @error('latitude')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
                                    <input type="number" 
                                           name="longitude" 
                                           id="longitude" 
                                           value="{{ old('longitude') }}" 
                                           step="any" 
                                           min="-180" 
                                           max="180" 
                                           readonly
                                           placeholder="Click map to set"
                                           class="coordinate-input mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 sm:text-sm">
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
                            💡 Click anywhere on the map to set the property location
                        </p>
                        <p class="mt-1 text-sm text-gray-400">
                            🗺️ Using OpenStreetMap (Free alternative to Google Maps)
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
                               value="{{ old('floors_count', 1) }}" 
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
                               value="{{ old('bedrooms_count') }}" 
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
                               value="{{ old('bathrooms_count') }}" 
                               min="0" 
                               max="20" 
                               placeholder="e.g., 2"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('bathrooms_count')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Total Area -->
                    <div>
                        <label for="total_area_sqm" class="block text-sm font-medium text-gray-700">Total Area (m²)</label>
                        <input type="number" 
                               name="total_area_sqm" 
                               id="total_area_sqm" 
                               value="{{ old('total_area_sqm') }}" 
                               min="1" 
                               step="0.01" 
                               placeholder="e.g., 150.50"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('total_area_sqm')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Land Area -->
                    <div>
                        <label for="land_area_sqm" class="block text-sm font-medium text-gray-700">Land Area (m²)</label>
                        <input type="number" 
                               name="land_area_sqm" 
                               id="land_area_sqm" 
                               value="{{ old('land_area_sqm') }}" 
                               min="1" 
                               step="0.01" 
                               placeholder="e.g., 300.00"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('land_area_sqm')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Parking Spaces -->
                    <div>
                        <label for="parking_spaces" class="block text-sm font-medium text-gray-700">Parking Spaces</label>
                        <input type="number" 
                               name="parking_spaces" 
                               id="parking_spaces" 
                               value="{{ old('parking_spaces', 0) }}" 
                               min="0" 
                               max="50" 
                               placeholder="e.g., 2"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('parking_spaces')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <!-- Additional Notes -->
                <div class="mt-6">
                    <label for="additional_notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                    <textarea name="additional_notes" 
                              id="additional_notes" 
                              rows="3" 
                              placeholder="Any additional information about the property such as parking spaces, garden area, special features, etc..."
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('additional_notes') }}</textarea>
                    @error('additional_notes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3 bg-gray-50 px-4 py-3 rounded-lg">
            <a href="{{ route('admin.properties.index') }}" 
               class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" 
                    class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                Create Property
            </button>
        </div>
    </form>
</div>

<!-- Leaflet Map Script -->
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
const rwandaDistricts = {
    'Gasabo': ['Bumbogo', 'Gatsata', 'Jali', 'Gikomero', 'Gisozi', 'Jabana', 'Kinyinya', 'Ndera', 'Nduba', 'Rusororo', 'Rutunga', 'Kacyiru', 'Kimihurura', 'Kimisagara', 'Remera'],
    'Kicukiro': ['Gahanga', 'Gatenga', 'Gikondo', 'Kagarama', 'Kanombe', 'Kicukiro', 'Kigarama', 'Masaka', 'Niboye', 'Nyarugunga'],
    'Nyarugenge': ['Gitega', 'Kanyinya', 'Kigali', 'Kimisagara', 'Mageragere', 'Muhima', 'Nyakabanda', 'Nyamirambo', 'Nyarugenge', 'Rwezamenyo']
};

// Leaflet map variables
let map;
let marker;

// Initialize everything when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeMap();
    updateSubtypes();
    updateSectors();
    
    // Set existing coordinates if available
    const existingLat = '{{ old('latitude') }}';
    const existingLng = '{{ old('longitude') }}';
    
    if (existingLat && existingLng) {
        setMarker(parseFloat(existingLat), parseFloat(existingLng));
        map.setView([parseFloat(existingLat), parseFloat(existingLng)], 15);
    }
});

function initializeMap() {
    // Rwanda coordinates (center of Rwanda)
    const rwandaCenter = [-1.9403, 29.8739];
    
    // Initialize map with better options
    map = L.map('map', {
        center: rwandaCenter,
        zoom: 9,
        zoomControl: true,
        scrollWheelZoom: true,
        doubleClickZoom: true,
        boxZoom: true,
        keyboard: true,
        dragging: true,
        touchZoom: true
    });
    
    // Add high-quality tile layer with multiple sources
    const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
        subdomains: ['a', 'b', 'c']
    });
    
    // Alternative satellite layer
    const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: '© <a href="https://www.esri.com/">Esri</a>, © <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19
    });
    
    // Add default layer
    osmLayer.addTo(map);
    
    // Layer control
    const baseLayers = {
        "Street Map": osmLayer,
        "Satellite": satelliteLayer
    };
    L.control.layers(baseLayers).addTo(map);
    
    // Add click event to map
    map.on('click', function(e) {
        setMarker(e.latlng.lat, e.latlng.lng);
    });
    
    // Force map to resize properly
    setTimeout(function() {
        map.invalidateSize();
    }, 100);
}

function setMarker(lat, lng) {
    // Remove existing marker
    if (marker) {
        map.removeLayer(marker);
    }
    
    // Custom icon for better visibility
    const customIcon = L.divIcon({
        html: `<div style="background-color: #ef4444; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>`,
        className: 'custom-div-icon',
        iconSize: [20, 20],
        iconAnchor: [10, 10]
    });
    
    // Add new marker with custom icon
    marker = L.marker([lat, lng], { 
        icon: customIcon,
        draggable: true 
    }).addTo(map);
    
    // Add popup with coordinates
    marker.bindPopup(`
        <div style="text-align: center;">
            <strong>🏠 Property Location</strong><br/>
            <small>Lat: ${lat.toFixed(6)}<br/>Lng: ${lng.toFixed(6)}</small><br/>
            <em>Drag marker to adjust position</em>
        </div>
    `).openPopup();
    
    // Add drag event to marker
    marker.on('dragend', function(e) {
        const newLat = e.target.getLatLng().lat;
        const newLng = e.target.getLatLng().lng;
        updateCoordinateInputs(newLat, newLng);
        
        // Update popup content
        marker.setPopupContent(`
            <div style="text-align: center;">
                <strong>🏠 Property Location</strong><br/>
                <small>Lat: ${newLat.toFixed(6)}<br/>Lng: ${newLng.toFixed(6)}</small><br/>
                <em>Drag marker to adjust position</em>
            </div>
        `);
    });
    
    // Update coordinate inputs
    updateCoordinateInputs(lat, lng);
}

function updateCoordinateInputs(lat, lng) {
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    
    latInput.value = lat.toFixed(8);
    lngInput.value = lng.toFixed(8);
    
    // Add visual feedback with animation
    latInput.classList.add('coordinate-updated');
    lngInput.classList.add('coordinate-updated');
    
    setTimeout(() => {
        latInput.classList.remove('coordinate-updated');
        lngInput.classList.remove('coordinate-updated');
    }, 2000);
}

function getCurrentLocation() {
    if (navigator.geolocation) {
        // Show loading state
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '🔄 Getting Location...';
        button.disabled = true;
        
        navigator.geolocation.getCurrentPosition(function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            map.setView([lat, lng], 15);
            setMarker(lat, lng);
            
            // Reset button
            button.innerHTML = originalText;
            button.disabled = false;
        }, function(error) {
            console.log('Error getting location:', error);
            alert('Unable to get your current location. Please click on the map to set coordinates manually.');
            
            // Reset button
            button.innerHTML = originalText;
            button.disabled = false;
        }, {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 60000
        });
    } else {
        alert('Geolocation is not supported by this browser.');
    }
}

function centerMapOnRwanda() {
    const rwandaCenter = [-1.9403, 29.8739];
    map.setView(rwandaCenter, 9);
}

function updateSubtypes() {
    const propertyType = document.getElementById('property_type').value;
    const subtypeSelect = document.getElementById('property_subtype');
    
    subtypeSelect.innerHTML = '<option value="">Select Subtype</option>';
    
    if (propertyType && propertySubtypes[propertyType]) {
        propertySubtypes[propertyType].forEach(subtype => {
            const option = document.createElement('option');
            option.value = subtype.value;
            option.textContent = subtype.label;
            if ('{{ old('property_subtype') }}' === subtype.value) {
                option.selected = true;
            }
            subtypeSelect.appendChild(option);
        });
    }
}

function updateSectors() {
    const district = document.getElementById('district').value;
    const sectorSelect = document.getElementById('sector');
    
    sectorSelect.innerHTML = '<option value="">Select Sector</option>';
    
    if (district && rwandaDistricts[district]) {
        rwandaDistricts[district].forEach(sector => {
            const option = document.createElement('option');
            option.value = sector;
            option.textContent = sector;
            if ('{{ old('sector') }}' === sector) {
                option.selected = true;
            }
            sectorSelect.appendChild(option);
        });
    }
}

// Additional utility functions for better UX
function searchLocation() {
    // Future enhancement: Add location search functionality
    const address = prompt('Enter an address to search:');
    if (address) {
        // This would integrate with a geocoding service
        alert('Location search feature coming soon!');
    }
}

// Handle map resize when container changes
window.addEventListener('resize', function() {
    if (map) {
        setTimeout(function() {
            map.invalidateSize();
        }, 100);
    }
});
</script>

@endsection