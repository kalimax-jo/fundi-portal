@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Admin Dashboard
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                </svg>
                {{ now()->format('l, F j, Y') }}
            </div>
        </div>
    </div>
    <div class="mt-4 flex md:ml-4 md:mt-0">
        <button type="button" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" />
            </svg>
            Export Report
        </button>
        <button type="button" class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
            </svg>
            Quick Actions
        </button>
    </div>
</div>
@endsection

@section('content')
<!-- Stats Grid -->
<div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    <!-- Total Users -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['users']['total']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
                <span class="text-green-600 font-medium">+{{ $stats['users']['new_this_month'] }}</span>
                <span class="text-gray-500"> this month</span>
            </div>
        </div>
    </div>

    <!-- Inspection Requests -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Inspection Requests</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['inspection_requests']['total']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
                <span class="text-orange-600 font-medium">{{ $stats['inspection_requests']['pending'] }} pending</span>
                <span class="text-gray-500"> • {{ $stats['inspection_requests']['today'] }} today</span>
            </div>
        </div>
    </div>

    <!-- Available Inspectors -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Available Inspectors</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $stats['inspectors']['available'] }}/{{ $stats['inspectors']['total'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
                <span class="text-yellow-600 font-medium">{{ $stats['inspectors']['busy'] }} busy</span>
                <span class="text-gray-500"> • {{ $stats['inspectors']['offline'] }} offline</span>
            </div>
        </div>
    </div>

    <!-- Revenue -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-emerald-500 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Revenue</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['financial']['total_revenue']) }} RWF</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
                <span class="text-green-600 font-medium">{{ number_format($stats['financial']['revenue_this_month']) }} RWF</span>
                <span class="text-gray-500"> this month</span>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <!-- Recent Inspection Requests -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Recent Inspection Requests</h3>
                <a href="{{ route('admin.inspection-requests.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500">View all →</a>
            </div>
            <div class="flow-root">
                <ul class="-my-5 divide-y divide-gray-200">
                    @forelse($recentActivities['recent_inspection_requests'] as $request)
                    <li class="py-4">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-{{ $request->status === 'pending' ? 'yellow' : ($request->status === 'completed' ? 'green' : 'blue') }}-100 rounded-full flex items-center justify-center">
                                    <div class="w-3 h-3 bg-{{ $request->status === 'pending' ? 'yellow' : ($request->status === 'completed' ? 'green' : 'blue') }}-600 rounded-full"></div>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $request->request_number }}</p>
                                <p class="text-sm text-gray-500">{{ $request->requester->full_name }} • {{ $request->property->address }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-900">{{ $request->package->display_name }}</p>
                                <p class="text-xs text-gray-500">{{ $request->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="py-4 text-center text-gray-500">No recent inspection requests</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Recent Users</h3>
                <a href="{{ route('admin.users.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500">View all →</a>
            </div>
            <div class="flow-root">
                <ul class="-my-5 divide-y divide-gray-200">
                    @forelse($recentActivities['recent_users'] as $user)
                    <li class="py-4">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-700">{{ $user->initials }}</span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $user->full_name }}</p>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            </div>
                            <div class="text-right">
                                @foreach($user->roles as $role)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $role->display_name }}
                                </span>
                                @endforeach
                                <p class="text-xs text-gray-500 mt-1">{{ $user->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="py-4 text-center text-gray-500">No recent users</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions Panel -->
<div class="mt-8 bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
            <a href="{{ route('admin.users.create') }}" class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="absolute inset-0" aria-hidden="true"></span>
                    <p class="text-sm font-medium text-gray-900">Add User</p>
                </div>
            </a>

            <a href="{{ route('admin.inspectors.create') }}" class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="absolute inset-0" aria-hidden="true"></span>
                    <p class="text-sm font-medium text-gray-900">Add Inspector</p>
                </div>
            </a>

            <a href="{{ route('admin.business-partners.create') }}" class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="absolute inset-0" aria-hidden="true"></span>
                    <p class="text-sm font-medium text-gray-900">Add Partner</p>
                </div>
            </a>

            <a href="{{ route('admin.properties.create') }}" class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="absolute inset-0" aria-hidden="true"></span>
                    <p class="text-sm font-medium text-gray-900">Add Property</p>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- Status Distribution Chart -->
<div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
    <!-- Request Status Chart -->
    <div class="bg-white shadow rounded-lg lg:col-span-2">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Request Status Distribution</h3>
            <div class="space-y-4">
                @foreach(['pending', 'assigned', 'in_progress', 'completed', 'cancelled'] as $status)
                @php
                    $count = $stats['inspection_requests'][$status] ?? 0;
                    $total = $stats['inspection_requests']['total'];
                    $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                    $colorClass = [
                        'pending' => 'bg-yellow-500',
                        'assigned' => 'bg-blue-500',
                        'in_progress' => 'bg-purple-500',
                        'completed' => 'bg-green-500',
                        'cancelled' => 'bg-red-500'
                    ][$status] ?? 'bg-gray-500';
                @endphp
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-3 h-3 {{ $colorClass }} rounded-full mr-3"></div>
                        <span class="text-sm font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $status) }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-500">{{ $count }}</span>
                        <div class="w-24 bg-gray-200 rounded-full h-2">
                            <div class="{{ $colorClass }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                        <span class="text-sm text-gray-500 w-10 text-right">{{ $percentage }}%</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- User Roles Distribution -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">User Roles</h3>
            <div class="space-y-3">
                @foreach($stats['users']['by_role'] as $roleName => $count)
                @php
                    $percentage = $stats['users']['total'] > 0 ? round(($count / $stats['users']['total']) * 100, 1) : 0;
                @endphp
                <div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">{{ $roleName }}</span>
                        <span class="text-sm text-gray-500">{{ $count }}</span>
                    </div>
                    <div class="mt-1 w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Recent Payments and Business Partners -->
<div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
    <!-- Recent Payments -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Recent Payments</h3>
                <a href="{{ route('admin.payments.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500">View all →</a>
            </div>
            <div class="flow-root">
                <ul class="-my-5 divide-y divide-gray-200">
                    @forelse($recentActivities['recent_payments'] as $payment)
                    <li class="py-4">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-{{ $payment->status === 'completed' ? 'green' : ($payment->status === 'failed' ? 'red' : 'yellow') }}-100 rounded-full flex items-center justify-center">
                                    @if($payment->status === 'completed')
                                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    @elseif($payment->status === 'failed')
                                        <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $payment->transaction_reference }}</p>
                                <p class="text-sm text-gray-500">{{ $payment->inspectionRequest->requester->full_name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">{{ number_format($payment->amount) }} {{ $payment->currency }}</p>
                                <p class="text-xs text-gray-500">{{ $payment->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="py-4 text-center text-gray-500">No recent payments</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <!-- Recent Business Partners -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Recent Business Partners</h3>
                <a href="{{ route('admin.business-partners.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500">View all →</a>
            </div>
            <div class="flow-root">
                <ul class="-my-5 divide-y divide-gray-200">
                    @forelse($recentActivities['recent_business_partners'] as $partner)
                    <li class="py-4">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                @if($partner->logo)
                                    <img class="w-8 h-8 rounded-full" src="{{ Storage::url($partner->logo) }}" alt="{{ $partner->name }}">
                                @else
                                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700">{{ substr($partner->name, 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $partner->name }}</p>
                                <p class="text-sm text-gray-500 capitalize">{{ str_replace('_', ' ', $partner->type) }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $partner->partnership_status === 'active' ? 'green' : 'gray' }}-100 text-{{ $partner->partnership_status === 'active' ? 'green' : 'gray' }}-800">
                                    {{ ucfirst($partner->partnership_status) }}
                                </span>
                                <p class="text-xs text-gray-500 mt-1">{{ $partner->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="py-4 text-center text-gray-500">No recent business partners</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-refresh dashboard data every 5 minutes
    setInterval(function() {
        // You can implement AJAX refresh here if needed
        console.log('Dashboard data refresh interval');
    }, 300000); // 5 minutes

    // Initialize tooltips and other interactive elements
    document.addEventListener('DOMContentLoaded', function() {
        // Add any JavaScript initialization here
        console.log('Admin dashboard loaded');
    });
</script>
@endpush
@endsection