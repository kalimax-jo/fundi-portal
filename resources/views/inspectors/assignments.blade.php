@extends('layouts.inspector')

@section('title', 'My Assignments')

@section('content')
    <h1 class="text-2xl font-bold mb-6">My Assignments</h1>
    <div class="mb-4">
        <form method="GET" class="mb-4 flex flex-wrap items-center gap-2">
            <select name="status" class="border border-gray-300 rounded px-3 py-2">
                <option value="">All Statuses</option>
                <option value="assigned" @if(request('status')=='assigned') selected @endif>Assigned</option>
                <option value="in_progress" @if(request('status')=='in_progress') selected @endif>In Progress</option>
            </select>
            <select name="urgency" class="border border-gray-300 rounded px-3 py-2">
                <option value="">All Urgencies</option>
                <option value="normal" @if(request('urgency')=='normal') selected @endif>Normal</option>
                <option value="urgent" @if(request('urgency')=='urgent') selected @endif>Urgent</option>
                <option value="emergency" @if(request('urgency')=='emergency') selected @endif>Emergency</option>
            </select>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search requests..." class="border border-gray-300 rounded px-3 py-2 w-64" />
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
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Assigned</th>
                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0"><span class="sr-only">View</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($allAssignments as $request)
                        <tr>
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ $request->request_number }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $request->property->address ?? '-' }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $request->package->display_name ?? '-' }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"><span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $request->status_color }}">{{ $request->status_text }}</span></td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $request->assigned_at ? $request->assigned_at->format('M d, Y') : '-' }}</td>
                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                <a href="{{ route('inspector.requests.show', $request->id) }}" class="text-indigo-600 hover:text-indigo-900">View<span class="sr-only">, {{ $request->request_number }}</span></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 text-center">No assignments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $allAssignments->links() }}
            </div>
        </div>
    </div>
@endsection 