@extends('layouts.inspector')

@section('title', 'In Progress Assignments')

@section('content')
    <h1 class="text-2xl font-bold mb-6">In Progress Requests</h1>
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
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Started At</th>
                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($inProgressRequests as $request)
                        <tr>
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ $request->request_number }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $request->property->address ?? '-' }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $request->package->display_name ?? '-' }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ ucfirst($request->urgency) }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $request->started_at }}</td>
                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('inspector.requests.show', $request->id) }}" class="inline-block bg-indigo-500 hover:bg-indigo-700 text-white text-xs font-semibold px-4 py-2 rounded">View Request</a>
                                    <a href="{{ route('inspector.requests.report', $request->id) }}" class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-semibold px-4 py-2 rounded">Edit Progress</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 text-center">No in-progress requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection 