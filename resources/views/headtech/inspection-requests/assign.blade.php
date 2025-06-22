@extends('layouts.headtech')

@section('title', 'Assign Inspectors')

@section('content')
<div class="py-8">
    <h1 class="text-2xl font-bold mb-6">Assign Inspectors</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <!-- Pending Requests -->
        <div>
            <h2 class="text-lg font-semibold mb-4">Pending Requests ({{ $pendingRequests->count() }})</h2>
            @if($pendingRequests->count())
                <ul class="divide-y divide-gray-200 bg-white rounded shadow">
                    @foreach($pendingRequests as $request)
                        <li class="p-4">
                            <div class="font-semibold text-indigo-700">{{ $request->request_number ?? 'Request #' . $request->id }}</div>
                            <div class="text-xs text-gray-500 mb-2">{{ $request->property->address ?? '-' }} | {{ ucfirst($request->urgency) }} | {{ $request->package->display_name ?? '-' }}</div>
                            <form action="{{ route('headtech.inspection-requests.assign', $request->id) }}" method="POST" class="flex flex-col md:flex-row gap-2 items-center">
                                @csrf
                                <select name="inspector_id" required class="rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    <option value="">Select Inspector</option>
                                    @foreach($availableInspectors as $inspector)
                                        <option value="{{ $inspector->id }}">{{ $inspector->user->full_name ?? 'Inspector '.$inspector->id }}</option>
                                    @endforeach
                                </select>
                                <input type="date" name="scheduled_date" required class="rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <input type="time" name="scheduled_time" required class="rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm">Assign</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-gray-400">No pending requests to assign.</div>
            @endif
        </div>
        <!-- Available Inspectors -->
        <div>
            <h2 class="text-lg font-semibold mb-4">Available Inspectors ({{ $availableInspectors->count() }})</h2>
            @if($availableInspectors->count())
                <ul class="divide-y divide-gray-200 bg-white rounded shadow">
                    @foreach($availableInspectors as $inspector)
                        <li class="p-4 flex items-center gap-4">
                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center font-bold text-gray-700">
                                {{ $inspector->user->initials ?? substr($inspector->user->first_name,0,1).substr($inspector->user->last_name,0,1) }}
                            </div>
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">{{ $inspector->user->full_name ?? 'Inspector '.$inspector->id }}</div>
                                <div class="text-xs text-gray-500">{{ ucfirst($inspector->certification_level) }} Level</div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-gray-400">No available inspectors.</div>
            @endif
        </div>
    </div>
</div>
@endsection