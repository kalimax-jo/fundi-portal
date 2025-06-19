@extends('layouts.headtech')

@section('title', 'Edit Inspector')

@section('content')
<div class="py-8 max-w-xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Edit Inspector</h1>
    <form method="POST" action="{{ route('headtech.inspectors.update', $inspector) }}">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block mb-1">First Name</label>
            <input type="text" name="first_name" class="w-full border rounded px-3 py-2" value="{{ old('first_name', $inspector->user->first_name) }}" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1">Last Name</label>
            <input type="text" name="last_name" class="w-full border rounded px-3 py-2" value="{{ old('last_name', $inspector->user->last_name) }}" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1">Email</label>
            <input type="email" name="email" class="w-full border rounded px-3 py-2" value="{{ old('email', $inspector->user->email) }}" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1">Phone</label>
            <input type="text" name="phone" class="w-full border rounded px-3 py-2" value="{{ old('phone', $inspector->user->phone) }}" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1">Certification Level</label>
            <select name="certification_level" class="w-full border rounded px-3 py-2" required>
                <option value="basic" @if($inspector->certification_level=='basic') selected @endif>Basic</option>
                <option value="advanced" @if($inspector->certification_level=='advanced') selected @endif>Advanced</option>
                <option value="expert" @if($inspector->certification_level=='expert') selected @endif>Expert</option>
            </select>
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Update Inspector</button>
    </form>
</div>
@endsection 