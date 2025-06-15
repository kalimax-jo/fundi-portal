@extends('layouts.admin')

@section('title', 'User Details - ' . $user->full_name)

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            {{ $user->full_name }}
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-500">Users</a>
                <span class="mx-2">/</span>
                <span>{{ $user->full_name }}</span>
            </div>
        </div>
    </div>
    <div class="mt-4 flex space-x-3 md:ml-4 md:mt-0">
        <a href="{{ route('admin.users.edit', $user) }}" 
           class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z" />
            </svg>
            Edit User
        </a>
        <a href="{{ route('admin.users.index') }}" 
           class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            Back to Users
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <!-- User Profile Card -->
    <div class="lg:col-span-1">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        @if($user->profile_photo)
                            <img class="h-16 w-16 rounded-full" src="{{ Storage::url($user->profile_photo) }}" alt="{{ $user->full_name }}">
                        @else
                            <div class="h-16 w-16 rounded-full bg-gray-300 flex items-center justify-center">
                                <span class="text-xl font-medium text-gray-700">{{ $user->initials }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ $user->full_name }}</h3>
                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                        @if($user->phone)
                            <p class="text-sm text-gray-500">{{ $user->phone }}</p>
                        @endif
                    </div>
                </div>

                <!-- Status -->
                <div class="mt-4">
                    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium 
                        {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 
                           ($user->status === 'suspended' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </div>

                <!-- User Roles -->
                <div class="mt-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">Roles</h4>
                    <div class="flex flex-wrap gap-2">
                        @forelse($user->roles as $role)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $role->display_name }}
                            </span>
                        @empty
                            <span class="text-sm text-gray-500">No roles assigned</span>
                        @endforelse
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="space-y-3">
                        @if($user->id !== auth()->id())
                        <button onclick="toggleUserStatus({{ $user->id }})" 
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white 
                                {{ $user->status === 'active' ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }}">
                            {{ $user->status === 'active' ? 'Deactivate User' : 'Activate User' }}
                        </button>
                        @endif

                        <div x-data="{ showRoleManager: false }">
                            <button @click="showRoleManager = !showRoleManager" 
                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Manage Roles
                            </button>

                            <!-- Role Management Modal -->
                            <div x-show="showRoleManager" x-transition class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                <div class="flex items-center justify-center min-h-screen px-4">
                                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="showRoleManager = false"></div>
                                    <div class="bg-white rounded-lg p-6 max-w-md w-full relative">
                                        <h3 class="text-lg font-medium mb-4">Manage User Roles</h3>
                                        <div class="space-y-3">
                                            @foreach($user->roles as $role)
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-900">{{ $role->display_name }}</span>
                                                @if(($user->roles->count() > 1) && !($user->id === auth()->id() && $role->name === 'admin'))
                                                <button onclick="removeRole({{ $user->id }}, {{ $role->id }})" 
                                                        class="text-red-600 hover:text-red-800 text-sm">Remove</button>
                                                @endif
                                            </div>
                                            @endforeach
                                        </div>
                                        <div class="mt-4 pt-4 border-t">
                                            <button @click="showRoleManager = false" 
                                                    class="w-full px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                                                Close
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($user->id !== auth()->id())
                        <button onclick="deleteUser({{ $user->id }})" 
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                            Delete User
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Information -->
        <div class="mt-6 bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Account Information</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Account Age</dt>
                        <dd class="text-sm text-gray-900">{{ $stats['account_age'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Last Login</dt>
                        <dd class="text-sm text-gray-900">
                            {{ $stats['last_login'] ? $stats['last_login']->format('M j, Y \a\t g:i A') : 'Never' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created Date</dt>
                        <dd class="text-sm text-gray-900">{{ $user->created_at->format('M j, Y \a\t g:i A') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Updated Date</dt>
                        <dd class="text-sm text-gray-900">{{ $user->updated_at->format('M j, Y \a\t g:i A') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Inspection Requests -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Requests</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['total_inspection_requests'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Completed Inspections -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Completed</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['completed_inspections'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Inspections -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['pending_inspections'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Payments -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Paid</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_payments']) }} RWF</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Inspection Requests -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Inspection Requests</h3>
                @if($user->inspectionRequests->count() > 0)
                    <div class="flow-root">
                        <ul class="-my-5 divide-y divide-gray-200">
                            @foreach($user->inspectionRequests->take(5) as $request)
                            <li class="py-4">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-{{ $request->status === 'pending' ? 'yellow' : ($request->status === 'completed' ? 'green' : 'blue') }}-100 rounded-full flex items-center justify-center">
                                            <div class="w-3 h-3 bg-{{ $request->status === 'pending' ? 'yellow' : ($request->status === 'completed' ? 'green' : 'blue') }}-600 rounded-full"></div>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $request->request_number }}</p>
                                        <p class="text-sm text-gray-500">{{ $request->property->address }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $request->status === 'pending' ? 'yellow' : ($request->status === 'completed' ? 'green' : 'blue') }}-100 text-{{ $request->status === 'pending' ? 'yellow' : ($request->status === 'completed' ? 'green' : 'blue') }}-800">
                                            {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                        </span>
                                        <p class="text-xs text-gray-500 mt-1">{{ $request->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @if($user->inspectionRequests->count() > 5)
                        <div class="mt-4">
                            <a href="{{ route('admin.inspection-requests.index', ['user' => $user->id]) }}" 
                               class="text-sm text-indigo-600 hover:text-indigo-500">
                                View all {{ $user->inspectionRequests->count() }} requests →
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-6">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">No inspection requests yet</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Business Partner Associations (if applicable) -->
        @if($user->businessPartners->count() > 0)
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Business Partner Associations</h3>
                <div class="space-y-3">
                    @foreach($user->businessPartners as $partner)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $partner->name }}</p>
                            <p class="text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $partner->type)) }}</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ ucfirst($partner->pivot->access_level) }}
                            </span>
                            @if($partner->pivot->is_primary_contact)
                                <p class="text-xs text-gray-500 mt-1">Primary Contact</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Inspector Profile (if applicable) -->
        @if($user->inspector)
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Inspector Profile</h3>
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Inspector Code</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $user->inspector->inspector_code }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Certification Level</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($user->inspector->certification_level) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Experience</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $user->inspector->experience_years }} years</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Rating</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $user->inspector->rating }}/5.0</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Total Inspections</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $user->inspector->total_inspections }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Availability</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $user->inspector->availability_status === 'available' ? 'bg-green-100 text-green-800' : 
                                   ($user->inspector->availability_status === 'busy' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($user->inspector->availability_status) }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function toggleUserStatus(userId) {
    if (!confirm('Are you sure you want to change this user\'s status?')) {
        return;
    }

    fetch(`/admin/users/${userId}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        alert('An error occurred while updating user status.');
        console.error('Error:', error);
    });
}

function removeRole(userId, roleId) {
    if (!confirm('Are you sure you want to remove this role from the user?')) {
        return;
    }

    fetch(`/admin/users/${userId}/remove-role/${roleId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        alert('An error occurred while removing the role.');
        console.error('Error:', error);
    });
}

function deleteUser(userId) {
    if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/users/${userId}`;
    form.innerHTML = `
        <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
        <input type="hidden" name="_method" value="DELETE">
    `;
    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush
@endsection