@extends('layouts.business-partner')

@section('content')
@php
    use App\Helpers\PartnerAccess;
@endphp
<div class="w-full h-full bg-gray-50">
    <!-- Page Header -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Inspection Requests</h1>
                    <p class="mt-1 text-sm text-gray-600">Manage inspection requests for {{ $partner->name }}</p>
                </div>
                @php
                    $canRequest = \App\Helpers\PartnerAccess::can('create_request', $partner);
                @endphp
                <a href="{{ $canRequest ? route('business-partner.inspection-requests.create') : '#' }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 {{ $canRequest ? 'hover:bg-indigo-700 active:bg-indigo-700' : 'opacity-50 cursor-not-allowed' }}"
                   @if(!$canRequest) tabindex="-1" aria-disabled="true" title="You cannot create a new request: either your quota is used up, you have no active tier, or your tier does not allow any packages." @endif>
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    New Request
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('business-partner.inspection-requests.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-brand focus:border-primary-brand sm:text-sm">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div>
                    <label for="property_type" class="block text-sm font-medium text-gray-700">Property Type</label>
                    <select name="property_type" id="property_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-brand focus:border-primary-brand sm:text-sm">
                        <option value="">All Types</option>
                        <option value="residential" {{ request('property_type') == 'residential' ? 'selected' : '' }}>Residential</option>
                        <option value="commercial" {{ request('property_type') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                        <option value="industrial" {{ request('property_type') == 'industrial' ? 'selected' : '' }}>Industrial</option>
                    </select>
                </div>
                <div>
                    <label for="client" class="block text-sm font-medium text-gray-700">Client</label>
                    <select name="client" id="client" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-brand focus:border-primary-brand sm:text-sm">
                        <option value="">All Clients</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ request('client') == $client->id ? 'selected' : '' }}>
                                {{ $client->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-brand hover:bg-primary-brand focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-brand">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Inspection Requests Table -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            @if($inspectionRequests->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Package</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inspector</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($inspectionRequests as $request)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">#{{ $request->id }}</div>
                                                <div class="text-sm text-gray-500">{{ $request->created_at->format('M j, Y') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $request->property->property_code }}</div>
                                        <div class="text-sm text-gray-500">{{ $request->property->address }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $clientName = $request->client->full_name ?? $request->property->client_name ?? $request->property->owner_name ?? 'N/A';
                                            $clientEmail = $request->client->email ?? $request->property->owner_email ?? 'N/A';
                                        @endphp
                                        <div class="text-sm text-gray-900">{{ $clientName }}</div>
                                        <div class="text-sm text-gray-500">{{ $clientEmail }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $request->package->name ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">{{ $request->package->price ? 'RWF ' . number_format($request->package->price) : 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($request->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($request->status === 'assigned') bg-blue-100 text-blue-800
                                            @elseif($request->status === 'in_progress') bg-orange-100 text-orange-800
                                            @elseif($request->status === 'completed') bg-green-100 text-green-800
                                            @elseif($request->status === 'cancelled') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($request->inspector)
                                            {{ $request->inspector->user->full_name }}
                                        @else
                                            <span class="text-gray-400">Not assigned</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $request->created_at->format('M j, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <a href="{{ route('business-partner.inspection-requests.show', $request) }}" class="text-blue-600 hover:text-blue-900" title="View">
                                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                            </a>
                                            @if($request->status === 'completed' && $request->report)
                                                <a href="{{ route('business-partner.reports.show', $request->report) }}" class="text-green-600 hover:text-green-900" title="Download Report">
                                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" /></svg>
                                                </a>
                                            @endif
                                            @if($request->status === 'pending' || !$request->inspector)
                                                <a href="{{ route('business-partner.inspection-requests.edit', $request) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.828a4 4 0 01-2.828 1.172H7v-2a4 4 0 011.172-2.828z" /></svg>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $inspectionRequests->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No inspection requests</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new inspection request.</p>
                    @php
                        $canRequest = \App\Helpers\PartnerAccess::can('create_request', $partner);
                    @endphp
                    <div class="mt-6">
                        <a href="{{ $canRequest ? route('business-partner.inspection-requests.create') : '#' }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-brand focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-brand {{ $canRequest ? 'hover:bg-primary-brand active:bg-primary-brand' : 'opacity-50 cursor-not-allowed' }}"
                           @if(!$canRequest) tabindex="-1" aria-disabled="true" title="You cannot create a new request: either your quota is used up, you have no active tier, or your tier does not allow any packages." @endif>
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            New Request
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 