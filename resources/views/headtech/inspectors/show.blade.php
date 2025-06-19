@extends('layouts.headtech')

@section('title', 'Inspector Details')

@section('content')
<div class="py-8 max-w-xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Inspector Details</h1>
    <div class="bg-white p-6 rounded shadow">
        <p><strong>Name:</strong> {{ $inspector->user->full_name }}</p>
        <p><strong>Email:</strong> {{ $inspector->user->email }}</p>
        <p><strong>Phone:</strong> {{ $inspector->user->phone }}</p>
        <p><strong>Certification Level:</strong> {{ ucfirst($inspector->certification_level) }}</p>
        <p><strong>Status:</strong> {{ ucfirst($inspector->availability_status) }}</p>
        <a href="{{ route('headtech.inspectors.edit', $inspector) }}" class="mt-4 inline-block text-yellow-600 hover:underline">Edit</a>
        <a href="{{ route('headtech.inspectors.index') }}" class="mt-4 inline-block text-blue-600 hover:underline">Back to List</a>
    </div>
</div>
@endsection 