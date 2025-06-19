@extends('layouts.headtech')

@section('title', 'Add Inspector')

@section('content')
<div class="py-8 max-w-xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Add Inspector</h1>
    <form method="POST" action="{{ route('headtech.inspectors.store') }}">
        @csrf
        <div class="mb-4">
            <label class="block mb-1">First Name</label>
            <input type="text" name="first_name" class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1">Last Name</label>
            <input type="text" name="last_name" class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1">Email</label>
            <input type="email" name="email" class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1">Phone</label>
            <input type="text" name="phone" class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1">Certification Level</label>
            <select name="certification_level" class="w-full border rounded px-3 py-2" required>
                <option value="basic">Basic</option>
                <option value="advanced">Advanced</option>
                <option value="expert">Expert</option>
            </select>
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Create Inspector</button>
    </form>
</div>
@endsection 