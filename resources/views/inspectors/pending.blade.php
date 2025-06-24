@extends('layouts.inspector')

@section('title', 'Pending Assignments')

@section('content')
        <h1 class="text-2xl font-bold mb-6">Pending Requests</h1>
    <div class="mb-4">
        <form method="GET" class="mb-4 flex flex-wrap items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search requests..." class="border border-gray-300 rounded px-3 py-2 w-64" />
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="border border-gray-300 rounded px-3 py-2" />
            <span>to</span>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="border border-gray-300 rounded px-3 py-2" />
            <select name="urgency" class="border border-gray-300 rounded px-3 py-2">
                <option value="">All Urgencies</option>
                <option value="normal" @if(request('urgency')=='normal') selected @endif>Normal</option>
                <option value="urgent" @if(request('urgency')=='urgent') selected @endif>Urgent</option>
                <option value="emergency" @if(request('urgency')=='emergency') selected @endif>Emergency</option>
            </select>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Filter</button>
        </form>
    </div>
    <div class="overflow-x-auto">
        <div class="inline-block min-w-full align-middle">
            <table class="min-w-full divide-y divide-gray-300">
                <thead>
                    <tr>
                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Request #</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Property</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Package</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Urgency</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Scheduled</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($pendingRequests as $request)
                        <tr>
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ $request->request_number }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $request->property->address ?? '-' }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $request->package->display_name ?? '-' }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ ucfirst($request->urgency) }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $request->scheduled_date }} {{ $request->scheduled_time }}</td>
                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                @if ($request->status === 'completed' && $request->report && $request->report->status === 'completed')
                                    <div class="flex items-center space-x-4">
                                        <a href="{{ route('inspector.requests.report', $request->id) }}" class="inline-flex items-center text-green-600 hover:text-green-900" title="View Report">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('inspector.requests.report', ['id' => $request->id, 'edit' => 'true']) }}" class="inline-flex items-center text-blue-600 hover:text-blue-900" title="Edit Report">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                              <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('inspector.reports.download', $request->report->id) }}" class="inline-flex items-center text-gray-600 hover:text-gray-900" title="Download PDF">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                            </svg>
                                        </a>
                                    </div>
                                @elseif ($request->status === 'in_progress')
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('inspector.requests.show', $request->id) }}" class="inline-block bg-indigo-500 hover:bg-indigo-700 text-white text-xs font-semibold px-4 py-2 rounded">View Request</a>
                                        <a href="{{ route('inspector.requests.report', $request->id) }}" class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-semibold px-4 py-2 rounded">Continue Inspection</a>
                                    </div>
                                @elseif ($request->status === 'assigned')
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('inspector.requests.show', $request->id) }}" class="inline-block bg-indigo-500 hover:bg-indigo-700 text-white text-xs font-semibold px-4 py-2 rounded">View</a>
                                        <form action="{{ route('inspector.requests.start', $request->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-4 py-2 rounded">Start</button>
                                        </form>
                        </div>
        @else
                                    <a href="{{ route('inspector.requests.show', $request->id) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-900">
                                        <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        View Details
                                    </a>
        @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 text-center">No pending requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
    </div>
</div>
@endsection 