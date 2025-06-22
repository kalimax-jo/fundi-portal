@extends('layouts.inspector')

@section('title', 'Inspection Request Details')

@section('content')
<div class="py-8 bg-white min-h-screen px-0">
    <div class="px-8 max-w-4xl mx-auto space-y-8">
        <a href="{{ route('inspector.assignments') }}" class="inline-block mb-2 text-indigo-600 hover:underline">&larr; Back to Assignments</a>

        {{-- Request Summary --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                <div>
                    <h1 class="text-2xl font-bold mb-1">Request #{{ $request->request_number ?? $request->id }}</h1>
                    <div class="flex gap-2 mb-2">
                        <span class="inline-block px-3 py-1 rounded text-xs font-semibold 
                            @if($request->status == 'in_progress') bg-yellow-100 text-yellow-800
                            @elseif($request->status == 'completed') bg-green-100 text-green-800
                            @else bg-indigo-100 text-indigo-800 @endif">
                            {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                        </span>
                        <span class="inline-block px-3 py-1 rounded text-xs font-semibold
                            @if($request->urgency == 'emergency') bg-red-100 text-red-800
                            @elseif($request->urgency == 'urgent') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($request->urgency ?? 'normal') }}
                        </span>
                    </div>
                    <div class="text-sm text-gray-500">Purpose: <span class="text-gray-700">{{ ucfirst(str_replace('_', ' ', $request->purpose ?? '-')) }}</span></div>
                    <div class="text-sm text-gray-500">Preferred Date: <span class="text-gray-700">{{ $request->preferred_date ? $request->preferred_date->format('M d, Y') : '-' }}</span></div>
                    <div class="text-sm text-gray-500">Preferred Time: <span class="text-gray-700">{{ ucfirst($request->preferred_time_slot ?? '-') }}</span></div>
                </div>
                <div class="mt-4 md:mt-0 text-right">
                    <div class="text-xs text-gray-500">Scheduled:</div>
                    <div class="text-sm font-semibold">
                        @if($request->status == 'in_progress')
                            Started: {{ $request->started_at }}
                        @elseif($request->status == 'completed')
                            Completed: {{ $request->completed_at }}
                        @else
                            {{ $request->scheduled_date }} {{ $request->scheduled_time }}
                        @endif
                    </div>
                    <div class="text-xs text-gray-500 mt-2">Total Cost:</div>
                    <div class="text-sm font-semibold">{{ number_format($request->total_cost, 0) }} RWF</div>
                </div>
            </div>
            @if($request->special_instructions)
            <div class="mb-2">
                <div class="text-sm text-gray-500 mb-1">Special Instructions</div>
                <div class="text-gray-700">{{ $request->special_instructions }}</div>
            </div>
            @endif
        </div>

        {{-- Client & Business Partner Info --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold mb-2">Client Information</h2>
                @if($request->requester)
                    <div class="font-semibold text-gray-800">{{ $request->requester->full_name ?? '-' }}</div>
                    <div class="text-sm text-gray-500">Email: <span class="text-gray-700">{{ $request->requester->email ?? '-' }}</span></div>
                    <div class="text-sm text-gray-500">Phone: <span class="text-gray-700">{{ $request->requester->phone ?? '-' }}</span></div>
                    <div class="text-sm text-gray-500">Type: <span class="text-gray-700">{{ ucfirst($request->requester_type) }}</span></div>
                @else
                    <div class="text-gray-400">No client information available.</div>
                @endif
            </div>
            @if($request->businessPartner)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold mb-2">Business Partner</h2>
                <div class="font-semibold text-gray-800">{{ $request->businessPartner->name ?? '-' }}</div>
                <div class="text-sm text-gray-500">Contact: <span class="text-gray-700">{{ $request->businessPartner->contact_person ?? '-' }}</span></div>
                <div class="text-sm text-gray-500">Email: <span class="text-gray-700">{{ $request->businessPartner->contact_email ?? '-' }}</span></div>
                <div class="text-sm text-gray-500">Phone: <span class="text-gray-700">{{ $request->businessPartner->contact_phone ?? '-' }}</span></div>
                <div class="text-sm text-gray-500">Type: <span class="text-gray-700">{{ $request->businessPartner->type ?? '-' }}</span></div>
            </div>
            @endif
        </div>

        {{-- Property Info --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-2">Property Information</h2>
            @if($request->property)
                <div class="mb-2 font-semibold text-gray-800">{{ $request->property->address ?? '-' }}</div>
                <div class="text-sm text-gray-500 mb-2">Type: <span class="text-gray-700">{{ $request->property->getTypeDisplayName() ?? $request->property->property_type }}</span>
                    @if($request->property->property_subtype)
                        ({{ $request->property->getSubtypeDisplayName() }})
                    @endif
                </div>
                <div class="text-sm text-gray-500 mb-2">Owner: <span class="text-gray-700">{{ $request->property->owner_name ?? '-' }}</span></div>
                <div class="text-sm text-gray-500 mb-2">Phone: <span class="text-gray-700">{{ $request->property->owner_phone ?? '-' }}</span></div>
                <div class="text-sm text-gray-500 mb-2">Email: <span class="text-gray-700">{{ $request->property->owner_email ?? '-' }}</span></div>
                <div class="text-sm text-gray-500 mb-2">District: <span class="text-gray-700">{{ $request->property->district ?? '-' }}</span> | Sector: <span class="text-gray-700">{{ $request->property->sector ?? '-' }}</span> | Cell: <span class="text-gray-700">{{ $request->property->cell ?? '-' }}</span></div>
                <div class="text-sm text-gray-500 mb-2">Year Built: <span class="text-gray-700">{{ $request->property->built_year ?? '-' }}</span></div>
                <div class="text-sm text-gray-500 mb-2">Total Area: <span class="text-gray-700">{{ $request->property->total_area_sqm ? number_format($request->property->total_area_sqm) . ' m²' : '-' }}</span></div>
                <div class="text-sm text-gray-500 mb-2">Floors: <span class="text-gray-700">{{ $request->property->floors_count ?? '-' }}</span></div>
                <div class="text-sm text-gray-500 mb-2">Bedrooms: <span class="text-gray-700">{{ $request->property->bedrooms_count ?? '-' }}</span></div>
                <div class="text-sm text-gray-500 mb-2">Bathrooms: <span class="text-gray-700">{{ $request->property->bathrooms_count ?? '-' }}</span></div>
                @if($request->property->latitude && $request->property->longitude)
                    <div class="my-4">
                        <iframe width="100%" height="250" frameborder="0" style="border:0" allowfullscreen
                            src="https://www.google.com/maps?q={{ $request->property->latitude }},{{ $request->property->longitude }}&output=embed">
                        </iframe>
                        <div class="text-xs text-gray-500 mt-1">
                            <a href="https://maps.google.com?q={{ $request->property->latitude }},{{ $request->property->longitude }}" target="_blank" class="text-indigo-600 hover:text-indigo-500">Open in Google Maps</a>
                        </div>
                    </div>
                @endif
                @if($request->property->property_photos && is_array($request->property->property_photos) && count($request->property->property_photos))
                    <div class="mb-2">
                        <div class="text-sm text-gray-500 mb-1">Photos</div>
                        <div class="flex flex-wrap gap-2">
                            @foreach($request->property->property_photos as $photo)
                                <img src="{{ asset('storage/' . $photo) }}" alt="Property Photo" class="w-32 h-24 object-cover rounded border" />
                            @endforeach
                        </div>
                    </div>
                @endif
                @if($request->property->additional_notes)
                    <div class="text-sm text-gray-500 mb-2">Notes: <span class="text-gray-700">{{ $request->property->additional_notes }}</span></div>
                @endif
            @else
                <div class="text-gray-400">No property information available.</div>
            @endif
        </div>

        {{-- Package Info --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-2">Inspection Package</h2>
            @if($request->package)
                <div class="font-semibold text-gray-800 mb-1">{{ $request->package->display_name ?? '-' }}</div>
                <div class="text-sm text-gray-500 mb-2">Description: <span class="text-gray-700">{{ $request->package->description ?? '-' }}</span></div>
                <div class="text-sm text-gray-500 mb-2">Price: <span class="text-gray-700">{{ $request->package->price ? number_format($request->package->price) . ' RWF' : '-' }}</span></div>
                <div class="text-sm text-gray-500 mb-2">Duration: <span class="text-gray-700">{{ $request->package->duration_hours ? $request->package->duration_hours . ' hours' : '-' }}</span></div>
                @php $services = $request->package->services ?? collect(); @endphp
                @if($services->count())
                    <div class="text-sm text-gray-500 mb-1">Included Services:</div>
                    <ul class="list-disc list-inside text-gray-700 mb-2">
                        @foreach($services as $service)
                            <li>{{ $service->name }} <span class="text-xs text-gray-400">({{ $service->getCategoryDisplayName() }})</span></li>
                        @endforeach
                    </ul>
                @endif
            @else
                <div class="text-gray-400">No package information available.</div>
            @endif
        </div>

        {{-- Status History --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-2">Status History</h2>
            @if($request->statusHistory && $request->statusHistory->count())
                <ul class="divide-y divide-gray-200">
                    @foreach($request->statusHistory->sortByDesc('changed_at') as $history)
                        <li class="py-2">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="font-semibold text-gray-700">{{ $history->getChangeSummary() }}</span>
                                    <span class="text-xs text-gray-500 ml-2">by {{ $history->changedByUser->full_name ?? 'System' }}</span>
                                </div>
                                <div class="text-xs text-gray-400">{{ $history->changed_at->format('M d, Y H:i') }}</div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-gray-400">No status history available.</div>
            @endif
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-2">Quick Actions</h2>
            <ul class="divide-y divide-gray-200">
                <li>
                    <a href="#" onclick="window.print(); return false;" class="block py-2 hover:text-indigo-600">Print Request Details</a>
                </li>
                @if($request->property && $request->property->latitude && $request->property->longitude)
                <li>
                    <a href="https://maps.google.com?q={{ $request->property->latitude }},{{ $request->property->longitude }}" target="_blank" class="block py-2 hover:text-indigo-600">View Property on Google Maps</a>
                </li>
                @endif
            </ul>
        </div>
    </div>
</div>
@endsection 