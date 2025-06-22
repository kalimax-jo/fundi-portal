@extends('layouts.inspector')

@section('title', 'In Progress Assignments')

@section('content')
<div class="py-8 bg-white min-h-screen px-0">
    <div class="px-8">
        <h1 class="text-2xl font-bold mb-6">In Progress Requests</h1>
        @if($inProgressRequests->count())
            <ul class="divide-y divide-gray-200 bg-white rounded-lg shadow">
                @foreach($inProgressRequests as $request)
                    <li class="p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                        <div>
                            <div class="font-semibold text-yellow-700">
                                {{ $request->request_number ?? 'Request #' . $request->id }}
                                <span class="ml-2 px-2 py-1 rounded text-xs font-semibold bg-yellow-100 text-yellow-800">In Progress</span>
                            </div>
                            <div class="text-xs text-gray-500 mb-2">{{ $request->property->address ?? '-' }} | {{ ucfirst($request->urgency) }} | {{ $request->package->display_name ?? '-' }}</div>
                            <div class="text-xs text-gray-400">Started: {{ $request->started_at }}</div>
                        </div>
                        <div class="flex-shrink-0 flex flex-col gap-2 items-end">
                            <a href="{{ route('inspector.requests.show', $request->id) }}" class="inline-block bg-indigo-500 hover:bg-indigo-700 text-white text-xs font-semibold px-4 py-2 rounded mb-1">View Request</a>
                            <a href="{{ route('inspector.requests.report', $request->id) }}" class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-semibold px-4 py-2 rounded">Edit Progress</a>
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="text-gray-400 text-center py-8">No in-progress requests found.</div>
        @endif
    </div>
</div>
@endsection 