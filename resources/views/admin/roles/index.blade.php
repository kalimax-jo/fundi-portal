@extends('layouts.admin')

@section('title', 'Roles & Permissions')

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Roles & Permissions
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                Total: {{ $roles->total() }} roles
            </div>
        </div>
    </div>
    <div class="mt-4 flex md:ml-4 md:mt-0">
        <a href="{{ route('admin.roles.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
            </svg>
            Add Role
        </a>
    </div>
</div>
@endsection

@section('content')
<!-- Search -->
<div class="bg-white shadow rounded-lg mb-6">
    <div class="px-4 py-5 sm:p-6">
        <form method="GET" action="{{ route('admin.roles.index') }}" class="flex gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700">Search Roles</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       placeholder="Name, display name, description..." 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Search
                </button>
                <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Roles List -->
<div class="bg-white shadow overflow-hidden sm:rounded-md">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">System Roles</h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">Manage user roles and their permissions</p>
    </div>

    <ul role="list" class="divide-y divide-gray-200">
        @forelse($roles as $role)
        <li>
            <div class="px-4 py-4 flex items-center justify-between">
                <div class="flex items-center min-w-0 flex-1">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                    </div>

                    <div class="ml-4 flex-1 min-w-0">
                        <div class="flex items-center">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $role->display_name }}</p>
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $role->name }}
                            </span>
                        </div>
                        @if($role->description)
                            <p class="text-sm text-gray-500 mt-1">{{ $role->description }}</p>
                        @endif
                        <div class="mt-2 flex items-center text-sm text-gray-500">
                            <span>{{ $role->users_count }} {{ Str::plural('user', $role->users_count) }}</span>
                            @php
                                $permissions = is_array($role->permissions) ? $role->permissions : [];
                                $permissionCount = count($permissions);
                            @endphp
                            @if($permissionCount > 0)
                                <span class="mx-2">•</span>
                                <span>{{ $permissionCount }} {{ Str::plural('permission', $permissionCount) }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="ml-4 flex-shrink-0 flex items-center space-x-2">
                    <!-- View Button -->
                    <a href="{{ route('admin.roles.show', $role) }}" 
                       class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                        View
                    </a>

                    <!-- Edit Button -->
                    <a href="{{ route('admin.roles.edit', $role) }}" 
                       class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-indigo-600 hover:bg-indigo-700">
                        Edit
                    </a>

                    <!-- Delete Button (not for admin role) -->
                    @if($role->name !== 'admin')
                    <button onclick="deleteRole({{ $role->id }})" 
                            class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700">
                        Delete
                    </button>
                    @endif
                </div>
            </div>
        </li>
        @empty
        <li class="px-4 py-8 text-center">
            <div class="text-sm text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <p class="mt-2">No roles found</p>
                <p class="mt-1">
                    <a href="{{ route('admin.roles.create') }}" class="text-indigo-600 hover:text-indigo-500">Create the first role</a>
                </p>
            </div>
        </li>
        @endforelse
    </ul>

    <!-- Pagination -->
    @if($roles->hasPages())
    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
        {{ $roles->links() }}
    </div>
    @endif
</div>

<!-- Permission Reference -->
<div class="mt-8 bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Available Permissions</h3>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($groupedPermissions as $group => $permissions)
            <div>
                <h4 class="text-sm font-medium text-gray-900 mb-2">{{ $group }}</h4>
                <ul class="space-y-1">
                    @foreach($permissions as $permission)
                    <li class="text-sm text-gray-600">
                        <code class="bg-gray-100 px-2 py-1 rounded text-xs">{{ $permission }}</code>
                        <span class="ml-2">{{ $availablePermissions[$permission] ?? str_replace('_', ' ', $permission) }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
function deleteRole(roleId) {
    if (!confirm('Are you sure you want to delete this role? This action cannot be undone.')) {
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