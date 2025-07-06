@extends('layouts.business-partner')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Edit Property</h1>
                        <p class="mt-1 text-sm text-gray-600">{{ $property->property_code }} - {{ $property->address }}</p>
                    </div>
                    <a href="{{ route('business-partner.properties.show', $property) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Property
                    </a>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <form method="POST" action="{{ route('business-partner.properties.update', $property) }}">
                @csrf
                @method('PUT')
                
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Property Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Property Code -->
                        <div>
                            <label for="property_code" class="block text-sm font-medium text-gray-700">Property Code</label>
                            <input type="text" name="property_code" id="property_code" value="{{ old('property_code', $property->property_code) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('property_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Property Type -->
                        <div>
                            <label for="property_type" class="block text-sm font-medium text-gray-700">Property Type</label>
                            <select name="property_type" id="property_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Select type</option>
                                <option value="residential" {{ old('property_type', $property->property_type) === 'residential' ? 'selected' : '' }}>Residential</option>
                                <option value="commercial" {{ old('property_type', $property->property_type) === 'commercial' ? 'selected' : '' }}>Commercial</option>
                                <option value="industrial" {{ old('property_type', $property->property_type) === 'industrial' ? 'selected' : '' }}>Industrial</option>
                                <option value="mixed" {{ old('property_type', $property->property_type) === 'mixed' ? 'selected' : '' }}>Mixed Use</option>
                            </select>
                            @error('property_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="active" {{ old('status', $property->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $property->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                            <input type="text" name="address" id="address" value="{{ old('address', $property->address) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- District -->
                        <div>
                            <label for="district" class="block text-sm font-medium text-gray-700">District</label>
                            <input type="text" name="district" id="district" value="{{ old('district', $property->district) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('district')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Sector -->
                        <div>
                            <label for="sector" class="block text-sm font-medium text-gray-700">Sector</label>
                            <input type="text" name="sector" id="sector" value="{{ old('sector', $property->sector) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('sector')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Cell -->
                        <div>
                            <label for="cell" class="block text-sm font-medium text-gray-700">Cell</label>
                            <input type="text" name="cell" id="cell" value="{{ old('cell', $property->cell) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('cell')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Total Area -->
                        <div>
                            <label for="total_area_sqm" class="block text-sm font-medium text-gray-700">Total Area (sqm)</label>
                            <input type="number" name="total_area_sqm" id="total_area_sqm" value="{{ old('total_area_sqm', $property->total_area_sqm) }}" min="0" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('total_area_sqm')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Bedrooms -->
                        <div>
                            <label for="bedrooms_count" class="block text-sm font-medium text-gray-700">Bedrooms</label>
                            <input type="number" name="bedrooms_count" id="bedrooms_count" value="{{ old('bedrooms_count', $property->bedrooms_count) }}" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('bedrooms_count')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Bathrooms -->
                        <div>
                            <label for="bathrooms_count" class="block text-sm font-medium text-gray-700">Bathrooms</label>
                            <input type="number" name="bathrooms_count" id="bathrooms_count" value="{{ old('bathrooms_count', $property->bathrooms_count) }}" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('bathrooms_count')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Built Year -->
                        <div>
                            <label for="built_year" class="block text-sm font-medium text-gray-700">Built Year</label>
                            <input type="number" name="built_year" id="built_year" value="{{ old('built_year', $property->built_year) }}" min="1900" max="{{ date('Y') + 1 }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('built_year')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <h2 class="text-lg font-semibold text-gray-900 mb-6 mt-8">Owner Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Owner Name -->
                        <div>
                            <label for="owner_name" class="block text-sm font-medium text-gray-700">Owner Name</label>
                            <input type="text" name="owner_name" id="owner_name" value="{{ old('owner_name', $property->owner_name) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('owner_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Owner Phone -->
                        <div>
                            <label for="owner_phone" class="block text-sm font-medium text-gray-700">Owner Phone</label>
                            <input type="text" name="owner_phone" id="owner_phone" value="{{ old('owner_phone', $property->owner_phone) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('owner_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Owner Email -->
                        <div>
                            <label for="owner_email" class="block text-sm font-medium text-gray-700">Owner Email</label>
                            <input type="email" name="owner_email" id="owner_email" value="{{ old('owner_email', $property->owner_email) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('owner_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Client Name -->
                        <div>
                            <label for="client_name" class="block text-sm font-medium text-gray-700">Client Name</label>
                            <input type="text" name="client_name" id="client_name" value="{{ old('client_name', $property->client_name) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('client_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Client National ID -->
                        <div>
                            <label for="client_national_id" class="block text-sm font-medium text-gray-700">Client National ID</label>
                            <input type="text" name="client_national_id" id="client_national_id" value="{{ old('client_national_id', $property->client_national_id) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('client_national_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Additional Notes -->
                    <div class="mt-6">
                        <label for="additional_notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                        <textarea name="additional_notes" id="additional_notes" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('additional_notes', $property->additional_notes) }}</textarea>
                        @error('additional_notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <h2 class="text-lg font-semibold text-gray-900 mb-6 mt-8">Property Location (Map)</h2>
                    <div class="mb-6">
                        <div class="location-buttons mb-2 flex gap-2">
                            <button type="button" onclick="getCurrentLocation()" class="location-btn bg-indigo-600 text-white font-semibold px-3 py-1 rounded">📍 Use My Location</button>
                            <button type="button" onclick="centerMapOnRwanda()" class="location-btn bg-gray-200 text-gray-800 font-semibold px-3 py-1 rounded">🇷🇼 Center on Rwanda</button>
                        </div>
                        <div id="map" style="height: 300px; width: 100%; border-radius: 0.5rem; border: 2px solid #d1d5db;"></div>
                        <div class="grid grid-cols-2 gap-4 mt-2">
                            <div>
                                <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
                                <input type="number" name="latitude" id="latitude" value="{{ old('latitude', $property->latitude) }}" step="any" min="-90" max="90" readonly class="coordinate-input mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-50 sm:text-sm">
                                @error('latitude')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
                                <input type="number" name="longitude" id="longitude" value="{{ old('longitude', $property->longitude) }}" step="any" min="-180" max="180" readonly class="coordinate-input mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-50 sm:text-sm">
                                @error('longitude')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-8 flex justify-end space-x-3">
                        <a href="{{ route('business-partner.properties.show', $property) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Update Property
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
@endpush
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
let map = L.map('map').setView([-1.95, 30.06], 8);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap'
}).addTo(map);
let marker;
function setMarker(lat, lng) {
    if (marker) map.removeLayer(marker);
    marker = L.marker([lat, lng]).addTo(map);
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;
}
map.on('click', function(e) {
    setMarker(e.latlng.lat, e.latlng.lng);
});
function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            setMarker(pos.coords.latitude, pos.coords.longitude);
            map.setView([pos.coords.latitude, pos.coords.longitude], 15);
        });
    }
}
function centerMapOnRwanda() {
    map.setView([-1.95, 30.06], 8);
}
// If property has coordinates, set marker
@if(old('latitude', $property->latitude) && old('longitude', $property->longitude))
    setMarker({{ old('latitude', $property->latitude) }}, {{ old('longitude', $property->longitude) }});
@endif
</script>
@endpush 