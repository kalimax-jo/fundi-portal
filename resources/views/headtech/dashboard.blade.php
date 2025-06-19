@extends('layouts.headtech')

@section('title', 'Head Technician Dashboard')

@section('content')
<div class="py-8">
    <h1 class="text-2xl font-bold mb-4">Welcome, Head Technician!</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-lg font-semibold">Inspectors</h2>
            <a href="{{ route('headtech.inspectors.index') }}" class="text-indigo-600 hover:underline">Manage Inspectors</a>
        </div>
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-lg font-semibold">Inspection Requests</h2>
            <a href="{{ route('headtech.inspection-requests.index') }}" class="text-indigo-600 hover:underline">Manage Requests</a>
        </div>
    </div>
</div>
@endsection 