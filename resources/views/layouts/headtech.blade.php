<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Head Technician Portal') - {{ config('app.name', 'Fundi Portal') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS CDN (temporary fix) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js - Load early to prevent conflicts -->
    <!-- Temporarily disabled to test refresh issue -->
    <!-- <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script> -->

    @stack('styles')
</head>
<body class="h-full">
    <div class="min-h-screen flex flex-col">
        <!-- Sidebar and main layout -->
        <div class="flex flex-1">
            <!-- Static sidebar for desktop -->
            <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
                <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-indigo-600 px-6">
                    <div class="flex h-16 shrink-0 items-center">
                        <h1 class="text-white text-xl font-bold">Head Technician Portal</h1>
                    </div>
                    @include('layouts.partials.headtech-navigation')
                </div>
            </div>
            <!-- Main content area with fixed header/footer -->
            <div class="flex-1 flex flex-col min-h-screen lg:pl-72">
                <!-- Fixed Header -->
                <header class="fixed top-0 left-0 right-0 z-40 h-16 flex items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8" style="margin-left: 18rem;">
                    <button type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden" @click="mobileMenuOpen = true">
                        <span class="sr-only">Open sidebar</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>

                    <!-- Separator -->
                    <div class="h-6 w-px bg-gray-200 lg:hidden" aria-hidden="true"></div>

                    <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                        <!-- Search (optional, can be removed) -->
                        <form class="relative flex flex-1" action="#" method="GET">
                            <label for="search-field" class="sr-only">Search</label>
                            <svg class="pointer-events-none absolute inset-y-0 left-0 h-full w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                            </svg>
                            <input id="search-field" class="block h-full w-full border-0 py-0 pl-8 pr-0 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm" placeholder="Search inspectors, requests..." type="search" name="search">
                        </form>

                        <div class="flex items-center gap-x-4 lg:gap-x-6">
                            <!-- Notifications bell -->
                            <div class="relative">
                                <button type="button" class="relative -m-2.5 p-2.5 text-gray-400 hover:text-gray-500">
                                    <span class="sr-only">View notifications</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                    </svg>
                                    @if($ht_notification_count > 0)
                                        <span class="absolute top-1 right-1 block h-2 w-2 rounded-full ring-2 ring-white bg-red-500"></span>
                                    @endif
                                </button>
                            </div>

                            <!-- Separator -->
                            <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-200" aria-hidden="true"></div>

                            <!-- Profile dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button type="button" class="-m-1.5 flex items-center p-1.5" @click="open = !open">
                                    <span class="sr-only">Open user menu</span>
                                    <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700">{{ auth()->user()->initials }}</span>
                                    </div>
                                    <span class="hidden lg:flex lg:items-center">
                                        <span class="ml-4 text-sm font-semibold leading-6 text-gray-900">{{ auth()->user()->full_name }}</span>
                                        <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </button>
                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-50 py-2 border" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" style="display: none;">
                                    <a href="{{ route('headtech.profile') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Profile
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
                <!-- Main Content (scrollable, with padding for header/footer) -->
                <main class="flex-1 overflow-auto pt-20 pb-16 px-4 sm:px-6 lg:px-8">
                    <!-- Page header -->
                    @hasSection('page-header')
                        <div class="mb-8">
                            @yield('page-header')
                        </div>
                    @endif
                    @yield('content')
                </main>
                <!-- Fixed Footer -->
                <footer class="fixed bottom-0 left-0 right-0 bg-gray-100 border-t py-4 px-4 text-center text-sm text-gray-500 z-50" style="margin-left: 18rem;">
                    &copy; {{ date('Y') }} Fundi Portal. All rights reserved.
                </footer>
            </div>
        </div>
    </div>
    @stack('scripts')
    <!-- Add Alpine.js CDN if not present -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html> 