@extends('layouts.admin')

@section('title', 'Inspection Requests Report')

@section('page-header')
    <h1 class="text-2xl font-bold">Inspection Requests Report</h1>
@endsection

@section('content')
    <div class="mb-6">
        <form method="GET" class="flex flex-wrap gap-4">
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-input" placeholder="Start Date">
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-input" placeholder="End Date">
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                <option value="assigned" {{ request('status')=='assigned'?'selected':'' }}>Assigned</option>
                <option value="in_progress" {{ request('status')=='in_progress'?'selected':'' }}>In Progress</option>
                <option value="completed" {{ request('status')=='completed'?'selected':'' }}>Completed</option>
                <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Cancelled</option>
            </select>
            <select name="package_id" class="form-select">
                <option value="">All Packages</option>
                @foreach($packages as $package)
                    <option value="{{ $package->id }}" {{ request('package_id')==$package->id?'selected':'' }}>{{ $package->display_name }}</option>
                @endforeach
            </select>
            <select name="inspector_id" class="form-select">
                <option value="">All Inspectors</option>
                @foreach($inspectors as $inspector)
                    <option value="{{ $inspector->id }}" {{ request('inspector_id')==$inspector->id?'selected':'' }}>
                        {{ $inspector->user->full_name ?? 'Inspector #'.$inspector->id }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>

    <div class="mb-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded shadow">
                <div class="text-gray-500">Total</div>
                <div class="text-xl font-bold">{{ $stats['total'] }}</div>
            </div>
            <div class="bg-white p-4 rounded shadow">
                <div class="text-gray-500">Completed</div>
                <div class="text-xl font-bold">{{ $stats['completed'] }}</div>
            </div>
            <div class="bg-white p-4 rounded shadow">
                <div class="text-gray-500">Pending</div>
                <div class="text-xl font-bold">{{ $stats['pending'] }}</div>
            </div>
            <div class="bg-white p-4 rounded shadow">
                <div class="text-gray-500">Cancelled</div>
                <div class="text-xl font-bold">{{ $stats['cancelled'] }}</div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2">#</th>
                    <th class="px-4 py-2">Date</th>
                    <th class="px-4 py-2">Request #</th>
                    <th class="px-4 py-2">Package</th>
                    <th class="px-4 py-2">Inspector</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Total Cost</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requests as $req)
                <tr>
                    <td class="px-4 py-2">{{ ($requests->currentPage() - 1) * $requests->perPage() + $loop->iteration }}</td>
                    <td class="px-4 py-2">{{ $req->created_at->format('Y-m-d') }}</td>
                    <td class="px-4 py-2">{{ $req->request_number }}</td>
                    <td class="px-4 py-2">{{ $req->package->display_name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $req->assignedInspector->user->full_name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ ucfirst(str_replace('_',' ',$req->status)) }}</td>
                    <td class="px-4 py-2">{{ number_format($req->total_cost, 0) }} RWF</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">
            {{ $requests->links() }}
        </div>
    </div>
@endsection 