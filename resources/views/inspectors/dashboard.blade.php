@extends('layouts.inspector')

@section('title', 'Inspector Dashboard')

@section('content')
    <h1 class="text-3xl font-bold mb-6">Welcome, {{ $inspector->user->full_name ?? 'Inspector' }}</h1>
    
    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-indigo-100 rounded-lg shadow p-4 flex items-center">
            <div class="flex-shrink-0"><span class="inline-block w-8 h-8 bg-indigo-400 rounded-full flex items-center justify-center text-white font-bold">{{ $assignedRequests->count() }}</span></div>
            <div class="ml-4">
                <div class="text-xs text-gray-500">Assigned</div>
                <div class="text-base font-bold text-indigo-800">Requests</div>
            </div>
        </div>
        <div class="bg-yellow-100 rounded-lg shadow p-4 flex items-center">
            <div class="flex-shrink-0"><span class="inline-block w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center text-white font-bold">{{ $inProgressRequests->count() }}</span></div>
            <div class="ml-4">
                <div class="text-xs text-gray-500">In Progress</div>
                <div class="text-base font-bold text-yellow-800">Requests</div>
            </div>
        </div>
        <div class="bg-green-100 rounded-lg shadow p-4 flex items-center">
            <div class="flex-shrink-0"><span class="inline-block w-8 h-8 bg-green-400 rounded-full flex items-center justify-center text-white font-bold">{{ $inspector->completedInspections()->count() }}</span></div>
            <div class="ml-4">
                <div class="text-xs text-gray-500">Completed</div>
                <div class="text-base font-bold text-green-800">Requests</div>
            </div>
        </div>
    </div>
   
    <!-- Recent Activity -->
    <div>
        <h2 class="text-lg font-semibold mb-4">Recent Activity</h2>
        <ul class="text-sm text-gray-600 space-y-2">
            @php
                $recentActivity = $inspector->inspectionRequests()
                    ->with(['statusHistory' => function($q) { $q->orderByDesc('changed_at'); }])
                    ->get()
                    ->pluck('statusHistory')
                    ->flatten()
                    ->sortByDesc('changed_at')
                    ->take(5);
            @endphp
            @forelse($recentActivity as $activity)
                <li class="flex items-center justify-between">
                    <div>
                        <span class="font-semibold text-indigo-700">{{ $activity->inspectionRequest->request_number ?? 'Request #' . $activity->inspection_request_id }}</span>
                        <span class="mx-1">&mdash;</span>
                        {{ $activity->getChangeSummary() }}
                    </div>
                    <span class="text-xs text-gray-400 ml-2">{{ $activity->getTimeElapsed() }}</span>
                </li>
            @empty
                <li>No recent activity.</li>
            @endforelse
        </ul>
    </div>
@endsection 