@extends('layouts.admin')

@section('title', 'Role Details - ' . $role->display_name)

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            {{ $role->display_name }}
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <a href="{{ route('admin.roles.index') }}" class="text-indigo-600 hover:text-indigo-500">Roles & Permissions</a>
                <span class="mx-2">/</span>
                <span>{{ $role->display_name }}</span>
            </div>
        </div>
    </div>
    <div class="mt-4 flex space-x-3 md:ml-4 md:mt-0">
        <a href="{{ route('admin.roles.edit', $role) }}" 
           class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z" />
            </svg>
            Edit Role
        </a>
        <a href="{{ route('admin.roles.index') }}" 
           class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            Back to Roles
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <!-- Role Information Card -->
    <div class="lg:col-span-1">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ $role->display_name }}</h3>
                        <p class="text-sm text-gray-500">{{ $role->name }}</p>
                    </div>
                </div>

                <!-- Description -->
                @if($role->description)
                <div class="mt-4">
                    <p class="text-sm text-gray-600">{{ $role->description }}</p>
                </div>
                @endif

                <!-- Statistics -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <dl class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Users</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $role->users_count }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Permissions</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ count($role->permissions ?? []) }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Quick Actions -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="space-y-3">
                        <div x-data="{ showPermissionManager: false }">
                            <button @click="showPermissionManager = !showPermissionManager" 
                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Manage Permissions
                            </button>

                            <!-- Permission Management Modal -->
                            <div x-show="showPermissionManager" x-transition class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                <div class="flex items-center justify-center min-h-screen px-4">
                                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="showPermissionManager = false"></div>
                                    <div class="bg-white rounded-lg p-6 max-w-2xl w-full relative max-h-96 overflow-y-auto">
                                        <h3 class="text-lg font-medium mb-4">Role Permissions</h3>
                                        <div class="space-y-4">
                                            @if($role->permissions && count($role->permissions) > 0)
                                                @foreach($groupedPermissions as $group => $permissions)
                                                    @php
                                                        $groupPermissions = array_intersect($permissions, $role->permissions);
                                                    @endphp
                                                    @if(count($groupPermissions) > 0)
                                                        <div>
                                                            <h4 class="text-sm font-medium text-gray-900 mb-2">{{ $group }}</h4>
                                                            <div class="flex flex-wrap gap-2">
                                                                @foreach($groupPermissions as $permission)
                                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                                        {{ $availablePermissions[$permission] ?? str_replace('_', ' ', $permission) }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @else
                                                <p class="text-sm text-gray-500">No permissions assigned to this role.</p>
                                            @endif
                                        </div>
                                        <div class="mt-6 pt-4 border-t">
                                            <button @click="showPermissionManager = false" 
                                                    class="w-full px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                                                Close
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($role->name !== 'admin')
                        <button onclick="deleteRole({{ $role->id }})" 
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                            Delete Role
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Role Information -->
        <div class="mt-6 bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Role Information</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created Date</dt>
                        <dd class="text-sm text-gray-900">{{ $role->created_at->format('M j, Y \a\t g:i A') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                        <dd class="text-sm text-gray-900">{{ $role->updated_at->format('M j, Y \a\t g:i A') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Role Type</dt>
                        <dd class="text-sm text-gray-900">
                            @if($role->isAdminRole())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Admin Role
                                </span>
                            @elseif($role->isStaffRole())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Staff Role
                                </span>
                            @elseif($role->isClientRole())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Client Role
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Custom Role
                                </span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Permissions Grid -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Assigned Permissions</h3>
                @if($role->permissions && count($role->permissions) > 0)
                    <div class="space-y-6">
                        @foreach($groupedPermissions as $group => $permissions)
                            @php
                                $groupPermissions = array_intersect($permissions, $role->permissions);
                            @endphp
                            @if(count($groupPermissions) > 0)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 mb-3">{{ $group }}</h4>
                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                        @foreach($groupPermissions as $permission)
                                        <div class="flex items-start p-3 bg-green-50 border border-green-200 rounded-lg">
                                            <div class="flex-shrink-0">
                                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $availablePermissions[$permission] ?? str_replace('_', ' ', $permission) }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    <code class="bg-gray-100 px-1 py-0.5 rounded">{{ $permission }}</code>
                                                </p>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">No permissions assigned to this role</p>
                        <p class="mt-1">
                            <a href="{{ route('admin.roles.edit', $role) }}" class="text-indigo-600 hover:text-indigo-500">Assign permissions</a>
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Users with this Role -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Users with this Role</h3>
                    <span class="text-sm text-gray-500">{{ $role->users_count }} {{ Str::plural('user', $role->users_count) }}</span>
                </div>
                @if($role->users->count() > 0)
                    <div class="flow-root">
                        <ul class="-my-5 divide-y divide-gray-200">
                            @foreach($role->users->take(10) as $user)
                            <li class="py-4">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        @if($user->profile_photo)
                                            <img class="h-8 w-8 rounded-full" src="{{ Storage::url($user->profile_photo) }}" alt="{{ $user->full_name }}">
                                        @else
                                            <div class="h-8 w-8 bg-gray-300 rounded-full flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-700">{{ $user->initials }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $user->full_name }}</p>
                                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 
                                               ($user->status === 'suspended' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                        <div class="mt-1">
                                            <a href="{{ route('admin.users.show', $user) }}" class="text-xs text-indigo-600 hover:text-indigo-500">View User</a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @if($role->users_count > 10)
                        <div class="mt-4">
                            <a href="{{ route('admin.users.index', ['role' => $role->name]) }}" 
                               class="text-sm text-indigo-600 hover:text-indigo-500">
                                View all {{ $role->users_count }} users with this role →
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-6">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">No users have this role yet</p>
                        <p class="mt-1">
                            <a href="{{ route('admin.users.create') }}" class="text-indigo-600 hover:text-indigo-500">Create a user with this role</a>
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function deleteRole(roleId) {
    if (!confirm('Are you sure you want to delete this role? This action cannot be undone and will affect all users with this role.')) {
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/roles/${roleId}`;
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