@extends('layouts.headtech')

@section('title', 'Inspector Details')

@section('content')
<div class="py-8 max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Inspector Details</h1>
    <div class="bg-white rounded shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <div class="text-xs text-gray-500">Name</div>
                <div class="font-semibold text-gray-800">{{ $inspector->user->full_name ?? '-' }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500">Email</div>
                <div class="font-semibold text-gray-800">{{ $inspector->user->email ?? '-' }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500">Phone</div>
                <div class="font-semibold text-gray-800">{{ $inspector->user->phone ?? '-' }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500">Inspector Code</div>
                <div class="font-semibold text-gray-800">{{ $inspector->inspector_code }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500">Certification Level</div>
                <div class="font-semibold text-gray-800">{{ ucfirst($inspector->certification_level) }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500">Specializations</div>
                <div class="font-semibold text-gray-800">{{ is_array($inspector->specializations) ? implode(', ', $inspector->specializations) : ($inspector->specializations ?? '-') }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500">Experience Years</div>
                <div class="font-semibold text-gray-800">{{ $inspector->experience_years ?? '-' }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500">Certification Expiry</div>
                <div class="font-semibold text-gray-800">{{ $inspector->certification_expiry ? $inspector->certification_expiry->format('Y-m-d') : '-' }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500">Equipment Assigned</div>
                <div class="font-semibold text-gray-800">{{ is_array($inspector->equipment_assigned) ? implode(', ', $inspector->equipment_assigned) : ($inspector->equipment_assigned ?? '-') }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500">Availability Status</div>
                <div class="font-semibold text-gray-800">{{ ucfirst($inspector->availability_status) }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500">Rating</div>
                <div class="font-semibold text-gray-800">{{ $inspector->rating ?? '-' }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500">Total Inspections</div>
                <div class="font-semibold text-gray-800">{{ $inspector->total_inspections ?? '-' }}</div>
            </div>
        </div>
    </div>
    <a href="{{ route('headtech.inspectors.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm">Back to Inspectors</a>
</div>
@endsection 