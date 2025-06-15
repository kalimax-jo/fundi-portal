@extends('layouts.admin')

@section('title', 'Inspector Management')

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Inspector Management
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                Total: {{ $inspectors->total() }} inspectors
            </div>
        </div>
    </div>
    <div class="mt-4 flex space-x-3 md:ml-4 md:mt-0">
        <a href="{{ route('admin.inspectors.assignments') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L7.53 10.53a.75.75 0 00-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
            </svg>
            View Assignments
        </a>
        <a href="{{ route('admin.inspectors.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
            </svg>
            Add Inspector
        </a>
    </div>
</div>
@endsection

@section('content')
<!-- Filters -->
<div class="bg-white shadow rounded-lg mb-6">
    <div class="px-4 py-5 sm:p-6">
        <form method="GET" action="{{ route('admin.inspectors.index') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-5">
            <!-- Search -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       placeholder="Name, email, code..." 
                       class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-sm shadow-sm placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>

            <!-- Availability Filter -->
            <div>
                <label for="availability" class="block text-sm font-medium text-gray-700">Availability</label>
                <select name="availability" id="availability" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-sm shadow-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    <option value="">All Statuses</option>
                    @foreach($availabilityStatuses as $status)
                        <option value="{{ $status }}" {{ request('availability') === $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Certification Filter -->
            <div>
                <label for="certification" class="block text-sm font-medium text-gray-700">Certification</label>
                <select name="certification" id="certification" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-sm shadow-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    <option value="">All Levels</option>
                    @foreach($certificationLevels as $level)
                        <option value="{{ $level }}" {{ request('certification') === $level ? 'selected' : '' }}>
                            {{ ucfirst($level) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Specialization Filter -->
            <div>
                <label for="specialization" class="block text-sm font-medium text-gray-700">Specialization</label>
                <select name="specialization" id="specialization" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-sm shadow-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    <option value="">All Specializations</option>
                    @foreach($specializations as $key => $value)
                        <option value="{{ $key }}" {{ request('specialization') === $key ? 'selected' : '' }}>
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Actions -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Filter
                </button>
                <a href="{{ route('admin.inspectors.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Inspectors Table -->
<div class="bg-white shadow overflow-hidden sm:rounded-md">
    <div class="px-4 py-5 sm:px-6">
        <div class="flex justify-between items-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Inspectors</h3>
            <div class="flex items-center space-x-2">
                <!-- Sort Options -->
                <select onchange="updateSort(this.value)" class="text-sm border-gray-300 rounded-md">
                    <option value="created_at-desc" {{ request('sort') === 'created_at' && request('direction') === 'desc' ? 'selected' : '' }}>Newest First</option>
                    <option value="created_at-asc" {{ request('sort') === 'created_at' && request('direction') === 'asc' ? 'selected' : '' }}>Oldest First</option>
                    <option value="name-asc" {{ request('sort') === 'name' && request('direction') === 'asc' ? 'selected' : '' }}>Name A-Z</option>
                    <option value="name-desc" {{ request('sort') === 'name' && request('direction') === 'desc' ? 'selected' : '' }}>Name Z-A</option>
                    <option value="rating-desc" {{ request('sort') === 'rating' && request('direction') === 'desc' ? 'selected' : '' }}>Highest Rated</option>
                </select>
            </div>
        </div>
    </div>

    <ul role="list" class="divide-y divide-gray-200">
        @forelse($inspectors as $inspector)
        <li>
            <div class="px-4 py-4 flex items-center justify-between">
                <div class="flex items-center min-w-0 flex-1">
                    <!-- Inspector Avatar -->
                    <div class="flex-shrink-0">
                        @if($inspector->user->profile_photo)
                            <img class="h-10 w-10 rounded-full" src="{{ Storage::url($inspector->user->profile_photo) }}" alt="{{ $inspector->user->full_name }}">
                        @else
                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Inspector Info -->
                    <div class="ml-4 flex-1 min-w-0">
                        <div class="flex items-center">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $inspector->user->full_name }}</p>
                            <!-- Inspector Code -->
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $inspector->inspector_code }}
                            </span>
                            <!-- Availability Status -->
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $inspector->availability_status === 'available' ? 'bg-green-100 text-green-800' : 
                                   ($inspector->availability_status === 'busy' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($inspector->availability_status) }}
                            </span>
                        </div>
                        <div class="flex items-center mt-1">
                            <p class="text-sm text-gray-500">{{ $inspector->user->email }}</p>
                            @if($inspector->user->phone)
                                <span class="mx-2 text-gray-300">•</span>
                                <p class="text-sm text-gray-500">{{ $inspector->user->phone }}</p>
                            @endif
                        </div>
                        <!-- Certification and Experience -->
                        <div class="mt-2 flex items-center space-x-4">
                            <div class="flex items-center">
                                <span class="text-xs text-gray-500">Certification:</span>
                                <span class="ml-1 inline-flex items-center px-2 py-1 rounded-md text-xs font-medium 
                                    {{ $inspector->certification_level === 'expert' ? 'bg-purple-100 text-purple-800' : 
                                       ($inspector->certification_level === 'advanced' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                                    {{ ucfirst($inspector->certification_level) }}
                                </span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-xs text-gray-500">Experience:</span>
                                <span class="ml-1 text-xs text-gray-700">{{ $inspector->experience_years }} years</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-xs text-gray-500">Rating:</span>
                                <div class="ml-1 flex items-center">
                                    <span class="text-xs text-gray-700">{{ number_format($inspector->rating, 1) }}</span>
                                    <svg class="ml-1 h-3 w-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <!-- Specializations -->
                        @if($inspector->specializations && count($inspector->specializations) > 0)
                        <div class="mt-2 flex flex-wrap gap-1">
                            @foreach(array_slice($inspector->specializations, 0, 3) as $specialization)
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-indigo-100 text-indigo-800">
                                    {{ $specializations[$specialization] ?? ucfirst(str_replace('_', ' ', $specialization)) }}
                                </span>
                            @endforeach
                            @if(count($inspector->specializations) > 3)
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                                    +{{ count($inspector->specializations) - 3 }} more
                                </span>
                            @endif
                        </div>
                        @endif
                        <p class="text-xs text-gray-500 mt-1">
                            Joined {{ $inspector->created_at->format('M j, Y') }} • 
                            {{ $inspector->total_inspections }} inspections completed
                        </p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="ml-4 flex-shrink-0 flex items-center space-x-2">
                    <!-- Availability Toggle -->
                    <button onclick="toggleAvailability({{ $inspector->id }})" 
                            class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white 
                            {{ $inspector->availability_status === 'available' ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }}">
                        {{ $inspector->availability_status === 'available' ? 'Set Offline' : 'Set Available' }}
                    </button>

                    <!-- View Button -->
                    <a href="{{ route('admin.inspectors.show', $inspector) }}" 
                       class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                        View
                    </a>

                    <!-- Edit Button -->
                    <a href="{{ route('admin.inspectors.edit', $inspector) }}" 
                       class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-indigo-600 hover:bg-indigo-700">
                        Edit
                    </a>

                    <!-- Delete Button -->
                    <button onclick="deleteInspector({{ $inspector->id }})" 
                            class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700">
                        Delete
                    </button>
                </div>
            </div>
        </li>
        @empty
        <li class="px-4 py-8 text-center">
            <div class="text-sm text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <p class="mt-2">No inspectors found</p>
                <p class="mt-1">
                    <a href="{{ route('admin.inspectors.create') }}" class="text-indigo-600 hover:text-indigo-500">Add the first inspector</a>
                </p>
            </div>
        </li>
        @endforelse
    </ul>

    <!-- Pagination -->
    @if($inspectors->hasPages())
    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
        {{ $inspectors->links() }}
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

function toggleAvailability(inspectorId) {
    if (!confirm('Are you sure you want to change this inspector\'s availability status?')) {
        return;
    }

    fetch(`/admin/inspectors/${inspectorId}/toggle-availability`, {
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
        alert('An error occurred while updating availability status.');
        console.error('Error:', error);
    });
}

function deleteInspector(inspectorId) {
    if (!confirm('Are you sure you want to delete this inspector? This action cannot be undone and will also delete their user account.')) {
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/inspectors/${inspectorId}`;
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