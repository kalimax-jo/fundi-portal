@extends('layouts.headtech')

@section('title', 'Head Technician Dashboard')

@section('content')
    <div class="py-8">
    <h1 class="text-3xl font-bold mb-6">Head Technician Dashboard</h1>
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-indigo-100 rounded shadow p-4 flex items-center">
            <div class="flex-shrink-0"><span class="inline-block w-8 h-8 bg-indigo-400 rounded-full flex items-center justify-center text-white font-bold">{{ $totalInspectors }}</span></div>
            <div class="ml-4">
                <div class="text-xs text-gray-500">Total</div>
                <div class="text-lg font-bold text-indigo-800">Inspectors</div>
            </div>
        </div>
        <div class="bg-green-100 rounded shadow p-4 flex items-center">
            <div class="flex-shrink-0"><span class="inline-block w-8 h-8 bg-green-400 rounded-full flex items-center justify-center text-white font-bold">{{ $availableInspectors }}</span></div>
            <div class="ml-4">
                <div class="text-xs text-gray-500">Available</div>
                <div class="text-lg font-bold text-green-800">Inspectors</div>
            </div>
        </div>
        <div class="bg-yellow-100 rounded shadow p-4 flex items-center">
            <div class="flex-shrink-0"><span class="inline-block w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center text-white font-bold">{{ $busyInspectors }}</span></div>
            <div class="ml-4">
                <div class="text-xs text-gray-500">Busy</div>
                <div class="text-lg font-bold text-yellow-800">Inspectors</div>
            </div>
        </div>
        <div class="bg-gray-100 rounded shadow p-4 flex items-center">
            <div class="flex-shrink-0"><span class="inline-block w-8 h-8 bg-gray-400 rounded-full flex items-center justify-center text-white font-bold">{{ $offlineInspectors }}</span></div>
            <div class="ml-4">
                <div class="text-xs text-gray-500">Offline</div>
                <div class="text-lg font-bold text-gray-800">Inspectors</div>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-blue-100 rounded shadow p-4 flex items-center">
            <div class="flex-shrink-0"><span class="inline-block w-8 h-8 bg-blue-400 rounded-full flex items-center justify-center text-white font-bold">{{ $totalRequests }}</span></div>
            <div class="ml-4">
                <div class="text-xs text-gray-500">Total</div>
                <div class="text-lg font-bold text-blue-800">Requests</div>
            </div>
        </div>
        <div class="bg-yellow-100 rounded shadow p-4 flex items-center">
            <div class="flex-shrink-0"><span class="inline-block w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center text-white font-bold">{{ $pendingRequests }}</span></div>
            <div class="ml-4">
                <div class="text-xs text-gray-500">Pending</div>
                <div class="text-lg font-bold text-yellow-800">Requests</div>
            </div>
        </div>
        <div class="bg-indigo-100 rounded shadow p-4 flex items-center">
            <div class="flex-shrink-0"><span class="inline-block w-8 h-8 bg-indigo-400 rounded-full flex items-center justify-center text-white font-bold">{{ $inProgressRequests }}</span></div>
            <div class="ml-4">
                <div class="text-xs text-gray-500">In Progress</div>
                <div class="text-lg font-bold text-indigo-800">Requests</div>
            </div>
        </div>
        <div class="bg-green-100 rounded shadow p-4 flex items-center">
            <div class="flex-shrink-0"><span class="inline-block w-8 h-8 bg-green-400 rounded-full flex items-center justify-center text-white font-bold">{{ $completedRequests }}</span></div>
            <div class="ml-4">
                <div class="text-xs text-gray-500">Completed</div>
                <div class="text-lg font-bold text-green-800">Requests</div>
            </div>
        </div>
    </div>
    <!-- Quick Links -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <a href="{{ route('headtech.assignments.index') }}" class="block bg-indigo-600 text-white rounded-lg shadow p-6 hover:bg-indigo-700 transition">
            <div class="text-lg font-semibold mb-2">Assign Inspectors</div>
            <div class="text-xs">Quickly assign pending inspection requests to available inspectors.</div>
        </a>
        <a href="{{ route('headtech.inspectors.index') }}" class="block bg-blue-600 text-white rounded-lg shadow p-6 hover:bg-blue-700 transition">
            <div class="text-lg font-semibold mb-2">Manage Inspectors</div>
            <div class="text-xs">View, add, or update inspector details and status.</div>
        </a>
        <a href="{{ route('headtech.inspection-requests.index') }}" class="block bg-green-600 text-white rounded-lg shadow p-6 hover:bg-green-700 transition">
            <div class="text-lg font-semibold mb-2">All Inspection Requests</div>
            <div class="text-xs">Browse, filter, and manage all inspection requests.</div>
        </a>
    </div>
    <!-- Recent Activity -->
    <div class="bg-white rounded shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Recent Activity</h2>
        <div class="space-y-3">
            @forelse($recentActivity as $activity)
                <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="flex-shrink-0 text-xl">
                        {{ $activity->getActivityIcon() }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm {{ $activity->getActivityColorClass() }} font-medium">
                            {{ $activity->getActivityDescription() }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $activity->getTimeElapsed() }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    <div class="text-4xl mb-2">📋</div>
                    <p>No recent activity</p>
                </div>
            @endforelse
        </div>
        @if($recentActivity->count() > 0)
            <div class="mt-4 pt-4 border-t border-gray-200">
                <a href="{{ route('headtech.assignments.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    View all activities →
                </a>
            </div>
        @endif
    </div>
    </div>
@endsection 