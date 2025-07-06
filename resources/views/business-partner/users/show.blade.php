@extends('layouts.business-partner')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">User Details</h1>
                        <p class="mt-1 text-sm text-gray-600">View user information for {{ $partner->name }}</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('business-partner.users.edit', $user) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit User
                        </a>
                        <a href="{{ route('business-partner.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Users
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Information -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- User Profile -->
            <div class="lg:col-span-2">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center mb-6">
                            <div class="flex-shrink-0 h-20 w-20">
                                <img class="h-20 w-20 rounded-full" src="https://ui-avatars.com/api/?name={{ $user->first_name }}+{{ $user->last_name }}&color=7C3AED&background=EBF4FF&size=80" alt="{{ $user->full_name }}">
                            </div>
                            <div class="ml-6">
                                <h2 class="text-2xl font-bold text-gray-900">{{ $user->full_name }}</h2>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                <div class="mt-2 flex items-center space-x-4">
                                    @foreach($user->roles as $role)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($role->name === 'institutional_partner') bg-blue-100 text-blue-800
                                            @elseif($role->name === 'client') bg-green-100 text-green-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                        </span>
                                    @endforeach
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($user->status === 'active') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h3>
                                <dl class="space-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                                        <dd class="text-sm text-gray-900">{{ $user->full_name }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Email Address</dt>
                                        <dd class="text-sm text-gray-900">{{ $user->email }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Phone Number</dt>
                                        <dd class="text-sm text-gray-900">{{ $user->phone ?? 'Not provided' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">National ID</dt>
                                        <dd class="text-sm text-gray-900">{{ $user->national_id ?? 'Not provided' }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Account Information</h3>
                                <dl class="space-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Account Status</dt>
                                        <dd class="text-sm text-gray-900">{{ ucfirst($user->status) }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Created By</dt>
                                        <dd class="text-sm text-gray-900">{{ $user->createdBy ? $user->createdBy->full_name : 'System' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Created Date</dt>
                                        <dd class="text-sm text-gray-900">{{ $user->created_at->format('M j, Y \a\t g:i A') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                        <dd class="text-sm text-gray-900">{{ $user->updated_at->format('M j, Y \a\t g:i A') }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Statistics -->
            <div class="lg:col-span-1">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">User Statistics</h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Properties Owned</span>
                                <span class="text-sm text-gray-900">{{ $user->properties()->count() }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Inspection Requests</span>
                                <span class="text-sm text-gray-900">{{ $user->inspectionRequests()->count() }}</span>
                            </div>
                            @if($user->createdUsers()->count() > 0)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-500">Clients Created</span>
                                    <span class="text-sm text-gray-900">{{ $user->createdUsers()->count() }}</span>
                                </div>
                            @endif
                        </div>

                        @if($user->createdUsers()->count() > 0)
                            <div class="mt-6">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Created Clients</h4>
                                <div class="space-y-2">
                                    @foreach($user->createdUsers()->take(5) as $client)
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-gray-600">{{ $client->full_name }}</span>
                                            <span class="text-gray-400">{{ $client->email }}</span>
                                        </div>
                                    @endforeach
                                    @if($user->createdUsers()->count() > 5)
                                        <div class="text-xs text-gray-400 text-center">
                                            +{{ $user->createdUsers()->count() - 5 }} more
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 