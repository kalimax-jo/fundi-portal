@extends('layouts.business-partner')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Add User</h1>
    <form method="POST" action="{{ route('partner.users.store') }}" class="bg-white shadow rounded-lg p-8 max-w-lg mx-auto space-y-6">
        @csrf
        <div>
            <label for="first_name" class="block text-gray-700">First Name</label>
            <input type="text" name="first_name" id="first_name" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
        </div>
        <div>
            <label for="last_name" class="block text-gray-700">Last Name</label>
            <input type="text" name="last_name" id="last_name" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
        </div>
        <div>
            <label for="email" class="block text-gray-700">Email</label>
            <input type="email" name="email" id="email" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
        </div>
        <div>
            <label for="phone" class="block text-gray-700">Phone</label>
            <input type="text" name="phone" id="phone" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label for="access_level" class="block text-gray-700">Access Level</label>
            <select name="access_level" id="access_level" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="admin">Admin</option>
                <option value="user">User</option>
                <option value="viewer">Viewer</option>
            </select>
        </div>
        <div>
            <label for="password" class="block text-gray-700">Password</label>
            <input type="password" name="password" id="password" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
        </div>
        <div>
            <label for="password_confirmation" class="block text-gray-700">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
        </div>
        <div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded transition duration-150">Create User</button>
        </div>
    </form>
@endsection 