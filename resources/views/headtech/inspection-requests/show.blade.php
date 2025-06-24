@extends('layouts.headtech')

@section('title', 'Inspection Request Details')

@section('content')
<div class="py-8 max-w-5xl mx-auto space-y-6">
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
                            @if($inspectionRequest->status === 'assigned') bg-blue-100 text-blue-800
                            @elseif($inspectionRequest->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($inspectionRequest->status === 'in_progress') bg-indigo-100 text-indigo-800
                            @elseif($inspectionRequest->status === 'completed') bg-green-100 text-green-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($inspectionRequest->status) }}
                        </span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                            @if($inspectionRequest->urgency === 'emergency') bg-red-100 text-red-800
                            @elseif($inspectionRequest->urgency === 'urgent') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($inspectionRequest->urgency ?? 'normal') }}
                        </span>
                        @if($inspectionRequest->inspector && $inspectionRequest->inspector->user)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                                Assigned to: {{ $inspectionRequest->inspector->user->full_name }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="mt-4 md:mt-0 flex flex-col items-end">
                <div class="text-xs text-gray-500">Preferred Date:</div>
                <div class="text-sm font-semibold">{{ $inspectionRequest->preferred_date ? $inspectionRequest->preferred_date->format('M d, Y') : '-' }}</div>
                <div class="text-xs text-gray-500">Preferred Time: {{ ucfirst($inspectionRequest->preferred_time_slot ?? '-') }}</div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div>
                <div class="text-xs text-gray-500">Requester</div>
                <div class="font-semibold text-gray-800">{{ $inspectionRequest->requester->full_name ?? '-' }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500">Package</div>
                <div class="font-semibold text-gray-800">{{ $inspectionRequest->package->display_name ?? '-' }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500">Business Partner</div>
                <div class="font-semibold text-gray-800">{{ $inspectionRequest->businessPartner->name ?? '-' }}</div>
            </div>
        </div>
    </div>

    {{-- Property Information Card --}}
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Property Information</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Complete details about this property.</p>
        </div>
        <div class="border-t border-gray-200">
            @if($inspectionRequest->property)
            <dl>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Owner Information</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="space-y-1">
                            <p class="font-medium">{{ $inspectionRequest->property->owner_name ?: 'Not specified' }}</p>
                            @if($inspectionRequest->property->owner_phone)
                                <p class="text-gray-600">Phone: {{ $inspectionRequest->property->owner_phone }}</p>
                            @endif
                            @if($inspectionRequest->property->owner_email)
                                <p class="text-gray-600">Email: {{ $inspectionRequest->property->owner_email }}</p>
                            @endif
                        </div>
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Property Type</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $inspectionRequest->property->getTypeDisplayName() }}
                            </span>
                            @if($inspectionRequest->property->property_subtype)
                                <span class="text-gray-600">• {{ $inspectionRequest->property->getSubtypeDisplayName() }}</span>
                            @endif
                        </div>
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Address</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="space-y-1">
                            <p>{{ $inspectionRequest->property->address }}</p>
                            <p class="text-gray-600">
                                <b>District:</b> {{ $inspectionRequest->property->district }} <b> Sector:</b> {{ $inspectionRequest->property->sector ? ' ' . $inspectionRequest->property->sector : '' }}   <b>Cell:</b>{{ $inspectionRequest->property->cell ? ' ' . $inspectionRequest->property->cell : '' }}
                            </p>
                            @if($inspectionRequest->property->latitude && $inspectionRequest->property->longitude)
                                <p class="text-gray-600">
                                    GPS Coordinates: {{ $inspectionRequest->property->latitude }}, {{ $inspectionRequest->property->longitude }}
                                    <a href="https://maps.google.com?q={{ $inspectionRequest->property->latitude }},{{ $inspectionRequest->property->longitude }}" target="_blank" class="text-indigo-600 hover:text-indigo-500 ml-2">View on Map</a>
                                </p>
                            @endif
                        </div>
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Specifications</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="grid grid-cols-2 gap-4">
                            @if($inspectionRequest->property->built_year)
                                <div><span class="font-medium">Year Built:</span> {{ $inspectionRequest->property->built_year }}</div>
                            @endif
                            @if($inspectionRequest->property->total_area_sqm)
                                <div><span class="font-medium">Total Area:</span> {{ number_format($inspectionRequest->property->total_area_sqm) }} m²</div>
                            @endif
                            @if($inspectionRequest->property->floors_count)
                                <div><span class="font-medium">Floors:</span> {{ $inspectionRequest->property->floors_count }}</div>
                            @endif
                            @if($inspectionRequest->property->bedrooms_count)
                                <div><span class="font-medium">Bedrooms:</span> {{ $inspectionRequest->property->bedrooms_count }}</div>
                            @endif
                            @if($inspectionRequest->property->bathrooms_count)
                                <div><span class="font-medium">Bathrooms:</span> {{ $inspectionRequest->property->bathrooms_count }}</div>
                            @endif
                        </div>
                    </dd>
                </div>
                @if($inspectionRequest->property->additional_notes)
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Additional Notes</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $inspectionRequest->property->additional_notes }}
                    </dd>
                </div>
                @endif
            </dl>
            @else
            <div class="text-gray-400 p-4">No property details available.</div>
            @endif
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Quick Actions</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Common actions for this property.</p>
        </div>
        <div class="border-t border-gray-200">
            <ul class="divide-y divide-gray-200">
                
               
              
                </li>
                <li>
                    <a href="https://maps.google.com?q={{ $inspectionRequest->property->latitude }},{{ $inspectionRequest->property->longitude }}" target="_blank" class="block px-4 py-4 hover:bg-gray-50 flex items-center justify-between">
                        <span>View on Google Maps</span>
                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                    </a>
                </li>
                
                <li>
                    <a href="#" onclick="window.print(); return false;" class="block px-4 py-4 hover:bg-gray-50 flex items-center justify-between">
                        <span>Print Property Details</span>
                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection 