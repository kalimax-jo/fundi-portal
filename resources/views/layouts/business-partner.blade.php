<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @stack('styles')
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Sidebar (fixed) -->
    <aside class="fixed left-0 top-0 h-screen w-64 bg-indigo-600 shadow-lg flex flex-col justify-between z-30">
        <div>
            <div class="p-6 border-b border-indigo-700">
                <h2 class="text-xl font-bold text-white">{{ $partner->name ?? 'Partner Portal' }}</h2>
                <p class="text-xs text-indigo-100 mt-1">{{ $partner->subdomain ?? '' }}.fundi.info</p>
            </div>
            <nav class="mt-6 flex-1">
                <ul class="space-y-1 px-3">
                    <li>
                        <a href="{{ route('partner.dashboard') }}" class="flex items-center px-3 py-2 text-white hover:bg-indigo-700 rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('partner.users.index') }}" class="flex items-center px-3 py-2 text-white hover:bg-indigo-700 rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            Users
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('partner.users.create') }}" class="flex items-center px-3 py-2 text-white hover:bg-indigo-700 rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            Add User
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('business-partner.inspection-requests.index') }}" class="flex items-center px-3 py-2 text-white hover:bg-indigo-700 rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Inspection Requests
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('business-partner.properties.index') }}" class="flex items-center px-3 py-2 text-white hover:bg-indigo-700 rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            Properties
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('business-partner.reports.index') }}" class="flex items-center px-3 py-2 text-white hover:bg-indigo-700 rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Reports
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('partner.billing') }}" class="flex items-center px-3 py-2 text-white hover:bg-indigo-700 rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            Billing
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center px-3 py-2 text-white hover:bg-indigo-700 rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            Packages
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center px-3 py-2 text-white hover:bg-indigo-700 rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Services
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center px-3 py-2 text-white hover:bg-indigo-700 rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Profile
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <!-- System Status -->
        <div class="p-4 border-t border-indigo-700">
            <div class="rounded bg-indigo-700 p-3 flex items-center">
                <span class="h-3 w-3 rounded-full bg-green-400 mr-2"></span>
                <span class="text-xs text-white font-medium">System Status</span>
            </div>
            <div class="text-xs text-indigo-100 mt-1">All systems operational</div>
        </div>
    </aside>
    <!-- Main Content (with left margin for sidebar) -->
    <div class="ml-64 min-h-screen flex flex-col">
        <!-- Top Bar (fixed) -->
        <header class="fixed top-0 left-64 right-0 z-40 flex items-center justify-between bg-white h-16 px-6 border-b shadow-sm">
            <div class="flex items-center">
                <h1 class="text-lg font-semibold text-gray-800">Partner Portal</h1>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Notifications -->
                <button class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 15V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v4c0 .386-.149.735-.405 1.005L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </button>
                <!-- Profile Dropdown -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                        <div class="h-8 w-8 rounded-full bg-indigo-200 flex items-center justify-center text-indigo-800 font-bold">
                            {{ strtoupper(substr(auth()->user()->first_name ?? 'P', 0, 1)) }}
                        </div>
                        <span class="text-gray-700 font-medium">{{ auth()->user()->first_name ?? 'Partner' }}</span>
                        <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-50 py-2 border" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" style="display: none;">
                        <a href="#" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Profile
                        </a>
                        <a href="#" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Settings
                        </a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <form id="logout-form" action="{{ route('partner.logout') }}" method="POST">
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
        </header>
        <!-- Main Content Area -->
        <main class="flex-1 bg-white min-h-screen overflow-auto shadow-inner rounded-none pt-16">
            <div class="w-full h-full">
            @yield('content')
            </div>
        </main>
    </div>
</body>
@stack('scripts')
</html> 