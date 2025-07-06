@extends('layouts.business-partner')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Analytics Dashboard</h1>
                        <p class="mt-1 text-sm text-gray-600">Performance insights for {{ $partner->name }}</p>
                    </div>
                    <div class="flex space-x-3">
                        <select id="dateRange" class="border-gray-300 rounded-md shadow-sm focus:ring-primary-brand focus:border-primary-brand sm:text-sm">
                            <option value="7">Last 7 days</option>
                            <option value="30" selected>Last 30 days</option>
                            <option value="90">Last 90 days</option>
                            <option value="365">Last year</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-6">
            <!-- Total Properties -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Properties</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $metrics['total_properties'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Clients -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Clients</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $metrics['total_clients'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Requests -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Requests</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $metrics['total_requests'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Completed Inspections -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Completed</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $metrics['completed_inspections'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Detailed Analytics -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Request Status Distribution -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Request Status Distribution</h3>
                    <div class="space-y-4">
                        @foreach($statusDistribution as $status => $count)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full mr-3
                                        @if($status === 'pending') bg-yellow-400
                                        @elseif($status === 'assigned') bg-blue-400
                                        @elseif($status === 'in_progress') bg-orange-400
                                        @elseif($status === 'completed') bg-green-400
                                        @elseif($status === 'cancelled') bg-red-400
                                        @else bg-gray-400
                                        @endif"></div>
                                    <span class="text-sm font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-sm text-gray-900 font-medium">{{ $count }}</span>
                                    <span class="text-sm text-gray-500 ml-2">({{ $totalRequests > 0 ? round(($count / $totalRequests) * 100, 1) : 0 }}%)</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Property Type Distribution -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Property Type Distribution</h3>
                    <div class="space-y-4">
                        @foreach($propertyTypeDistribution as $type => $count)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full mr-3
                                        @if($type === 'residential') bg-blue-400
                                        @elseif($type === 'commercial') bg-green-400
                                        @elseif($type === 'industrial') bg-purple-400
                                        @else bg-gray-400
                                        @endif"></div>
                                    <span class="text-sm font-medium text-gray-700">{{ ucfirst($type) }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-sm text-gray-900 font-medium">{{ $count }}</span>
                                    <span class="text-sm text-gray-500 ml-2">({{ $totalProperties > 0 ? round(($count / $totalProperties) * 100, 1) : 0 }}%)</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h3>
                @if($recentActivity->count() > 0)
                    <div class="flow-root">
                        <ul class="-mb-8">
                            @foreach($recentActivity as $activity)
                                <li>
                                    <div class="relative pb-8">
                                        @if(!$loop->last)
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white
                                                    @if($activity->status === 'pending') bg-yellow-500
                                                    @elseif($activity->status === 'assigned') bg-blue-500
                                                    @elseif($activity->status === 'in_progress') bg-orange-500
                                                    @elseif($activity->status === 'completed') bg-green-500
                                                    @elseif($activity->status === 'cancelled') bg-red-500
                                                    @else bg-gray-500
                                                    @endif">
                                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">
                                                        Inspection request <span class="font-medium text-gray-900">#{{ $activity->id }}</span> 
                                                        for property <span class="font-medium text-gray-900">{{ $activity->property->property_code }}</span>
                                                        is now <span class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $activity->status)) }}</span>
                                                    </p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    <time datetime="{{ $activity->updated_at->format('Y-m-d') }}">{{ $activity->updated_at->diffForHumans() }}</time>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No recent activity</h3>
                        <p class="mt-1 text-sm text-gray-500">Activity will appear here as inspections progress.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('dateRange').addEventListener('change', function() {
    const range = this.value;
    window.location.href = '{{ route("business-partner.analytics.index") }}?range=' + range;
});
</script>
@endsection 