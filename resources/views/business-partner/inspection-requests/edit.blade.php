@extends('layouts.business-partner')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Edit Inspection Request</h1>
                <form method="POST" action="{{ route('business-partner.inspection-requests.update', $inspectionRequest) }}">
                    @csrf
                    @method('PUT')
                    @if(session('error'))
                        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
                    @endif
                    @if(session('success'))
                        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                            <ul class="list-disc pl-5">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Property</label>
                        <div class="mt-1 text-gray-900 font-semibold">{{ $inspectionRequest->property->property_code }} - {{ $inspectionRequest->property->address }}</div>
                    </div>
                    <div class="mb-4">
                        <label for="client_id" class="block text-sm font-medium text-gray-700">Client (Optional)</label>
                        <select name="client_id" id="client_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">No specific client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ $inspectionRequest->client_id == $client->id ? 'selected' : '' }}>{{ $client->full_name }} ({{ $client->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="preferred_date" class="block text-sm font-medium text-gray-700">Preferred Inspection Date</label>
                        <input type="date" name="preferred_date" id="preferred_date" min="{{ date('Y-m-d') }}" value="{{ $inspectionRequest->preferred_date ? $inspectionRequest->preferred_date->format('Y-m-d') : '' }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div class="mb-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                        <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Any special requirements or notes for the inspection...">{{ old('notes', $inspectionRequest->special_instructions) }}</textarea>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <a href="{{ route('business-partner.inspection-requests.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 mr-2">Cancel</a>
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 