@extends('layouts.admin')

@section('title', 'User Management')

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            User Management
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                Total: {{ $users->total() }} users
            </div>
        </div>
    </div>
    <div class="mt-4 flex md:ml-4 md:mt-0">
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
            </svg>
            Add User
        </a>
    </div>
</div>
@endsection

@section('content')
<!-- Filters -->
<div class="bg-white shadow rounded-lg mb-6">
    <div class="px-4 py-5 sm:p-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-4">
            <!-- Search -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       placeholder="Name, email, phone..." 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <!-- Role Filter -->
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                <select name="role" id="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                            {{ $role->display_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Actions -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Filter
                </button>
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="bg-white shadow overflow-hidden sm:rounded-md">
    <div class="px-4 py-5 sm:px-6">
        <div class="flex justify-between items-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Users</h3>
            <div class="flex items-center space-x-2">
                <!-- Sort Options -->
                <select onchange="updateSort(this.value)" class="text-sm border-gray-300 rounded-md">
                    <option value="created_at-desc" {{ request('sort') === 'created_at' && request('direction') === 'desc' ? 'selected' : '' }}>Newest First</option>
                    <option value="created_at-asc" {{ request('sort') === 'created_at' && request('direction') === 'asc' ? 'selected' : '' }}>Oldest First</option>
                    <option value="first_name-asc" {{ request('sort') === 'first_name' && request('direction') === 'asc' ? 'selected' : '' }}>Name A-Z</option>
                    <option value="first_name-desc" {{ request('sort') === 'first_name' && request('direction') === 'desc' ? 'selected' : '' }}>Name Z-A</option>
                    <option value="email-asc" {{ request('sort') === 'email' && request('direction') === 'asc' ? 'selected' : '' }}>Email A-Z</option>
                </select>
            </div>
        </div>
    </div>

    <ul role="list" class="divide-y divide-gray-200">
        @forelse($users as $user)
        <li>
            <div class="px-4 py-4 flex items-center justify-between">
                <div class="flex items-center min-w-0 flex-1">
                    <!-- User Avatar -->
                    <div class="flex-shrink-0">
                        @if($user->profile_photo)
                            <img class="h-10 w-10 rounded-full" src="{{ Storage::url($user->profile_photo) }}" alt="{{ $user->full_name }}">
                        @else
                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                <span class="text-sm font-medium text-gray-700">{{ $user->initials }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- User Info -->
                    <div class="ml-4 flex-1 min-w-0">
                        <div class="flex items-center">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $user->full_name }}</p>
                            <!-- Status Badge -->
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 
                                   ($user->status === 'suspended' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </div>
                        <div class="flex items-center mt-1">
                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            @if($user->phone)
                                <span class="mx-2 text-gray-300">•</span>
                                <p class="text-sm text-gray-500">{{ $user->phone }}</p>
                            @endif
                        </div>
                        <!-- Roles -->
                        <div class="mt-2 flex flex-wrap gap-1">
                            @foreach($user->roles as $role)
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $role->display_name }}
                                </span>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            Joined {{ $user->created_at->format('M j, Y') }} • 
                            Last login {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                        </p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="ml-4 flex-shrink-0 flex items-center space-x-2">
                    <!-- Status Toggle -->
                    <button onclick="toggleUserStatus({{ $user->id }})" 
                            class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white 
                            {{ $user->status === 'active' ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }}">
                        {{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}
                    </button>

                    <!-- View Button -->
                    <a href="{{ route('admin.users.show', $user) }}" 
                       class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                        View
                    </a>

                    <!-- Edit Button -->
                    <a href="{{ route('admin.users.edit', $user) }}" 
                       class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-indigo-600 hover:bg-indigo-700">
                        Edit
                    </a>

                    <!-- Delete Button -->
                    @if($user->id !== auth()->id())
                    <button onclick="deleteUser({{ $user->id }})" 
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <p class="mt-2">No users found</p>
                <p class="mt-1">
                    <a href="{{ route('admin.users.create') }}" class="text-indigo-600 hover:text-indigo-500">Add the first user</a>
                </p>
            </div>
        </li>
        @endforelse
    </ul>

    <!-- Pagination -->
    @if($users->hasPages())
    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
        {{ $users->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
function updateSort(value) {
    const [sort, direction] = value.split('-');
    const url = new URL(window.location);
    url.searchParams.set('sort', sort);
    url.searchParams.set('direction', direction);
    window.location = url;
}

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