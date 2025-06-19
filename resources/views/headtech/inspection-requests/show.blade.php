@extends('layouts.headtech')

@section('title', 'Inspection Request - ' . $inspectionRequest->request_number)

@section('page-header')
{{-- Copy the admin page-header section, updating route/layout references to headtech --}}
@endsection

@section('content')
<div class="py-8 max-w-xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Inspection Request Details</h1>
    <div class="bg-white p-6 rounded shadow mb-6">
        <p><strong>Request #:</strong> {{ $inspectionRequest->request_number }}</p>
        <p><strong>Property:</strong> {{ $inspectionRequest->property->address ?? '-' }}</p>
        <p><strong>Package:</strong> {{ $inspectionRequest->package->display_name ?? '-' }}</p>
        <p><strong>Status:</strong> {{ ucfirst($inspectionRequest->status) }}</p>
        <p><strong>Inspector:</strong> {{ $inspectionRequest->assignedInspector ? $inspectionRequest->assignedInspector->user->full_name : '-' }}</p>
        <p><strong>Purpose:</strong> {{ $inspectionRequest->purpose }}</p>
        <p><strong>Urgency:</strong> {{ ucfirst($inspectionRequest->urgency) }}</p>
        <p><strong>Preferred Date:</strong> {{ $inspectionRequest->preferred_date }}</p>
        <p><strong>Preferred Time Slot:</strong> {{ ucfirst($inspectionRequest->preferred_time_slot) }}</p>
    </div>
    @if($inspectionRequest->status === 'pending')
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-lg font-semibold mb-2">Assign Inspector</h2>
        <form method="POST" action="{{ route('headtech.inspection-requests.assign', $inspectionRequest->id) }}">
            @csrf
            <div class="mb-4">
                <label class="block mb-1">Inspector ID</label>
                <input type="number" name="inspector_id" class="w-full border rounded px-3 py-2" required>
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Assign Inspector</button>
        </form>
    </div>
    @endif
    <a href="{{ route('headtech.inspection-requests.index') }}" class="mt-4 inline-block text-blue-600 hover:underline">Back to List</a>
</div>
@endsection 