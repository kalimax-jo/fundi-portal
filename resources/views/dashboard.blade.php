@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto py-10">
    <h1 class="text-2xl font-semibold mb-6">Welcome, {{ auth()->user()->full_name }}</h1>
    <div class="space-y-4">
        <a href="{{ route('inspection-requests.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-white shadow hover:bg-indigo-500">
            Request Inspection
        </a>
    </div>
</div>
@endsection
