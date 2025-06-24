@extends('layouts.app')

@section('title', 'Inspection Request Details')

@section('content')
<div class="py-8 max-w-5xl mx-auto space-y-6">
    {{-- Back Link --}}
    <div class="mb-4">
        <a href="{{ route('dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-800">&larr; Back to Dashboard</a>
    </div>

    {{-- Inspection Request Summary Card --}}
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
            <div class="flex items-center gap-4">
                <div class="flex flex-col items-center justify-center w-14 h-14 rounded-full bg-gray-100 text-gray-500 font-bold text-lg">
                    {{ str_pad($inspectionRequest->id, 4, '0', STR_PAD_LEFT) }}
                </div>
                <div>
                    <div class="text-lg font-semibold text-indigo-700">{{ $inspectionRequest->request_number ?? '-' }}</div>
                    <div class="flex gap-2 mt-1">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                            @if($inspectionRequest->status === 'assigned' || $inspectionRequest->status === 'in_progress') bg-blue-100 text-blue-800
                            @elseif($inspectionRequest->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($inspectionRequest->status === 'completed') bg-green-100 text-green-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst(str_replace('_', ' ', $inspectionRequest->status)) }}
                        </span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                            @if($inspectionRequest->urgency === 'emergency') bg-red-100 text-red-800
                            @elseif($inspectionRequest->urgency === 'urgent') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($inspectionRequest->urgency ?? 'normal') }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="mt-4 md:mt-0 flex flex-col items-end">
                <div class="text-xs text-gray-500">Requested On:</div>
                <div class="text-sm font-semibold">{{ $inspectionRequest->created_at ? $inspectionRequest->created_at->format('M d, Y') : '-' }}</div>
            </div>
        </div>
        <div class="border-t pt-4 mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <div class="text-xs text-gray-500">Package</div>
                <div class="font-semibold text-gray-800">{{ $inspectionRequest->package->display_name ?? '-' }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500">Purpose</div>
                <div class="font-semibold text-gray-800">{{ ucfirst($inspectionRequest->purpose) ?? '-' }}</div>
            </div>
             <div>
                <div class="text-xs text-gray-500">Total Cost</div>
                <div class="font-semibold text-gray-800">{{ number_format($inspectionRequest->total_cost, 0) }} RWF</div>
            </div>
        </div>
    </div>

    {{-- Inspector Information (if assigned) --}}
    @if($inspectionRequest->inspector)
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Assigned Inspector</h3>
        <div class="flex items-center">
            <div class="ml-4">
                <div class="text-base font-medium text-gray-800">{{ $inspectionRequest->inspector->user->full_name }}</div>
                <div class="text-sm text-gray-500">Scheduled for: {{ $inspectionRequest->scheduled_date ? Carbon\Carbon::parse($inspectionRequest->scheduled_date)->format('M d, Y') : 'Not scheduled' }}</div>
            </div>
        </div>
    </div>
    @endif
    
    {{-- Property Information Card --}}
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Property Information</h3>
        </div>
        @if($inspectionRequest->property)
        <dl class="border-t border-gray-200">
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Address</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    {{ $inspectionRequest->property->address }}
                </dd>
            </div>
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Location</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    {{ $inspectionRequest->property->district }}, {{ $inspectionRequest->property->sector }}
                </dd>
            </div>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Property Type</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    {{ $inspectionRequest->property->getTypeDisplayName() }}
                </dd>
            </div>
        </dl>
        @else
        <div class="text-gray-400 p-4">No property details available.</div>
        @endif
    </div>

    {{-- Report Download --}}
    @if($inspectionRequest->status === 'completed' && $inspectionRequest->report)
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-green-600">Report Ready</h3>
                    <p class="mt-1 text-sm text-gray-500">Your inspection report is complete and available for download.</p>
                </div>
                <a href="{{ route('inspection-requests.report.download', $inspectionRequest->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                    Download PDF
                </a>
            </div>
        </div>
    @endif
</div>
@endsection 