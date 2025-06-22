@extends('layouts.headtech')

@section('title', 'Inspection Requests')

@section('content')
    <div class="py-8">
        <h1 class="text-3xl font-bold mb-2">Inspection Requests</h1>
        <div class="flex items-center text-gray-500 mb-4">
            <span class="mr-4 text-sm"><svg class="inline w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg> {{ $stats['total_requests'] ?? 0 }} total requests</span>
            <span class="text-sm text-red-600"><svg class="inline w-4 h-4 mr-1 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg> {{ $stats['urgent_requests'] ?? 0 }} urgent</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded shadow p-4 flex items-center">
                <svg class="w-6 h-6 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                <div>
                    <div class="text-xs text-gray-500">Total Requests</div>
                    <div class="text-lg font-bold text-gray-900">{{ $stats['total_requests'] ?? 0 }}</div>
                </div>
            </div>
            <div class="bg-white rounded shadow p-4 flex items-center">
                <svg class="w-6 h-6 text-yellow-400 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L8.28 10.5a.75.75 0 00-1.06 1.061l1.5 1.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                <div>
                    <div class="text-xs text-gray-500">Pending</div>
                    <div class="text-lg font-bold text-yellow-600">{{ $stats['pending_requests'] ?? 0 }}</div>
                </div>
            </div>
            <div class="bg-white rounded shadow p-4 flex items-center">
                <svg class="w-6 h-6 text-blue-400 mr-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zM6 8a2 2 0 11-4 0 2 2 0 014 0zM1.49 15.326a.78.78 0 01-.358-.442 3 3 0 014.308-3.516 6.484 6.484 0 00-1.905 3.959c-.023.222-.014.442.025.654a4.97 4.97 0 01-2.07-.655z" /></svg>
                <div>
                    <div class="text-xs text-gray-500">Assigned</div>
                    <div class="text-lg font-bold text-blue-600">{{ $stats['assigned_requests'] ?? 0 }}</div>
                </div>
            </div>
            <div class="bg-white rounded shadow p-4 flex items-center">
                <svg class="w-6 h-6 text-indigo-400 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9.5 9.293 10.793a1 1 0 101.414 1.414l2-2a1 1 0 000-1.414z" clip-rule="evenodd" /></svg>
                <div>
                    <div class="text-xs text-gray-500">In Progress</div>
                    <div class="text-lg font-bold text-indigo-600">{{ $stats['in_progress_requests'] ?? 0 }}</div>
                </div>
            </div>
            <div class="bg-white rounded shadow p-4 flex items-center">
                <svg class="w-6 h-6 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                <div>
                    <div class="text-xs text-gray-500">Completed</div>
                    <div class="text-lg font-bold text-green-600">{{ $stats['completed_requests'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        {{-- Filter/Search Bar --}}
        <div class="bg-white rounded shadow p-4 mb-6">
            <form method="GET" action="" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Request number, requester, property..." class="w-full rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Status</label>
                    <select name="status" class="w-full rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Assigned</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Urgency</label>
                    <select name="urgency" class="w-full rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="">All Urgencies</option>
                        <option value="emergency" {{ request('urgency') === 'emergency' ? 'selected' : '' }}>Emergency</option>
                        <option value="urgent" {{ request('urgency') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                        <option value="normal" {{ request('urgency') === 'normal' ? 'selected' : '' }}>Normal</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Business Partner</label>
                    <select name="business_partner" class="w-full rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="">All Partners</option>
                        @if(isset($businessPartners))
                            @foreach($businessPartners as $id => $name)
                                <option value="{{ $id }}" {{ request('business_partner') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>
                <div class="col-span-1 md:col-span-5 flex gap-2 mt-2">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm">Filter</button>
                    <a href="{{ route('headtech.inspection-requests.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm">Clear</a>
                </div>
            </form>
        </div>

        {{-- Request Cards --}}
        @if(isset($requests) && count($requests))
            <div class="space-y-4">
                @foreach($requests as $request)
                    <div class="bg-white rounded shadow p-4 flex flex-col md:flex-row md:items-center md:justify-between">
                        <div class="flex items-center gap-4 flex-1">
                            <div class="flex flex-col items-center justify-center w-12 h-12 rounded-full bg-gray-100 text-gray-500 font-bold text-lg">
                                {{ str_pad($request->id, 4, '0', STR_PAD_LEFT) }}
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-indigo-700 text-sm">{{ $request->request_number ?? '-' }}</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">{{ ucfirst($request->urgency ?? 'normal') }}</span>
                                    @if($request->status === 'assigned')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 ml-2">Assigned</span>
                                    @elseif($request->status === 'pending')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 ml-2">Pending</span>
                                    @elseif($request->status === 'in_progress')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 ml-2">In Progress</span>
                                    @elseif($request->status === 'completed')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 ml-2">Completed</span>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-700 mt-1">{{ $request->requester->full_name ?? '' }}</div>
                                <div class="text-xs text-gray-500">{{ $request->property->address ?? '' }} &bull; {{ $request->package->display_name ?? '' }}</div>
                                @if($request->status === 'assigned' && $request->assignedInspector && $request->assignedInspector->user)
                                    <div class="text-xs text-blue-700 mt-1 font-semibold">Assigned to: {{ $request->assignedInspector->user->full_name }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2 mt-4 md:mt-0">
                            <a href="{{ route('headtech.inspection-requests.show', $request->id) }}" class="text-blue-600 hover:underline text-sm">View</a>
                            
                            @if($request->status === 'pending')
                                <form action="{{ route('headtech.inspection-requests.assign', $request->id) }}" method="POST" class="flex items-center gap-2 ml-2">
                                    @csrf
                                    <select name="inspector_id" required class="rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                        <option value="">Assign Inspector</option>
                                        @foreach($inspectors as $inspector)
                                            <option value="{{ $inspector->id }}">{{ $inspector->user->full_name ?? 'Inspector '.$inspector->id }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="inline-flex items-center px-3 py-1 bg-indigo-600 text-white text-xs font-semibold rounded hover:bg-indigo-700">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                        Assign
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p>No requests found.</p>
        @endif
    </div>
@endsection 