<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Fundi Portal')</title>

    @vite('resources/css/app.css')

    @stack('styles')
</head>
<body class="h-full">
@auth
    @php $user = Auth::user(); @endphp
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="w-64 bg-indigo-800 text-white flex-shrink-0 hidden md:flex flex-col">
            <div class="flex-1 flex flex-col pt-6 px-4 overflow-y-auto">
                @if($user->isInspector())
                    <div class="mb-8 text-xl font-bold tracking-wide">Inspector Portal</div>
                    <nav class="flex-1 space-y-2">
                        <a href="{{ route('inspector.dashboard') }}" class="block px-3 py-2 rounded hover:bg-indigo-700 {{ request()->routeIs('inspector.dashboard') ? 'bg-indigo-900' : '' }}">Dashboard</a>
                        <a href="{{ route('inspector.assignments') }}" class="block px-3 py-2 rounded hover:bg-indigo-700 {{ request()->routeIs('inspector.assignments') ? 'bg-indigo-900' : '' }}">My Assignments</a>
                        <a href="{{ route('inspector.pending') }}" class="block px-3 py-2 rounded hover:bg-indigo-700 {{ request()->routeIs('inspector.pending') ? 'bg-indigo-900' : '' }}">Pending</a>
                        <a href="{{ route('inspector.inprogress') }}" class="block px-3 py-2 rounded hover:bg-indigo-700 {{ request()->routeIs('inspector.inprogress') ? 'bg-indigo-900' : '' }}">In Progress</a>
                        <a href="{{ route('inspector.complete') }}" class="block px-3 py-2 rounded hover:bg-indigo-700 {{ request()->routeIs('inspector.complete') ? 'bg-indigo-900' : '' }}">Completed</a>
                    </nav>
                @elseif($user->isHeadTechnician())
                    @include('layouts.partials.headtech-navigation')
                @elseif($user->isAdmin())
                     @include('layouts.partials.admin-navigation')
                @else
                    <div class="mb-8 text-xl font-bold tracking-wide">Client Portal</div>
                    <nav class="flex-1 space-y-2">
                        <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded hover:bg-indigo-700 {{ request()->routeIs('dashboard') ? 'bg-indigo-900' : '' }}">Dashboard</a>
                        <a href="{{ route('my-requests') }}" class="block px-3 py-2 rounded hover:bg-indigo-700 {{ request()->routeIs('my-requests') ? 'bg-indigo-900' : '' }}">My Requests</a>
                        <a href="{{ route('my-properties') }}" class="block px-3 py-2 rounded hover:bg-indigo-700 {{ request()->routeIs('my-properties') ? 'bg-indigo-900' : '' }}">My Properties</a>
                        <a href="{{ route('profile') }}" class="block px-3 py-2 rounded hover:bg-indigo-700 {{ request()->routeIs('profile') ? 'bg-indigo-900' : '' }}">Profile</a>
                    </nav>
                @endif
            </div>
        </aside>

        <!-- Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top bar -->
            <header class="bg-white shadow-sm p-4 flex justify-between items-center">
                <button class="md:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                </button>
                <div class="flex-1">@yield('page-header')</div>
                <div>
                    <span class="font-semibold">{{ $user->full_name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline ml-4">
                        @csrf
                        <button type="submit" class="text-sm text-gray-600 hover:underline">Logout</button>
                    </form>
                </div>
            </header>

            <!-- Main content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
            
            <footer class="bg-white border-t border-gray-200 py-4 text-center text-sm text-gray-500 flex-shrink-0">
                &copy; {{ date('Y') }} Fundi Portal. All rights reserved.
            </footer>
        </div>
    </div>
@else
    <!-- Layout for guest users -->
    <div class="flex flex-col min-h-screen">
        <main class="flex-grow">
            @yield('content')
        </main>
        <footer class="bg-white border-t border-gray-200 mt-auto">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} Fundi Portal. All rights reserved.
            </div>
        </footer>
    </div>
@endauth

@stack('scripts')
</body>
</html>