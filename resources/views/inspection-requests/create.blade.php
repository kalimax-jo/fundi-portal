@extends('layouts.app')

@section('title', 'Request Inspection')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <h2 class="text-xl font-semibold mb-4">Request Inspection</h2>
    <form method="POST" action="{{ route('inspection-requests.store') }}" class="space-y-6">
        @csrf
        <div>
            <label for="property_id" class="block text-sm font-medium text-gray-700">Property</label>
            <select name="property_id" id="property_id" required class="mt-1 block w-full border-gray-300 rounded-md">
                <option value="">Choose property...</option>
                @foreach($properties as $property)
                    <option value="{{ $property->id }}" {{ old('property_id') == $property->id ? 'selected' : '' }}>
                        {{ $property->property_code }} - {{ $property->address }}
                    </option>
                @endforeach
            </select>
            @error('property_id')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="package_id" class="block text-sm font-medium text-gray-700">Inspection Package</label>
            <select name="package_id" id="package_id" required class="mt-1 block w-full border-gray-300 rounded-md">
                <option value="">Choose package...</option>
                @foreach($packages as $package)
                    <option value="{{ $package->id }}" {{ old('package_id') == $package->id ? 'selected' : '' }}>
                        {{ $package->display_name }} - {{ number_format($package->price) }} RWF
                    </option>
                @endforeach
            </select>
            @error('package_id')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="purpose" class="block text-sm font-medium text-gray-700">Purpose</label>
            <select name="purpose" id="purpose" required class="mt-1 block w-full border-gray-300 rounded-md">
                <option value="">Select purpose</option>
                <option value="rental" {{ old('purpose') == 'rental' ? 'selected' : '' }}>Rental</option>
                <option value="sale" {{ old('purpose') == 'sale' ? 'selected' : '' }}>Sale</option>
                <option value="purchase" {{ old('purpose') == 'purchase' ? 'selected' : '' }}>Purchase</option>
                <option value="loan_collateral" {{ old('purpose') == 'loan_collateral' ? 'selected' : '' }}>Loan Collateral</option>
                <option value="insurance" {{ old('purpose') == 'insurance' ? 'selected' : '' }}>Insurance</option>
                <option value="maintenance" {{ old('purpose') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                <option value="other" {{ old('purpose') == 'other' ? 'selected' : '' }}>Other</option>
            </select>
            @error('purpose')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="urgency" class="block text-sm font-medium text-gray-700">Urgency</label>
            <select name="urgency" id="urgency" required class="mt-1 block w-full border-gray-300 rounded-md">
                <option value="normal" {{ old('urgency') == 'normal' ? 'selected' : '' }}>Normal</option>
                <option value="urgent" {{ old('urgency') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                <option value="emergency" {{ old('urgency') == 'emergency' ? 'selected' : '' }}>Emergency</option>
            </select>
            @error('urgency')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="preferred_date" class="block text-sm font-medium text-gray-700">Preferred Date</label>
                <input type="date" name="preferred_date" id="preferred_date" value="{{ old('preferred_date') }}" class="mt-1 block w-full border-gray-300 rounded-md" />
                @error('preferred_date')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="preferred_time_slot" class="block text-sm font-medium text-gray-700">Time Slot</label>
                <select name="preferred_time_slot" id="preferred_time_slot" required class="mt-1 block w-full border-gray-300 rounded-md">
                    <option value="morning" {{ old('preferred_time_slot') == 'morning' ? 'selected' : '' }}>Morning</option>
                    <option value="afternoon" {{ old('preferred_time_slot') == 'afternoon' ? 'selected' : '' }}>Afternoon</option>
                    <option value="evening" {{ old('preferred_time_slot') == 'evening' ? 'selected' : '' }}>Evening</option>
                    <option value="flexible" {{ old('preferred_time_slot', 'flexible') == 'flexible' ? 'selected' : '' }}>Flexible</option>
                </select>
                @error('preferred_time_slot')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label for="special_instructions" class="block text-sm font-medium text-gray-700">Special Instructions</label>
            <textarea name="special_instructions" id="special_instructions" rows="4" class="mt-1 block w-full border-gray-300 rounded-md">{{ old('special_instructions') }}</textarea>
            @error('special_instructions')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-white shadow hover:bg-indigo-500">
                Submit Request
            </button>
        </div>
    </form>
</div>
@endsection
