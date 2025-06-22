<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Inspector Portal') - {{ config('app.name', 'Fundi Portal') }}</title>
    @vite('resources/css/app.css')
    @stack('styles')
</head>
<body class="h-full">
    <div x-data="{ mobileMenuOpen: false }">
        <!-- Off-canvas menu for mobile -->
        <div x-show="mobileMenuOpen" class="relative z-40 lg:hidden" x-ref="dialog" aria-modal="true">
            <div x-show="mobileMenuOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>
            <div class="fixed inset-0 z-40 flex">
                <div x-show="mobileMenuOpen" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="relative flex w-full max-w-xs flex-1 flex-col bg-indigo-600 pt-5 pb-4">
                    <div x-show="mobileMenuOpen" x-transition:enter="ease-in-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute top-0 right-0 -mr-12 pt-2">
                        <button type="button" class="ml-1 flex h-10 w-10 items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" @click="mobileMenuOpen = false">
                            <span class="sr-only">Close sidebar</span>
                            <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <div class="flex flex-shrink-0 items-center px-4">
                        <h1 class="text-white text-xl font-bold">Inspector Portal</h1>
                    </div>
                    <div class="mt-5 h-0 flex-1 overflow-y-auto px-2">
                        @include('layouts.partials.inspector-navigation')
                    </div>
                     <!-- Sidebar footer for mobile -->
                    <div class="flex-shrink-0 flex flex-col space-y-1 bg-indigo-700 p-2">
                        <a href="{{ route('inspector.settings') }}" class="flex-shrink-0 w-full group block rounded-md p-2">
                            <div class="flex items-center">
                                <div>
                                    <svg class="h-6 w-6 text-indigo-300 group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h14.25M3 9h9.75M3 13.5h5.25m5.25-.75L17.25 9m0 0L21 12.75M17.25 9v12" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-white">Settings</p>
                                </div>
                            </div>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button type="submit" class="w-full flex-shrink-0 group block rounded-md p-2 text-left">
                                <div class="flex items-center">
                                    <div>
                                        <svg class="h-6 w-6 text-indigo-300 group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m-3-3l-3 3m0 0l3 3m-3-3h12.75" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-white">Sign Out</p>
                                    </div>
                                </div>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="w-14 flex-shrink-0" aria-hidden="true"></div>
            </div>
        </div>

        <!-- Static sidebar for desktop -->
        <div class="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-72 lg:flex-col">
            <div class="flex min-h-0 flex-1 flex-col bg-indigo-600">
                <div class="flex h-16 flex-shrink-0 items-center bg-indigo-700 px-4">
                     <h1 class="text-white text-xl font-bold">Inspector Portal</h1>
                </div>
                <div class="flex flex-1 flex-col overflow-y-auto px-2 pt-4">
                     @include('layouts.partials.inspector-navigation')
                </div>
                <!-- Sidebar footer -->
                <div class="flex-shrink-0 flex flex-col space-y-1 bg-indigo-700 p-2">
                    <a href="{{ route('inspector.settings') }}" class="flex-shrink-0 w-full group block rounded-md p-2">
                        <div class="flex items-center">
                            <div>
                                <svg class="h-6 w-6 text-indigo-300 group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                     <path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h14.25M3 9h9.75M3 13.5h5.25m5.25-.75L17.25 9m0 0L21 12.75M17.25 9v12" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-white">Settings</p>
                            </div>
                        </div>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full flex-shrink-0 group block rounded-md p-2 text-left">
                            <div class="flex items-center">
                                <div>
                                    <svg class="h-6 w-6 text-indigo-300 group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m-3-3l-3 3m0 0l3 3m-3-3h12.75" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-white">Sign Out</p>
                                </div>
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex flex-1 flex-col lg:pl-72">
            <div class="sticky top-0 z-10 flex h-16 flex-shrink-0 bg-white shadow">
                <button type="button" class="border-r border-gray-200 px-4 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 lg:hidden" @click="mobileMenuOpen = true">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                </button>
                <div class="flex flex-1 justify-end px-4">
                    <div class="ml-4 flex items-center md:ml-6 space-x-4">
                        
                        <!-- Inspector ID and Availability Toggle -->
                        @if(Auth::user()->inspector)
                            <form action="{{ route('inspector.settings.availability.toggle') }}" method="POST" class="flex items-center space-x-4">
                                @csrf
                                <span class="text-sm font-medium text-gray-700">ID: {{ Auth::user()->inspector->inspector_code }}</span>
                                <span class="mr-2 text-sm font-medium text-gray-700">{{ Auth::user()->inspector->availability_status == 'available' ? 'Available' : 'Busy' }}</span>
                                <button type="submit" class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 {{ Auth::user()->inspector->availability_status == 'available' ? 'bg-green-500' : 'bg-gray-400' }}">
                                    <span class="sr-only">Toggle availability</span>
                                    <span aria-hidden="true" class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200 {{ Auth::user()->inspector->availability_status == 'available' ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                </button>
                            </form>
                        @endif

                        <!-- Profile dropdown -->
                        <div x-data="{ profileMenuOpen: false }" class="relative">
                            <div>
                                <button @click="profileMenuOpen = !profileMenuOpen" type="button" class="max-w-xs bg-white rounded-full flex items-center text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                    <span class="sr-only">Open user menu</span>
                                     <span class="hidden lg:flex lg:items-center">
                                        <span class="ml-2 text-sm font-semibold leading-6 text-gray-900">{{ auth()->user()->full_name }}</span>
                                        <svg class="ml-1 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg>
                                    </span>
                                </button>
                            </div>
                            <div x-show="profileMenuOpen" 
                                 x-transition:enter="transition ease-out duration-100" 
                                 x-transition:enter-start="transform opacity-0 scale-95" 
                                 x-transition:enter-end="transform opacity-100 scale-100" 
                                 x-transition:leave="transition ease-in duration-75" 
                                 x-transition:leave-start="transform opacity-100 scale-100" 
                                 x-transition:leave-end="transform opacity-0 scale-95" 
                                 @click.away="profileMenuOpen = false"
                                 class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                                <a href="{{ route('inspector.settings') }}" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1">Settings</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1">Sign out</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <main class="flex-1">
                <div class="py-6 h-full">
                    <div class="mx-auto max-w-7xl px-4 sm:px-6 md:px-8 h-full">
                        <div class="rounded-lg bg-white p-4 shadow sm:p-6 h-full">
                             @yield('content')
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('scripts')
</body>
</html> 