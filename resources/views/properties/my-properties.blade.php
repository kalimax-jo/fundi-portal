@extends('layouts.app')

@section('title', 'My Properties')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">My Properties</h1>
        <a href="{{ route('inspection-requests.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
            + Add New Property
        </a>
    </div>

    @if($properties->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($properties as $property)
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <div class="p-5">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-gray-500">{{ $property->property_code }}</p>
                                <h3 class="text-lg font-semibold text-gray-800">{{ $property->address }}</h3>
                                <p class="text-sm text-gray-600">{{ $property->district }}, {{ $property->sector }}</p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $property->getTypeDisplayName() }}
                            </span>
                        </div>
                        <div class="mt-4 border-t pt-4">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Total Inspections</span>
                                <span class="font-medium text-gray-800">{{ $property->inspection_requests_count }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600 mt-1">
                                <span>Last Inspected</span>
                                <span class="font-medium text-gray-800">{{ $property->last_inspection_date ? $property->last_inspection_date->format('M j, Y') : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">View History &rarr;</a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $properties->links() }}
        </div>
    @else
        <div class="text-center py-12 bg-white rounded-lg shadow">
            <h3 class="text-lg font-medium text-gray-900">No properties found</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by adding a new property and requesting an inspection.</p>
            <div class="mt-6">
                <a href="{{ route('inspection-requests.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Add Property & Request Inspection
                </a>
            </div>
        </div>
    @endif
</div>
@endsection 