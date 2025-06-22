@extends('layouts.headtech')

@section('title', $package->display_name)

@section('page-header')
    <div class="md:flex md:items-center md:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                {{ $package->display_name }}
            </h2>
            <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
                <div class="mt-2 flex items-center text-sm text-gray-500">
                    <svg class="mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Package ID: {{ $package->name }}
                </div>
                <div class="mt-2 flex items-center text-sm text-gray-500">
                    <svg class="mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                    </svg>
                    Created: {{ $package->created_at->format('M d, Y') }}
                </div>
            </div>
        </div>
        <div class="mt-4 flex space-x-3 md:ml-4 md:mt-0">
            <a href="{{ route('headtech.packages.edit', $package) }}" 
               class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                Edit Package
            </a>
            <a href="{{ route('headtech.packages.index') }}" 
               class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                Back to Packages
            </a>
        </div>
    </div>
@endsection

@section('content')
    <!-- Package details and recent requests -->
    <div class="space-y-8">
        <!-- Package details -->
        <div class="max-w-7xl mx-auto">
            <!-- Package Overview -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Basic Information -->
                <div class="lg:col-span-2">
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Package Information</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Package Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $package->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Display Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $package->display_name }}</dd>
                                </div>
                                <div class="md:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $package->description ?: 'No description provided' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Target Client Type</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $package->getTargetClientTypeDisplayName() }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $package->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $package->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </dd>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing & Statistics -->
                <div class="lg:col-span-1">
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Pricing & Statistics</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Pricing</dt>
                                    <dd class="mt-1 text-lg font-semibold text-gray-900">
                                        @if($package->is_custom_quote)
                                            <span class="text-indigo-600">Custom Quote</span>
                                        @else
                                            {{ number_format($package->price, 0) }} {{ $package->currency }}
                                        @endif
                                    </dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Duration</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $package->duration_hours ?? 'Not specified' }} hours</dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Services Included</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $package->services->count() }} services</dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Total Duration</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $package->getTotalDuration() }} minutes</dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Inspection Requests</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $package->inspectionRequests->count() }} requests</dd>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services Included -->
            <div class="bg-white shadow rounded-lg mb-8">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Services Included</h3>
                        <span class="text-sm text-gray-500">{{ $package->services->count() }} services</span>
                    </div>
                    
                    @if($package->services->count() > 0)
                        <div class="space-y-6">
                            @foreach($package->services->groupBy('category') as $category => $categoryServices)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-gray-900 mb-3 capitalize">{{ str_replace('_', ' ', $category) }}</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach($categoryServices as $service)
                                            <div class="bg-gray-50 rounded-lg p-4">
                                                <div class="flex items-start justify-between">
                                                    <div class="flex-1">
                                                        <h5 class="text-sm font-medium text-gray-900">{{ $service->name }}</h5>
                                                        <p class="text-xs text-gray-500 mt-1">{{ $service->description ?: 'No description' }}</p>
                                                        <div class="mt-2 flex items-center space-x-4 text-xs text-gray-500">
                                                            <span>{{ $service->estimated_duration_minutes }} min</span>
                                                            <span>{{ count($service->requires_equipment ?? []) }} equipment</span>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $service->pivot->is_mandatory ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                                            {{ $service->pivot->is_mandatory ? 'Mandatory' : 'Optional' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No services assigned</h3>
                            <p class="mt-1 text-sm text-gray-500">This package doesn't have any services assigned yet.</p>
                            <div class="mt-6">
                                <a href="{{ route('headtech.packages.edit', $package) }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    Add Services
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Inspection Requests -->
        @if ($package->inspectionRequests->count() > 0)
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recent Inspection Requests</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <tbody>
                            @foreach ($package->inspectionRequests as $request)
                                <tr>
                                    <td>
                                        <a href="{{ route('headtech.inspection-requests.show', $request->id) }}">
                                            {{ $request->id }}
                                        </a>
                                    </td>
                                    <!-- other request details -->
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection 