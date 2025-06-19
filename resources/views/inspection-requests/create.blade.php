@extends('layouts.app')

@section('title', 'New Inspection Request')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
      crossorigin=""/>
<style>
    #map {
        height: 400px;
        width: 100%;
        border-radius: 0.5rem;
        border: 2px solid #d1d5db;
    }
    .map-container {
        position: relative;
    }
    .coordinates-display {
        background: #f3f4f6;
        padding: 0.5rem;
        border-radius: 0.25rem;
        font-family: monospace;
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }
    .form-section {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .form-section h2 {
        color: #1f2937;
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }
    .form-section p {
        color: #6b7280;
        font-size: 0.875rem;
        margin-bottom: 1.5rem;
    }
    .coordinate-input {
        transition: background-color 0.3s ease;
    }
    .coordinate-updated {
        background-color: #f0fdf4 !important;
        border-color: #22c55e !important;
    }
    .location-buttons {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }
    .location-btn {
        display: inline-flex;
        align-items: center;
        padding: 0.375rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        background: white;
        color: #374151;
        font-size: 0.75rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }
    .location-btn:hover {
        background: #f9fafb;
        border-color: #9ca3af;
    }
    .location-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">New Inspection Request</h1>
    
    <form action="{{ route('inspection-requests.store') }}" method="POST" class="space-y-8">
        @csrf
        
        <!-- Step 1: Package Selection -->
        <div class="form-section">
            <h2>1. Select Inspection Package</h2>
            <p>Choose the inspection package that best fits your needs</p>
            <div>
                <label for="package_id" class="block text-sm font-medium text-gray-700">Inspection Package *</label>
                <select name="package_id" id="package_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Select a package</option>
                    @foreach($packages as $package)
                        <option value="{{ $package->id }}" {{ old('package_id') == $package->id ? 'selected' : '' }}>
                            {{ $package->display_name }} ({{ number_format($package->price, 0) }} RWF)
                        </option>
                    @endforeach
                </select>
                @error('package_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Step 2: Property Location -->
        <div class="form-section">
            <h2>2. Property Location</h2>
            <p>Provide the location details of the property to be inspected</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Address -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Property Address *</label>
                    <input type="text" name="address" id="address" value="{{ old('address') }}" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                           placeholder="Enter full property address">
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- District -->
                <div>
                    <label for="district" class="block text-sm font-medium text-gray-700">District *</label>
                    <select name="district" id="district" required onchange="updateSectors()" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Select district</option>
                        @foreach($districts as $district)
                            <option value="{{ $district }}" {{ old('district') == $district ? 'selected' : '' }}>{{ $district }}</option>
                        @endforeach
                    </select>
                    @error('district')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sector -->
                <div>
                    <label for="sector" class="block text-sm font-medium text-gray-700">Sector</label>
                    <select name="sector" id="sector" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Select Sector</option>
                    </select>
                    @error('sector')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Cell -->
                <div>
                    <label for="cell" class="block text-sm font-medium text-gray-700">Cell</label>
                    <input type="text" name="cell" id="cell" value="{{ old('cell') }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                           placeholder="Enter cell name">
                    @error('cell')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- GPS Coordinates -->
            <div class="mt-6">
                <div class="flex items-center justify-between mb-4">
                    <label class="block text-sm font-medium text-gray-700">GPS Coordinates</label>
                    <div class="location-buttons">
                        <button type="button" 
                                onclick="getCurrentLocation()" 
                                class="location-btn">
                            📍 Use My Location
                        </button>
                        <button type="button" 
                                onclick="centerMapOnRwanda()" 
                                class="location-btn">
                            🇷🇼 Center on Rwanda
                        </button>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
                        <input type="number" name="latitude" id="latitude" value="{{ old('latitude') }}" 
                               step="any" min="-90" max="90" readonly
                               placeholder="Click map to set"
                               class="coordinate-input mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-50 sm:text-sm">
                        @error('latitude')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
                        <input type="number" name="longitude" id="longitude" value="{{ old('longitude') }}" 
                               step="any" min="-180" max="180" readonly
                               placeholder="Click map to set"
                               class="coordinate-input mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-50 sm:text-sm">
                        @error('longitude')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Map Integration -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Property Location on Map</label>
                <div class="map-container">
                    <div id="map"></div>
                </div>
                <p class="mt-2 text-sm text-gray-500">💡 Click anywhere on the map to set the property location</p>
                <p class="mt-1 text-sm text-gray-400">🗺️ Using OpenStreetMap (Free alternative to Google Maps)</p>
            </div>
        </div>

        <!-- Step 3: Property Details -->
        <div class="form-section">
            <h2>3. Property Details</h2>
            <p>Provide detailed information about the property</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Property Type -->
                <div>
                    <label for="property_type" class="block text-sm font-medium text-gray-700">Property Type *</label>
                    <select name="property_type" id="property_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Select type</option>
                        @foreach($propertyTypes as $key => $label)
                            <option value="{{ $key }}" {{ old('property_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('property_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Property Size -->
                <div>
                    <label for="property_size" class="block text-sm font-medium text-gray-700">Property Size (sqm)</label>
                    <input type="number" name="property_size" id="property_size" value="{{ old('property_size') }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                           placeholder="Enter property size in square meters">
                    @error('property_size')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Number of Bedrooms -->
                <div>
                    <label for="bedrooms" class="block text-sm font-medium text-gray-700">Number of Bedrooms</label>
                    <input type="number" name="bedrooms" id="bedrooms" value="{{ old('bedrooms') }}" min="0"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                           placeholder="Number of bedrooms">
                    @error('bedrooms')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Number of Bathrooms -->
                <div>
                    <label for="bathrooms" class="block text-sm font-medium text-gray-700">Number of Bathrooms</label>
                    <input type="number" name="bathrooms" id="bathrooms" value="{{ old('bathrooms') }}" min="0"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                           placeholder="Number of bathrooms">
                    @error('bathrooms')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Construction Year -->
                <div>
                    <label for="construction_year" class="block text-sm font-medium text-gray-700">Construction Year</label>
                    <input type="number" name="construction_year" id="construction_year" value="{{ old('construction_year') }}" 
                           min="1900" max="{{ date('Y') + 1 }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                           placeholder="Year of construction">
                    @error('construction_year')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Property Condition -->
                <div>
                    <label for="property_condition" class="block text-sm font-medium text-gray-700">Property Condition</label>
                    <select name="property_condition" id="property_condition" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Select condition</option>
                        <option value="excellent" {{ old('property_condition') == 'excellent' ? 'selected' : '' }}>Excellent</option>
                        <option value="good" {{ old('property_condition') == 'good' ? 'selected' : '' }}>Good</option>
                        <option value="fair" {{ old('property_condition') == 'fair' ? 'selected' : '' }}>Fair</option>
                        <option value="poor" {{ old('property_condition') == 'poor' ? 'selected' : '' }}>Poor</option>
                        <option value="under_construction" {{ old('property_condition') == 'under_construction' ? 'selected' : '' }}>Under Construction</option>
                    </select>
                    @error('property_condition')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Property Description -->
            <div class="mt-6">
                <label for="property_description" class="block text-sm font-medium text-gray-700">Property Description</label>
                <textarea name="property_description" id="property_description" rows="3" 
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                          placeholder="Describe the property, its features, and any notable characteristics">{{ old('property_description') }}</textarea>
                @error('property_description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Step 4: Inspection Details -->
        <div class="form-section">
            <h2>4. Inspection Details</h2>
            <p>Specify the purpose and scheduling preferences for the inspection</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Purpose -->
                <div>
                    <label for="purpose" class="block text-sm font-medium text-gray-700">Purpose *</label>
                    <select name="purpose" id="purpose" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Select purpose</option>
                        <option value="rental" {{ old('purpose') == 'rental' ? 'selected' : '' }}>Rental</option>
                        <option value="sale" {{ old('purpose') == 'sale' ? 'selected' : '' }}>Sale</option>
                        <option value="purchase" {{ old('purpose') == 'purchase' ? 'selected' : '' }}>Purchase</option>
                        <option value="loan_collateral" {{ old('purpose') == 'loan_collateral' ? 'selected' : '' }}>Loan Collateral</option>
                        <option value="insurance" {{ old('purpose') == 'insurance' ? 'selected' : '' }}>Insurance</option>
                        <option value="maintenance" {{ old('purpose') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="other" {{ old('purpose') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('purpose')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Urgency -->
                <div>
                    <label for="urgency" class="block text-sm font-medium text-gray-700">Urgency *</label>
                    <select name="urgency" id="urgency" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Select urgency</option>
                        <option value="normal" {{ old('urgency') == 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="urgent" {{ old('urgency') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        <option value="emergency" {{ old('urgency') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                    </select>
                    @error('urgency')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Preferred Date -->
                <div>
                    <label for="preferred_date" class="block text-sm font-medium text-gray-700">Preferred Date</label>
                    <input type="date" name="preferred_date" id="preferred_date" value="{{ old('preferred_date') }}" 
                           min="{{ date('Y-m-d') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('preferred_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Preferred Time Slot -->
                <div>
                    <label for="preferred_time_slot" class="block text-sm font-medium text-gray-700">Preferred Time Slot *</label>
                    <select name="preferred_time_slot" id="preferred_time_slot" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Select time slot</option>
                        <option value="morning" {{ old('preferred_time_slot') == 'morning' ? 'selected' : '' }}>Morning (8:00 AM - 12:00 PM)</option>
                        <option value="afternoon" {{ old('preferred_time_slot') == 'afternoon' ? 'selected' : '' }}>Afternoon (1:00 PM - 5:00 PM)</option>
                        <option value="evening" {{ old('preferred_time_slot') == 'evening' ? 'selected' : '' }}>Evening (5:00 PM - 8:00 PM)</option>
                        <option value="flexible" {{ old('preferred_time_slot') == 'flexible' ? 'selected' : '' }}>Flexible</option>
                    </select>
                    @error('preferred_time_slot')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Special Instructions -->
            <div class="mt-6">
                <label for="special_instructions" class="block text-sm font-medium text-gray-700">Special Instructions</label>
                <textarea name="special_instructions" id="special_instructions" rows="3" 
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                          placeholder="Any special requirements or instructions for the inspector">{{ old('special_instructions') }}</textarea>
                @error('special_instructions')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Submit Inspection Request
            </button>
        </div>
    </form>
</div>

<!-- Leaflet Map Script -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
        crossorigin=""></script>
<script>
// Rwanda districts and sectors data
const rwandaDistricts = {
    'Gasabo': ['Bumbogo', 'Gatsata', 'Jali', 'Gikomero', 'Gisozi', 'Jabana', 'Kinyinya', 'Ndera', 'Nduba', 'Rusororo', 'Rutunga', 'Kacyiru', 'Kimihurura', 'Kimisagara', 'Remera'],
    'Kicukiro': ['Gahanga', 'Gatenga', 'Gikondo', 'Kagarama', 'Kanombe', 'Kicukiro', 'Kigarama', 'Masaka', 'Niboye', 'Nyarugunga'],
    'Nyarugenge': ['Gitega', 'Kanyinya', 'Kigali', 'Kimisagara', 'Mageragere', 'Muhima', 'Nyakabanda', 'Nyamirambo', 'Nyarugenge', 'Rwezamenyo'],
    'Bugesera': ['Gashora', 'Juru', 'Kamabuye', 'Ntarama', 'Mareba', 'Mayange', 'Musenyi', 'Mwogo', 'Ngeruka', 'Nyarugenge', 'Rilima', 'Ruhuha', 'Rweru', 'Shyara'],
    'Gatsibo': ['Gatsibo', 'Gasange', 'Gitoki', 'Kabarore', 'Kageyo', 'Kiramuruzi', 'Kiziguro', 'Muhura', 'Murambi', 'Ngarama', 'Nyagihanga', 'Remera', 'Rugarama', 'Rwimbogo'],
    'Kayonza': ['Gahini', 'Kabare', 'Kabarondo', 'Mukarange', 'Murama', 'Murundi', 'Mwiri', 'Ndego', 'Nyamirama', 'Rukara', 'Ruramira', 'Rwinkwavu'],
    'Kirehe': ['Gahara', 'Gatore', 'Kigarama', 'Kigina', 'Kirehe', 'Mahama', 'Mpanga', 'Musaza', 'Mushikiri', 'Nasho', 'Nyamugari', 'Nyarubuye'],
    'Ngoma': ['Gashanda', 'Jarama', 'Karembo', 'Kazo', 'Kibungo', 'Mugesera', 'Murama', 'Mutenderi', 'Remera', 'Rukira', 'Rukumberi', 'Rurenge', 'Sake', 'Zaza'],
    'Rwamagana': ['Fumbwe', 'Gahengeri', 'Gishari', 'Karenge', 'Kigabiro', 'Muhazi', 'Munyaga', 'Munyiginya', 'Musaza', 'Mushonyi', 'Mushube', 'Muyumbu', 'Mwulire', 'Nyakaliro', 'Nzige', 'Rubona'],
    'Burera': ['Bungwe', 'Butaro', 'Cyanika', 'Cyeru', 'Gahunga', 'Gatebe', 'Gitovu', 'Kagogo', 'Kinoni', 'Kinyababa', 'Kivuye', 'Nemba', 'Rugarama', 'Rugengabari', 'Ruhunde', 'Rusarabuye', 'Rwerere'],
    'Gakenke': ['Busengo', 'Coko', 'Cyabingo', 'Gakenke', 'Gashenyi', 'Janja', 'Kamubuga', 'Karambo', 'Kivuruga', 'Mataba', 'Minazi', 'Mugunga', 'Muhondo', 'Muyongwe', 'Muzo', 'Nemba', 'Ruli', 'Rusasa', 'Rushashi'],
    'Gicumbi': ['Bukure', 'Bwisige', 'Byumba', 'Cyumba', 'Giti', 'Kageyo', 'Kaniga', 'Manyagiro', 'Miyove', 'Mukarange', 'Muko', 'Mutete', 'Nyamiyaga', 'Nyankenke', 'Rubaya', 'Rukomo', 'Rushaki', 'Rutare', 'Ruvune', 'Rwamiko', 'Shangasha'],
    'Musanze': ['Busogo', 'Cyuve', 'Gacaca', 'Gashaki', 'Gataraga', 'Kimonyi', 'Kinigi', 'Muhoza', 'Muko', 'Musanze', 'Nkotsi', 'Nyange', 'Remera', 'Rwaza', 'Shingiro'],
    'Rulindo': ['Base', 'Burega', 'Bushoki', 'Buyoga', 'Cyinzuzi', 'Cyungo', 'Kinihira', 'Kisaro', 'Masoro', 'Mbogo', 'Murambi', 'Ngoma', 'Ntarabana', 'Rukozo', 'Rusiga', 'Shyorongi', 'Tumba'],
    'Gisagara': ['Gikundamvura', 'Gishubi', 'Kansi', 'Kibirizi', 'Kigembe', 'Mamba', 'Mukindo', 'Musha', 'Ndora', 'Nyanza', 'Save'],
    'Huye': ['Gishamvu', 'Huye', 'Karama', 'Kigoma', 'Kinazi', 'Mukura', 'Ngoma', 'Ruhashya', 'Rusatira', 'Rwaniro', 'Simbi', 'Tumba'],
    'Kamonyi': ['Gacurabwenge', 'Karama', 'Kayenzi', 'Kayumbu', 'Mugina', 'Musambira', 'Ngamba', 'Nyamiyaga', 'Nyarubaka', 'Rugalika', 'Rukoma', 'Runda'],
    'Muhanga': ['Cyeza', 'Kabacuzi', 'Kibangu', 'Kiyumba', 'Muhanga', 'Mushishiro', 'Nyabinoni', 'Nyamabuye', 'Nyarusange', 'Rongi', 'Rugendabari', 'Shyogwe'],
    'Nyamagabe': ['Buruhukiro', 'Cyanika', 'Gasaka', 'Gatare', 'Kaduha', 'Kamegeli', 'Kibirizi', 'Kibumbwe', 'Kitabi', 'Mbazi', 'Mugano', 'Musange', 'Musebeya', 'Mushubi', 'Nkomane', 'Tare', 'Uwinkingi'],
    'Nyanza': ['Busasamana', 'Busoro', 'Cyabakamyi', 'Kibilizi', 'Kigoma', 'Mukingo', 'Muyira', 'Ntyazo', 'Nyagisozi', 'Rwabicuma'],
    'Nyaruguru': ['Buruhukiro', 'Cyanika', 'Gasaka', 'Gatare', 'Kaduha', 'Kamegeli', 'Kibirizi', 'Kibumbwe', 'Kitabi', 'Mbazi', 'Mugano', 'Musange', 'Musebeya', 'Mushubi', 'Nkomane', 'Tare', 'Uwinkingi'],
    'Ruhango': ['Bweramvura', 'Byimana', 'Kabagali', 'Kinazi', 'Kinihira', 'Mbuye', 'Mpanda', 'Muhororo', 'Mushishiro', 'Ntongwe', 'Ruhango', 'Rusongati'],
    'Karongi': ['Boneza', 'Gihombo', 'Kagabiro', 'Kanjongo', 'Karongi', 'Kayove', 'Kibuye', 'Kivumu', 'Manihira', 'Murambi', 'Musasa', 'Mubuga', 'Mutuntu', 'Rubengera', 'Rugabano', 'Ruganda', 'Rwankuba', 'Twumba'],
    'Ngororero': ['Bwira', 'Gatumba', 'Hindiro', 'Kabaya', 'Kageyo', 'Kavumu', 'Matyazo', 'Muhanda', 'Muhororo', 'Ndaro', 'Ngororero', 'Nyange', 'Sovu'],
    'Nyabihu': ['Bigogwe', 'Jenda', 'Jomba', 'Kabatwa', 'Karago', 'Kintobo', 'Mukamira', 'Muringa', 'Rambura', 'Rugera', 'Rurembo', 'Shyira'],
    'Nyamasheke': ['Bushenge', 'Cyato', 'Gihombo', 'Kagano', 'Kanjongo', 'Karambi', 'Karengera', 'Kirimbi', 'Macuba', 'Mahembe', 'Nyabitekeri', 'Rangiro', 'Ruharambuga', 'Shangi', 'Yaramba'],
    'Rubavu': ['Bugeshi', 'Busasamana', 'Cyanzarwe', 'Gisenyi', 'Kanama', 'Kanzenze', 'Mudende', 'Nyakiriba', 'Nyamyumba', 'Nyundo', 'Rubavu', 'Rugerero'],
    'Rusizi': ['Bugarama', 'Butare', 'Bweyeye', 'Gashonga', 'Giheke', 'Gihundwe', 'Gikundamvura', 'Gitambi', 'Kamembe', 'Muganza', 'Mururu', 'Nkanka', 'Nkombo', 'Nkungu', 'Nyakabuye', 'Nyakarenzo', 'Nzahaha', 'Rwimbogo'],
    'Rutsiro': ['Boneza', 'Gihango', 'Kigeyo', 'Kivumu', 'Manihira', 'Mukura', 'Murunda', 'Musasa', 'Mushonyi', 'Mushubati', 'Nyabirasi', 'Ruhango', 'Rusebeya', 'Rwankuba']
};

// Leaflet map variables
let map;
let marker;

// Initialize everything when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeMap();
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
    
    // Add high-quality tile layer
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