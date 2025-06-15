{{-- File Path: resources/views/admin/inspection-requests/index.blade.php --}}

@extends('layouts.admin')

@section('title', 'Inspection Requests')

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Inspection Requests
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <svg class="mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9 12h3.75M9 15h3.75M9 18h3.75m3-6h6m-6 3h6m-6 3h6m-12-9h6V6a3 3 0 013-3h6a3 3 0 013 3v15a3 3 0 01-3 3H6a3 3 0 01-3-3V9z" />
                </svg>
                {{ $requests->total() }} total requests
            </div>
            <div class="mt-2 flex items-center text-sm text-red-600">
                <svg class="mr-1.5 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                {{ $stats['urgent_requests'] }} urgent
            </div>
        </div>
    </div>
    <div class="mt-4 flex space-x-3 md:ml-4 md:mt-0">
        <a href="{{ route('admin.inspection-requests.pending') }}" 
           class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L8.28 10.5a.75.75 0 00-1.06 1.061l1.5 1.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
            </svg>
            Pending ({{ $stats['pending_requests'] }})
        </a>
        <a href="{{ route('admin.inspection-requests.assign') }}" 
           class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 9a3 3 0 100-6 3 3 0 000 6zM6 8a2 2 0 11-4 0 2 2 0 014 0zM1.49 15.326a.78.78 0 01-.358-.442 3 3 0 014.308-3.516 6.484 6.484 0 00-1.905 3.959c-.023.222-.014.442.025.654a4.97 4.97 0 01-2.07-.655zM16.44 15.98a4.97 4.97 0 002.07-.654.78.78 0 00.357-.442 3 3 0 00-4.308-3.517 6.484 6.484 0 011.907 3.96 2.32 2.32 0 01-.026.654z" />
            </svg>
            Assign Inspectors
        </a>
        <a href="{{ route('admin.inspection-requests.create') }}" 
           class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
            </svg>
            New Request
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5">
        <!-- Total Requests -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Requests</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_requests']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L8.28 10.5a.75.75 0 00-1.06 1.061l1.5 1.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                            <dd class="text-lg font-medium text-yellow-600">{{ number_format($stats['pending_requests']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assigned -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zM6 8a2 2 0 11-4 0 2 2 0 014 0zM1.49 15.326a.78.78 0 01-.358-.442 3 3 0 014.308-3.516 6.484 6.484 0 00-1.905 3.959c-.023.222-.014.442.025.654a4.97 4.97 0 01-2.07-.655z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Assigned</dt>
                            <dd class="text-lg font-medium text-blue-600">{{ number_format($stats['assigned_requests']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9.5 9.293 10.793a1 1 0 101.414 1.414l2-2a1 1 0 000-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">In Progress</dt>
                            <dd class="text-lg font-medium text-indigo-600">{{ number_format($stats['in_progress_requests']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Completed</dt>
                            <dd class="text-lg font-medium text-green-600">{{ number_format($stats['completed_requests']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form method="GET" action="{{ route('admin.inspection-requests.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                               placeholder="Request number, requester, property..."
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Assigned</option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <!-- Urgency Filter -->
                    <div>
                        <label for="urgency" class="block text-sm font-medium text-gray-700">Urgency</label>
                        <select name="urgency" id="urgency" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">All Urgencies</option>
                            <option value="emergency" {{ request('urgency') === 'emergency' ? 'selected' : '' }}>Emergency</option>
                            <option value="urgent" {{ request('urgency') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                            <option value="normal" {{ request('urgency') === 'normal' ? 'selected' : '' }}>Normal</option>
                        </select>
                    </div>

                    <!-- Business Partner Filter -->
                    <div>
                        <label for="business_partner" class="block text-sm font-medium text-gray-700">Business Partner</label>
                        <select name="business_partner" id="business_partner" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">All Partners</option>
                            @foreach($businessPartners as $id => $name)
                            <option value="{{ $id }}" {{ request('business_partner') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.inspection-requests.index') }}" 
                       class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Clear
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Requests Table -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        @if($requests->count() > 0)
        <ul role="list" class="divide-y divide-gray-200">
            @foreach($requests as $request)
            <li>
                <a href="{{ route('admin.inspection-requests.show', $request) }}" class="block hover:bg-gray-50">
                    <div class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center min-w-0 flex-1">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ substr($request->request_number, -4) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4 min-w-0 flex-1">
                                    <div class="flex items-center">
                                        <p class="text-sm font-medium text-indigo-600 truncate">
                                            {{ $request->request_number }}
                                        </p>
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $request->urgency === 'emergency' ? 'bg-red-100 text-red-800' : 
                                               ($request->urgency === 'urgent' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ ucfirst($request->urgency) }}
                                        </span>
                                    </div>
                                    <div class="mt-1">
                                        <p class="text-sm text-gray-900">
                                            {{ $request->requester->full_name }} 
                                            @if($request->businessPartner)
                                            <span class="text-gray-500">via {{ $request->businessPartner->name }}</span>
                                            @endif
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $request->property->address }} • {{ $request->package->display_name }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $request->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                       ($request->status === 'assigned' ? 'bg-blue-100 text-blue-800' : 
                                       ($request->status === 'in_progress' ? 'bg-indigo-100 text-indigo-800' : 
                                       ($request->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'))) }}">
                                    {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                </span>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $request->created_at->format('M j, Y') }}
                                </p>
                                @if($request->assignedInspector)
                                <p class="text-xs text-gray-400">
                                    {{ $request->assignedInspector->user->full_name }}
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            </li>
            @endforeach
        </ul>

        <!-- Pagination -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $requests->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No inspection requests</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating a new inspection request.</p>
            <div class="mt-6">
                <a href="{{ route('admin.inspection-requests.create') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                    </svg>
                    New Inspection Request
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection