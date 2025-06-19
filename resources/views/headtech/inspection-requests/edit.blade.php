@extends('layouts.headtech')

@section('title', 'Edit Inspection Request')

@section('content')
<div class="py-8 max-w-xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Edit Inspection Request</h1>
    <form method="POST" action="{{ route('headtech.inspection-requests.update', $inspectionRequest) }}">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block mb-1">Purpose</label>
            <input type="text" name="purpose" class="w-full border rounded px-3 py-2" value="{{ old('purpose', $inspectionRequest->purpose) }}" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1">Urgency</label>
            <select name="urgency" class="w-full border rounded px-3 py-2" required>
                <option value="normal" @if($inspectionRequest->urgency=='normal') selected @endif>Normal</option>
                <option value="urgent" @if($inspectionRequest->urgency=='urgent') selected @endif>Urgent</option>
                <option value="emergency" @if($inspectionRequest->urgency=='emergency') selected @endif>Emergency</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block mb-1">Preferred Date</label>
            <input type="date" name="preferred_date" class="w-full border rounded px-3 py-2" value="{{ old('preferred_date', $inspectionRequest->preferred_date) }}" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1">Preferred Time Slot</label>
            <select name="preferred_time_slot" class="w-full border rounded px-3 py-2" required>
                <option value="morning" @if($inspectionRequest->preferred_time_slot=='morning') selected @endif>Morning</option>
                <option value="afternoon" @if($inspectionRequest->preferred_time_slot=='afternoon') selected @endif>Afternoon</option>
                <option value="evening" @if($inspectionRequest->preferred_time_slot=='evening') selected @endif>Evening</option>
                <option value="flexible" @if($inspectionRequest->preferred_time_slot=='flexible') selected @endif>Flexible</option>
            </select>
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Update Request</button>
    </form>
</div>
@endsection 