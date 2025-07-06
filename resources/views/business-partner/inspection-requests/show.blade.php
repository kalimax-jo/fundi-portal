@extends('layouts.business-partner')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
            <div class="p-8 pb-4 border-b flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100">
                        <svg class="h-7 w-7 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Inspection Request #{{ $inspectionRequest->id }}</h1>
                        <div class="text-sm text-gray-500">Created {{ $inspectionRequest->created_at->format('M j, Y') }}</div>
                    </div>
                </div>
                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                    @if($inspectionRequest->status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($inspectionRequest->status === 'in_progress') bg-blue-100 text-blue-800
                    @elseif($inspectionRequest->status === 'completed') bg-green-100 text-green-800
                    @else bg-gray-200 text-gray-700 @endif">
                    {{ ucfirst(str_replace('_', ' ', $inspectionRequest->status)) }}
                </span>
            </div>
            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Property Info -->
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="h-5 w-5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <h2 class="text-lg font-semibold">Property</h2>
                    </div>
                    <div class="text-gray-700 mb-1"><span class="font-semibold">Code:</span> {{ $inspectionRequest->property->property_code }}</div>
                    <div class="text-gray-700 mb-1"><span class="font-semibold">Address:</span> {{ $inspectionRequest->property->address }}</div>
                </div>
                <!-- Client Info -->
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="h-5 w-5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <h2 class="text-lg font-semibold">Client</h2>
                    </div>
                    @php
                        $clientName = $inspectionRequest->client->full_name ?? $inspectionRequest->property->client_name ?? $inspectionRequest->property->owner_name ?? 'N/A';
                        $clientEmail = $inspectionRequest->client->email ?? $inspectionRequest->property->owner_email ?? 'N/A';
                    @endphp
                    <div class="text-gray-700 mb-1"><span class="font-semibold">Name:</span> {{ $clientName }}</div>
                    <div class="text-gray-700 mb-1"><span class="font-semibold">Email:</span> {{ $clientEmail }}</div>
                </div>
                <!-- Package Info -->
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="h-5 w-5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                        <h2 class="text-lg font-semibold">Package</h2>
                    </div>
                    <div class="text-gray-700 mb-1"><span class="font-semibold">Name:</span> {{ $inspectionRequest->package->name ?? 'N/A' }}</div>
                    <div class="text-gray-700 mb-1"><span class="font-semibold">Price:</span> {{ $inspectionRequest->package->price ? 'RWF ' . number_format($inspectionRequest->package->price) : 'N/A' }}</div>
                </div>
                <!-- Preferred Date & Notes -->
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="h-5 w-5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <h2 class="text-lg font-semibold">Other Details</h2>
                    </div>
                    <div class="text-gray-700 mb-1"><span class="font-semibold">Preferred Date:</span> {{ $inspectionRequest->preferred_date ? $inspectionRequest->preferred_date->format('M j, Y') : 'N/A' }}</div>
                    <div class="text-gray-700 mb-1"><span class="font-semibold">Notes:</span> {{ $inspectionRequest->special_instructions ?? '-' }}</div>
                </div>
            </div>
            <div class="px-8 pb-8 flex flex-col md:flex-row md:justify-between md:items-center gap-4 border-t pt-6 bg-gray-50">
                <a href="{{ route('business-partner.inspection-requests.index') }}" class="inline-flex items-center px-6 py-2 bg-indigo-600 text-white font-semibold rounded-lg shadow hover:bg-indigo-700 transition">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    Back to List
                </a>
                <div class="text-xs text-gray-400">Inspection Request ID: {{ $inspectionRequest->id }}</div>
            </div>
        </div>
    </div>
</div>
@endsection 