{{-- File Path: resources/views/admin/business-partners/index.blade.php --}}

@extends('layouts.admin')

@section('title', 'Business Partners')

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Business Partners
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <svg class="mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zM6 8a2 2 0 11-4 0 2 2 0 014 0zM1.49 15.326a.78.78 0 01-.358-.442 3 3 0 014.308-3.516 6.484 6.484 0 00-1.905 3.959c-.023.222-.014.442.025.654a4.97 4.97 0 01-2.07-.655zM16.44 15.98a4.97 4.97 0 002.07-.654.78.78 0 00.357-.442 3 3 0 00-4.308-3.517 6.484 6.484 0 011.907 3.96 2.32 2.32 0 01-.026.654z" />
                </svg>
                Manage your business partnerships and corporate clients
            </div>
        </div>
    </div>
    <div class="mt-4 flex space-x-3 md:ml-4 md:mt-0">
        <a href="{{ route('admin.business-partners.create') }}" 
           class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
            </svg>
            Add Partner
        </a>
    </div>
</div>
@endsection

@section('content')
<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    <!-- Total Partners -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15l-.75 18H5.25L4.5 3z" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Partners</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_partners']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Partners -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Active</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['active']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Inactive Partners -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Inactive</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['inactive']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Suspended Partners -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Suspended</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['suspended']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="bg-white shadow rounded-lg mb-6">
    <div class="px-4 py-5 sm:p-6">
        <form method="GET" class="space-y-4 sm:space-y-0 sm:grid sm:grid-cols-1 lg:grid-cols-5 sm:gap-4">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" name="search" id="search" 
                           class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" 
                           placeholder="Search by name, email, contact person..." 
                           value="{{ request('search') }}">
                </div>
            </div>

            <!-- Type Filter -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                <select name="type" id="type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">All Types</option>
                    @foreach($partnerTypes as $value => $label)
                        <option value="{{ $value }}" {{ request('type') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>

            <!-- Tier Filter -->
            <div>
                <label for="tier" class="block text-sm font-medium text-gray-700">Tier</label>
                <select name="tier" id="tier" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">All Tiers</option>
                    <option value="bronze" {{ request('tier') == 'bronze' ? 'selected' : '' }}>Bronze</option>
                    <option value="silver" {{ request('tier') == 'silver' ? 'selected' : '' }}>Silver</option>
                    <option value="gold" {{ request('tier') == 'gold' ? 'selected' : '' }}>Gold</option>
                    <option value="platinum" {{ request('tier') == 'platinum' ? 'selected' : '' }}>Platinum</option>
                </select>
            </div>

            <!-- Submit Button -->
            <div class="flex items-end">
                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                    Filter
                </button>
            </div>
        </form>

        @if(request()->hasAny(['search', 'type', 'status', 'tier']))
        <div class="mt-4 flex items-center justify-between">
            <p class="text-sm text-gray-500">
                Showing filtered results
            </p>
            <a href="{{ route('admin.business-partners.index') }}" 
               class="text-sm text-indigo-600 hover:text-indigo-500">
                Clear filters
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Partners Table -->
<div class="bg-white shadow overflow-hidden sm:rounded-md">
    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Business Partners ({{ $partners->total() }})
            </h3>
            <div class="flex items-center space-x-4">
                <!-- Sort Dropdown -->
                <div class="relative">
                    <select onchange="updateSort(this.value)" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="created_at-desc" {{ request('sort') == 'created_at' && request('direction') == 'desc' ? 'selected' : '' }}>
                            Newest First
                        </option>
                        <option value="created_at-asc" {{ request('sort') == 'created_at' && request('direction') == 'asc' ? 'selected' : '' }}>
                            Oldest First
                        </option>
                        <option value="name-asc" {{ request('sort') == 'name' && request('direction') == 'asc' ? 'selected' : '' }}>
                            Name A-Z
                        </option>
                        <option value="name-desc" {{ request('sort') == 'name' && request('direction') == 'desc' ? 'selected' : '' }}>
                            Name Z-A
                        </option>
                        <option value="total_inspections-desc" {{ request('sort') == 'total_inspections' && request('direction') == 'desc' ? 'selected' : '' }}>
                            Most Inspections
                        </option>
                        <option value="total_inspections-asc" {{ request('sort') == 'total_inspections' && request('direction') == 'asc' ? 'selected' : '' }}>
                            Least Inspections
                        </option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <ul role="list" class="divide-y divide-gray-200">
        @forelse($partners as $partner)
        <li>
            <div class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center min-w-0 flex-1">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full {{ $partner->partnership_status === 'active' ? 'bg-green-100' : ($partner->partnership_status === 'suspended' ? 'bg-red-100' : 'bg-yellow-100') }} flex items-center justify-center">
                                @if($partner->type === 'bank')
                                    <svg class="h-6 w-6 {{ $partner->partnership_status === 'active' ? 'text-green-600' : ($partner->partnership_status === 'suspended' ? 'text-red-600' : 'text-yellow-600') }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18m2.25-18v18M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.75m-.75 3h.75m-.75 3h.75m-3.75-16.5h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75" />
                                    </svg>
                                @elseif($partner->type === 'insurance')
                                    <svg class="h-6 w-6 {{ $partner->partnership_status === 'active' ? 'text-green-600' : ($partner->partnership_status === 'suspended' ? 'text-red-600' : 'text-yellow-600') }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                    </svg>
                                @else
                                    <svg class="h-6 w-6 {{ $partner->partnership_status === 'active' ? 'text-green-600' : ($partner->partnership_status === 'suspended' ? 'text-red-600' : 'text-yellow-600') }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15l-.75 18H5.25L4.5 3z" />
                                    </svg>
                                @endif
                            </div>
                        </div>
                        <div class="ml-4 flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-indigo-600 truncate">
                                        <a href="{{ route('admin.business-partners.show', $partner) }}" class="hover:text-indigo-500">
                                            {{ $partner->name }}
                                        </a>
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ $partnerTypes[$partner->type] ?? ucfirst($partner->type) }}
                                        @if($partner->tier)
                                            • <span class="capitalize font-medium text-gray-700">{{ $partner->tier }}</span> Tier
                                        @endif
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <!-- Partnership Status -->
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                        $partner->partnership_status === 'active' ? 'bg-green-100 text-green-800' : 
                                        ($partner->partnership_status === 'suspended' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') 
                                    }}">
                                        {{ ucfirst($partner->partnership_status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="mt-2 flex items-center text-sm text-gray-500">
                                <div class="flex items-center">
                                    <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                    {{ $partner->contact_email }}
                                </div>
                                @if($partner->contact_phone)
                                    <div class="ml-4 flex items-center">
                                        <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M2 3.5A1.5 1.5 0 013.5 2h1.148a1.5 1.5 0 011.465 1.175l.716 3.223a1.5 1.5 0 01-1.052 1.767l-.933.267c-.41.117-.643.555-.48.95a11.542 11.542 0 006.254 6.254c.395.163.833-.07.95-.48l.267-.933a1.5 1.5 0 011.767-1.052l3.223.716A1.5 1.5 0 0118 15.352V16.5a1.5 1.5 0 01-1.5 1.5H15c-1.149 0-2.263-.15-3.326-.43A13.022 13.022 0 012.43 8.326 13.019 13.019 0 012 5V3.5z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $partner->contact_phone }}
                                    </div>
                                @endif
                                <div class="ml-4 flex items-center">
                                    <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.75 2.75 0 0118 6.75v8.5A2.75 2.75 0 0115.25 18H4.75A2.75 2.75 0 012 15.25v-8.5A2.75 2.75 0 014.75 4H5V2.75A.75.75 0 015.75 2zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75z" clip-rule="evenodd" />
                                    </svg>
                                    Partner since {{ $partner->partnership_start_date->format('M Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex space-x-2">
                        <!-- Toggle Status Button -->
                        <button onclick="togglePartnerStatus({{ $partner->id }})" 
                                class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded {{ 
                                    $partner->partnership_status === 'active' ? 'text-yellow-700 bg-yellow-100 hover:bg-yellow-200' : 'text-green-700 bg-green-100 hover:bg-green-200'
                                }}">
                            {{ $partner->partnership_status === 'active' ? 'Deactivate' : 'Activate' }}
                        </button>

                        <!-- View Button -->
                        <a href="{{ route('admin.business-partners.show', $partner) }}" 
                           class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                            View
                        </a>

                        <!-- Edit Button -->
                        <a href="{{ route('admin.business-partners.edit', $partner) }}" 
                           class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-indigo-600 hover:bg-indigo-700">
                            Edit
                        </a>

                        <!-- Actions Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="inline-flex items-center px-2 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" 
                                 class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                <div class="py-1">
                                    <a href="{{ route('admin.business-partners.users', $partner->id) }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Manage Users
                                    </a>
                                    <button onclick="deletePartner({{ $partner->id }})" 
                                            class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                        Delete Partner
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        @empty
        <li class="px-4 py-8 text-center">
            <div class="text-sm text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 21h16.5M4.5 3h15l-.75 18H5.25L4.5 3z" />
                </svg>
                <p class="mt-2">No business partners found</p>
                <p class="mt-1">
                    @if(request()->hasAny(['search', 'type', 'status', 'tier']))
                        <a href="{{ route('admin.business-partners.index') }}" class="text-indigo-600 hover:text-indigo-500">Clear filters</a>
                        or
                    @endif
                    <a href="{{ route('admin.business-partners.create') }}" class="text-indigo-600 hover:text-indigo-500">add the first business partner</a>
                </p>
            </div>
        </li>
        @endforelse
    </ul>

    <!-- Pagination -->
    @if($partners->hasPages())
    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
        {{ $partners->links() }}
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

function togglePartnerStatus(partnerId) {
    if (!confirm('Are you sure you want to change this partner\'s status?')) {
        return;
    }

    fetch(`/admin/business-partners/${partnerId}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the partner status.');
    });
}

function deletePartner(partnerId) {
    if (!confirm('Are you sure you want to delete this business partner? This action cannot be undone.')) {
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/business-partners/${partnerId}`;
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
    
    const methodField = document.createElement('input');
    methodField.type = 'hidden';
    methodField.name = '_method';
    methodField.value = 'DELETE';
    
    form.appendChild(csrfToken);
    form.appendChild(methodField);
    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush

@endsection