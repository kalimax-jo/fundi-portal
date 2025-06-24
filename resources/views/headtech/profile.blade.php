@extends('layouts.headtech')

@section('title', 'Profile Settings')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Profile Settings</h1>
    @if(session('success'))
        <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
    @endif
    <form method="POST" action="{{ route('headtech.profile.update') }}" class="space-y-4 bg-white p-6 rounded shadow">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700">First Name</label>
            <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" class="mt-1 block w-full border-gray-300 rounded px-3 py-2" required>
            @error('first_name') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Last Name</label>
            <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" class="mt-1 block w-full border-gray-300 rounded px-3 py-2" required>
            @error('last_name') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="mt-1 block w-full border-gray-300 rounded px-3 py-2" required>
            @error('email') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="mt-1 block w-full border-gray-300 rounded px-3 py-2">
            @error('phone') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
        </div>
        <div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Update Profile</button>
        </div>
    </form>

    <h2 class="text-xl font-semibold mt-10 mb-4">Change Password</h2>
    <form method="POST" action="{{ route('headtech.password.update') }}" class="space-y-4 bg-white p-6 rounded shadow">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700">Current Password</label>
            <input type="password" name="current_password" class="mt-1 block w-full border-gray-300 rounded px-3 py-2" required>
            @error('current_password') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">New Password</label>
            <input type="password" name="new_password" class="mt-1 block w-full border-gray-300 rounded px-3 py-2" required>
            @error('new_password') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Confirm New Password</label>
            <input type="password" name="new_password_confirmation" class="mt-1 block w-full border-gray-300 rounded px-3 py-2" required>
        </div>
        <div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Change Password</button>
        </div>
    </form>
</div>
@endsection 