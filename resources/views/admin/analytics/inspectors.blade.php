@extends('layouts.admin')

@section('title', 'Inspector Analytics')

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Inspector Analytics
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <svg class="mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Inspector performance and workload analysis
            </div>
        </div>
    </div>
    <div class="mt-4 flex space-x-3 md:ml-4 md:mt-0">
        <a href="{{ route('admin.analytics.overview') }}" 
           class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            Overview
        </a>
        <a href="{{ route('admin.analytics.revenue') }}" 
           class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            Revenue Analytics
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Date Range Filter -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" name="start_date" id="start_date" 
                           value="{{ $startDate->format('Y-m-d') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                    <input type="date" name="end_date" id="end_date" 
                           value="{{ $endDate->format('Y-m-d') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                        Filter
                    </button>
                </div>
                <div class="flex items-end">
                    <a href="{{ route('admin.analytics.inspectors') }}" 
                       class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Performance Summary Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <!-- Total Inspectors -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Inspectors</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ $inspectorPerformance->count() }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Assignments -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Assignments</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ $inspectorPerformance->sum('total_assignments') }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completion Rate -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Avg. Completion Rate</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                @php
                                    $avgCompletionRate = $inspectorPerformance->avg('completion_rate');
                                @endphp
                                {{ round($avgCompletionRate, 1) }}%
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Performer -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Top Performer</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                @php
                                    $topPerformer = $inspectorPerformance->sortByDesc('completion_rate')->first();
                                @endphp
                                @if($topPerformer)
                                    {{ Str::limit($topPerformer->name, 12) }}
                                @else
                                    -
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inspector Performance Table -->
    <div class="bg-white shadow rounded-lg mb-8">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Inspector Performance</h3>
            
            @if($inspectorPerformance->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inspector</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Assignments</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion Rate</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg. Revenue</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($inspectorPerformance as $inspector)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                <span class="text-sm font-medium text-indigo-600">
                                                    {{ strtoupper(substr($inspector->name, 0, 2)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $inspector->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $inspector->total_assignments }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $inspector->completed_assignments }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $inspector->completion_rate >= 90 ? 'bg-green-100 text-green-800' : 
                                           ($inspector->completion_rate >= 75 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ round($inspector->completion_rate, 1) }}%
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($inspector->avg_revenue_per_assignment, 0) }} RWF
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $maxCompletionRate = $inspectorPerformance->max('completion_rate');
                                        $performancePercentage = $maxCompletionRate > 0 ? ($inspector->completion_rate / $maxCompletionRate) * 100 : 0;
                                    @endphp
                                    <div class="flex items-center">
                                        <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $performancePercentage }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-500">{{ round($performancePercentage, 1) }}%</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-sm text-gray-500">No inspector performance data available for the selected date range.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Workload Analysis -->
    <div class="bg-white shadow rounded-lg mb-8">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Workload Analysis</h3>
            
            @if($inspectorWorkload->count() > 0)
                <div class="h-64">
                    <canvas id="workloadChart"></canvas>
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-sm text-gray-500">No workload data available for the selected date range.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Performance Insights -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Performance Insights</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Most Productive Inspector -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">Most Productive Inspector</h4>
                    @php
                        $mostProductive = $inspectorPerformance->sortByDesc('total_assignments')->first();
                    @endphp
                    @if($mostProductive)
                        <p class="text-lg font-semibold text-indigo-600">{{ $mostProductive->name }}</p>
                        <p class="text-sm text-gray-500">{{ $mostProductive->total_assignments }} assignments</p>
                    @else
                        <p class="text-sm text-gray-500">No data available</p>
                    @endif
                </div>
                
                <!-- Highest Completion Rate -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">Highest Completion Rate</h4>
                    @php
                        $highestCompletion = $inspectorPerformance->sortByDesc('completion_rate')->first();
                    @endphp
                    @if($highestCompletion)
                        <p class="text-lg font-semibold text-green-600">{{ $highestCompletion->name }}</p>
                        <p class="text-sm text-gray-500">{{ round($highestCompletion->completion_rate, 1) }}% completion rate</p>
                    @else
                        <p class="text-sm text-gray-500">No data available</p>
                    @endif
                </div>
                
                <!-- Highest Revenue Generator -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">Highest Revenue Generator</h4>
                    @php
                        $highestRevenue = $inspectorPerformance->sortByDesc('avg_revenue_per_assignment')->first();
                    @endphp
                    @if($highestRevenue)
                        <p class="text-lg font-semibold text-yellow-600">{{ $highestRevenue->name }}</p>
                        <p class="text-sm text-gray-500">{{ number_format($highestRevenue->avg_revenue_per_assignment, 0) }} RWF avg.</p>
                    @else
                        <p class="text-sm text-gray-500">No data available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Workload Chart
@if($inspectorWorkload->count() > 0)
const workloadCtx = document.getElementById('workloadChart').getContext('2d');
new Chart(workloadCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($inspectorWorkload->first()->pluck('date')) !!},
        datasets: [
            @foreach($inspectorWorkload as $inspectorName => $workload)
            {
                label: '{{ $inspectorName }}',
                data: {!! json_encode($workload->pluck('daily_assignments')) !!},
                borderColor: 'hsl(' + {{ $loop->index * 60 }} + ', 70%, 50%)',
                backgroundColor: 'hsla(' + {{ $loop->index * 60 }} + ', 70%, 50%, 0.1)',
                tension: 0.1
            }@if(!$loop->last),@endif
            @endforeach
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Daily Assignments'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Date'
                }
            }
        },
        plugins: {
            legend: {
                position: 'top',
            }
        }
    }
});
@endif
</script>
@endpush

@endsection 