@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="max-w-md mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Login</h1>
        @if($errors->any())
            <div class="mb-4 text-red-600">
                {{ $errors->first() }}
            </div>
        @endif
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="mt-1 block w-full border-gray-300 rounded" />
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input id="password" type="password" name="password" required class="mt-1 block w-full border-gray-300 rounded" />
            </div>
            <div class="flex items-center">
                <input id="remember" type="checkbox" name="remember" class="h-4 w-4 text-indigo-600 border-gray-300 rounded" />
                <label for="remember" class="ml-2 block text-sm text-gray-900">Remember me</label>
            </div>
            <div>
                <button type="submit" class="w-full py-2 px-4 bg-indigo-600 text-white rounded hover:bg-indigo-700">Login</button>
            </div>
        </form>
    </div>
@endsection
