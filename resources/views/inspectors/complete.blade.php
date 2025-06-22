@extends('layouts.inspector')

@section('title', 'Completed Assignments')

@section('content')
<div class="py-8 bg-white min-h-screen px-0">
    <div class="px-8">
        <h1 class="text-2xl font-bold mb-6">Completed Requests</h1>
        @if($completedRequests->count())
            <ul class="divide-y divide-gray-200 bg-white rounded-lg shadow">
                @foreach($completedRequests as $request)
                    <li class="p-4">
                        <div class="font-semibold text-green-700">
                            {{ $request->request_number ?? 'Request #' . $request->id }}
                            <span class="ml-2 px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-800">Completed</span>
                        </div>
                        <div class="text-xs text-gray-500 mb-2">{{ $request->property->address ?? '-' }} | {{ ucfirst($request->urgency) }} | {{ $request->package->display_name ?? '-' }}</div>
                        <div class="text-xs text-gray-400">Completed: {{ $request->completed_at }}</div>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="text-gray-400 text-center py-8">No completed requests found.</div>
        @endif
    </div>
</div>
@endsection 