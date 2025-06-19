@extends('layouts.headtech')

@section('title', 'Request Inspection')

@push('styles')
<style>
.auto-filled {
    background-color: #f0fdf4 !important;
    border-color: #22c55e !important;
    transition: all 0.3s ease;
}

.property-info-display {
    background-color: #e0f2fe;
    border: 1px solid #0ea5e9;
    border-radius: 0.375rem;
    padding: 0.75rem;
    margin-top: 0.5rem;
}

.search-highlight {
    background-color: #fef3c7;
    padding: 2px 4px;
    border-radius: 2px;
    font-weight: 500;
}
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <h2 class="text-xl font-semibold mb-4">Request Inspection</h2>
    
    <form method="POST" action="{{ route('headtech.inspection-requests.store') }}" class="space-y-6">
        @csrf
        <div class="mb-4">
            <label class="block mb-1">Property ID</label>
            <input type="number" name="property_id" class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1">Package ID</label>
            <input type="number" name="package_id" class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1">Purpose</label>
            <input type="text" name="purpose" class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1">Urgency</label>
            <select name="urgency" class="w-full border rounded px-3 py-2" required>
                <option value="normal">Normal</option>
                <option value="urgent">Urgent</option>
                <option value="emergency">Emergency</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block mb-1">Preferred Date</label>
            <input type="date" name="preferred_date" class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1">Preferred Time Slot</label>
            <select name="preferred_time_slot" class="w-full border rounded px-3 py-2" required>
                <option value="morning">Morning</option>
                <option value="afternoon">Afternoon</option>
                <option value="evening">Evening</option>
                <option value="flexible">Flexible</option>
            </select>
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Create Request</button>
    </form>
</div>

@push('scripts')
<script>
// ... existing JS from admin/inspection-requests/create.blade.php ...
</script>
@endpush

@endsection 