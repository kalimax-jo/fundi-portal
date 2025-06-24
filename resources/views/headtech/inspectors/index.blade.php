@extends('layouts.headtech')

@section('title', 'Inspectors')

@section('content')
    <div class="py-8">
        <h1 class="text-2xl font-bold mb-6">Inspectors</h1>
        <!-- Status Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-green-100 rounded shadow p-4 flex items-center">
                <div class="flex-shrink-0"><span class="inline-block w-8 h-8 bg-green-400 rounded-full flex items-center justify-center text-white font-bold">{{ $availableCount }}</span></div>
                <div class="ml-4">
                    <div class="text-xs text-gray-500">Available</div>
                    <div class="text-lg font-bold text-green-800">Inspectors</div>
                </div>
            </div>
            <div class="bg-yellow-100 rounded shadow p-4 flex items-center">
                <div class="flex-shrink-0"><span class="inline-block w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center text-white font-bold">{{ $busyCount }}</span></div>
                <div class="ml-4">
                    <div class="text-xs text-gray-500">Busy</div>
                    <div class="text-lg font-bold text-yellow-800">Inspectors</div>
                </div>
            </div>
            <div class="bg-gray-100 rounded shadow p-4 flex items-center">
                <div class="flex-shrink-0"><span class="inline-block w-8 h-8 bg-gray-400 rounded-full flex items-center justify-center text-white font-bold">{{ $offlineCount }}</span></div>
                <div class="ml-4">
                    <div class="text-xs text-gray-500">Offline</div>
                    <div class="text-lg font-bold text-gray-800">Inspectors</div>
                </div>
            </div>
            <div class="bg-indigo-100 rounded shadow p-4 flex items-center">
                <div class="flex-shrink-0"><span class="inline-block w-8 h-8 bg-indigo-400 rounded-full flex items-center justify-center text-white font-bold">{{ $totalCount }}</span></div>
                <div class="ml-4">
                    <div class="text-xs text-gray-500">Total</div>
                    <div class="text-lg font-bold text-indigo-800">Inspectors</div>
                </div>
            </div>
        </div>
        <!-- Add Inspector Button -->
        <div class="mb-4 flex justify-end">
            <a href="{{ route('headtech.inspectors.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Add Inspector</a>
        </div>
        <!-- Inspectors Table -->
        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                       <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Experience</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cert. Expiry</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Equipment</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Rating</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total Inspections</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($inspectors as $inspector)
                        <tr>
                            <td class="px-4 py-2">{{ $inspector->user->full_name ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $inspector->user->email ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $inspector->user->phone ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $inspector->inspector_code }}</td>
                           <td class="px-4 py-2">{{ $inspector->experience_years ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $inspector->certification_expiry ? $inspector->certification_expiry->format('Y-m-d') : '-' }}</td>
                            <td class="px-4 py-2">{{ is_array($inspector->equipment_assigned) ? implode(', ', $inspector->equipment_assigned) : ($inspector->equipment_assigned ?? '-') }}</td>
                            <td class="px-4 py-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                                    @if($inspector->availability_status === 'available') bg-green-100 text-green-800
                                    @elseif($inspector->availability_status === 'busy') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($inspector->availability_status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2">{{ $inspector->rating ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $inspector->total_inspections ?? '-' }}</td>
                            <td class="px-4 py-2">
                                <a href="{{ route('headtech.inspectors.show', $inspector->id) }}" class="text-blue-600 hover:underline text-xs">View</a>
                                <a href="{{ route('headtech.inspectors.edit', $inspector->id) }}" class="text-yellow-600 hover:underline text-xs ml-2">Edit</a>
                                <form action="{{ route('headtech.inspectors.destroy', $inspector->id) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline text-xs">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="13" class="px-4 py-2 text-center text-gray-400">No inspectors found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4">{{ $inspectors->links() }}</div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush 