@extends('layouts.app')

@section('title', 'My Properties')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Properties</h1>
        <p class="mt-2 text-gray-600">Manage your properties and view inspection history</p>
    </div>

    <!-- Properties Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @if($properties->count() > 0)
            @foreach($properties as $property)
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ $property->property_code }}</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ ucfirst($property->property_type) }}
                        </span>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Address</p>
                            <p class="text-sm text-gray-600">{{ $property->address }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-900">District</p>
                            <p class="text-sm text-gray-600">{{ $property->district ?? 'N/A' }}</p>
                        </div>
                        
                        @if($property->total_area_sqm)
                        <div>
                            <p class="text-sm font-medium text-gray-900">Area</p>
                            <p class="text-sm text-gray-600">{{ number_format($property->total_area_sqm, 2) }} sqm</p>
                        </div>
                        @endif
                        
                        @if($property->market_value)
                        <div>
                            <p class="text-sm font-medium text-gray-900">Market Value</p>
                            <p class="text-sm text-gray-600">{{ number_format($property->market_value, 0) }} RWF</p>
                        </div>
                        @endif
                        
                        <div>
                            <p class="text-sm font-medium text-gray-900">Inspection History</p>
                            <p class="text-sm text-gray-600">{{ $property->inspectionRequests->count() }} requests</p>
                        </div>
                        
                        @if($property->last_inspection_date)
                        <div>
                            <p class="text-sm font-medium text-gray-900">Last Inspection</p>
                            <p class="text-sm text-gray-600">{{ $property->last_inspection_date->format('M j, Y') }}</p>
                        </div>
                        @endif
                    </div>
                    
                    <div class="mt-6 flex space-x-3">
                        <a href="{{ route('inspection-requests.create') }}?property={{ $property->id }}" 
                           class="flex-1 bg-indigo-600 text-white text-center py-2 px-4 rounded-md text-sm font-medium hover:bg-indigo-700">
                            Request Inspection
                        </a>
                        <button class="bg-gray-100 text-gray-700 py-2 px-4 rounded-md text-sm font-medium hover:bg-gray-200">
                            View Details
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="col-span-full">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No properties found</h3>
                    <p class="mt-1 text-sm text-gray-500">Properties will appear here after you create inspection requests.</p>
                    <div class="mt-6">
                        <a href="{{ route('inspection-requests.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            Create First Request
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
    
    <!-- Pagination -->
    @if($properties->hasPages())
        <div class="mt-8">
            {{ $properties->links() }}
        </div>
    @endif
</div>
@endsection 