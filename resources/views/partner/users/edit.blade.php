@extends('layouts.business-partner')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Edit User</h1>
    <form method="POST" action="{{ route('partner.users.update', $user) }}" class="bg-white shadow rounded-lg p-8 max-w-lg mx-auto space-y-6">
        @csrf
        @method('PUT')
        <div>
            <label for="first_name" class="block text-gray-700">First Name</label>
            <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
        </div>
        <div>
            <label for="last_name" class="block text-gray-700">Last Name</label>
            <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
        </div>
        <div>
            <label for="email" class="block text-gray-700">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500" required readonly>
        </div>
        <div>
            <label for="phone" class="block text-gray-700">Phone</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label for="access_level" class="block text-gray-700">Access Level</label>
            <select name="access_level" id="access_level" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="admin" @if(($user->pivot->access_level ?? '') == 'admin') selected @endif>Admin</option>
                <option value="user" @if(($user->pivot->access_level ?? '') == 'user') selected @endif>User</option>
                <option value="viewer" @if(($user->pivot->access_level ?? '') == 'viewer') selected @endif>Viewer</option>
            </select>
        </div>
        <div>
            <label for="password" class="block text-gray-700">New Password <span class="text-xs text-gray-400">(leave blank to keep current)</span></label>
            <input type="password" name="password" id="password" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label for="password_confirmation" class="block text-gray-700">Confirm New Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded transition duration-150">Update User</button>
        </div>
    </form>
@endsection 