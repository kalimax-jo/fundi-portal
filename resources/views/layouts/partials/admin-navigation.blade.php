<nav class="flex flex-1 flex-col">
    <ul role="list" class="flex flex-1 flex-col gap-y-7">
        <li>
            <ul role="list" class="-mx-2 space-y-1">
                <!-- Dashboard -->
                <li>
                    <a href="{{ route('admin.dashboard') }}" 
                       class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}">
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                        Dashboard
                    </a>
                </li>

                <!-- User Management -->
                <li>
                    <div x-data="{ open: {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" 
                                class="group flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm leading-6 font-semibold {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                            User Management
                            <svg class="ml-auto h-5 w-5 shrink-0 transition-transform" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </button>
                        <ul x-show="open" x-transition class="mt-1 px-2 space-y-1">
                            <li>
                                <a href="{{ route('admin.users.index') }}" 
                                   class="group flex gap-x-3 rounded-md py-2 pl-6 pr-2 text-sm leading-6 {{ request()->routeIs('admin.users.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}">
                                    All Users
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.users.create') }}" 
                                   class="group flex gap-x-3 rounded-md py-2 pl-6 pr-2 text-sm leading-6 {{ request()->routeIs('admin.users.create') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}">
                                    Add User
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.roles.index') }}" 
                                   class="group flex gap-x-3 rounded-md py-2 pl-6 pr-2 text-sm leading-6 {{ request()->routeIs('admin.roles.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}">
                                    Roles & Permissions
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Inspector Management -->
                <li>
                    <div x-data="{ open: {{ request()->routeIs('admin.inspectors.*') || request()->routeIs('admin.assignments.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" 
                                class="group flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm leading-6 font-semibold {{ request()->routeIs('admin.inspectors.*') || request()->routeIs('admin.assignments.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Inspector Management
                            <svg class="ml-auto h-5 w-5 shrink-0 transition-transform" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </button>
                        <ul x-show="open" x-transition class="mt-1 px-2 space-y-1">
                            <li>
                                <a href="{{ route('admin.inspectors.index') }}" 
                                   class="group flex gap-x-3 rounded-md py-2 pl-6 pr-2 text-sm leading-6 {{ request()->routeIs('admin.inspectors.index') || request()->routeIs('admin.inspectors.show') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}">
                                    All Inspectors
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.inspectors.create') }}" 
                                   class="group flex gap-x-3 rounded-md py-2 pl-6 pr-2 text-sm leading-6 {{ request()->routeIs('admin.inspectors.create') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}">
                                    Add Inspector
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.inspectors.assignments') }}" 
                                   class="group flex gap-x-3 rounded-md py-2 pl-6 pr-2 text-sm leading-6 {{ request()->routeIs('admin.inspectors.assignments') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}">
                                    Assignments Overview
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Business Partners -->
                <li>
                    <div x-data="{ open: {{ request()->routeIs('admin.business-partners.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" 
                                class="group flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm leading-6 font-semibold {{ request()->routeIs('admin.business-partners.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15l-.75 18H5.25L4.5 3z" />
                            </svg>
                            Business Partners
                            <svg class="ml-auto h-5 w-5 shrink-0 transition-transform" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </button>
                        <ul x-show="open" x-transition class="mt-1 px-2 space-y-1">
                            <li>
                                <a href="{{ route('admin.business-partners.index') }}" 
                                   class="group flex gap-x-3 rounded-md py-2 pl-6 pr-2 text-sm leading-6 {{ request()->routeIs('admin.business-partners.index') || request()->routeIs('admin.business-partners.show') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}">
                                    All Partners
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.business-partners.create') }}" 
                                   class="group flex gap-x-3 rounded-md py-2 pl-6 pr-2 text-sm leading-6 {{ request()->routeIs('admin.business-partners.create') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}">
                                    Add Partner
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Inspection Requests -->
                <li>
                    <div x-data="{ open: {{ request()->routeIs('admin.inspection-requests.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" 
                                class="group flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm leading-6 font-semibold {{ request()->routeIs('admin.inspection-requests.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3-6h6m-6 3h6m-6 3h6m-12-9h6V6a3 3 0 013-3h6a3 3 0 013 3v15a3 3 0 01-3 3H6a3 3 0 01-3-3V9z" />
                            </svg>
                            Inspection Requests
                            <svg class="ml-auto h-5 w-5 shrink-0 transition-transform" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </button>
                        <ul x-show="open" x-transition class="mt-1 px-2 space-y-1">
                            <li>
                                <a href="{{ route('admin.inspection-requests.index') }}" 
                                   class="group flex gap-x-3 rounded-md py-2 pl-6 pr-2 text-sm leading-6 {{ request()->routeIs('admin.inspection-requests.index') || request()->routeIs('admin.inspection-requests.show') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}">
                                    All Requests
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.inspection-requests.pending') }}" 
                                   class="group flex gap-x-3 rounded-md py-2 pl-6 pr-2 text-sm leading-6 {{ request()->routeIs('admin.inspection-requests.pending') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}">
                                    Pending Requests
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.inspection-requests.assign') }}" 
                                   class="group flex gap-x-3 rounded-md py-2 pl-6 pr-2 text-sm leading-6 {{ request()->routeIs('admin.inspection-requests.assign') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}">
                                    Assign Inspectors
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Properties -->
                <li>
                    <a href="{{ route('admin.properties.index') }}" 
                       class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.properties.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}">
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                        Properties
                    </a>
                </li>

                <!-- Payments -->
                <li>
                    <a href="{{ route('admin.payments.index') }}" 
                       class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.payments.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}">
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                        </svg>
                        Payments
                    </a>
                </li>

                <!-- Packages & Services -->
                <li>
                    <div x-data="{ open: {{ request()->routeIs('admin.packages.*') || request()->routeIs('admin.services.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" 
                                class="group flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm leading-6 font-semibold {{ request()->routeIs('admin.packages.*') || request()->routeIs('admin.services.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                            </svg>
                            Packages & Services
                            <svg class="ml-auto h-5 w-5 shrink-0 transition-transform" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </button>
                        <ul x-show="open" x-transition class="mt-1 px-2 space-y-1">
                            <li>
                                <a href="{{ route('admin.packages.index') }}" 
                                   class="group flex gap-x-3 rounded-md py-2 pl-6 pr-2 text-sm leading-6 {{ request()->routeIs('admin.packages.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}">
                                    Inspection Packages
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.services.index') }}" 
                                   class="group flex gap-x-3 rounded-md py-2 pl-6 pr-2 text-sm leading-6 {{ request()->routeIs('admin.services.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}">
                                    Inspection Services
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.tiers.index') }}"
                                   class="group flex gap-x-3 rounded-md py-2 pl-6 pr-2 text-sm leading-6 {{ request()->routeIs('admin.tiers.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}">
                                    Tier Management
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Reports & Analytics -->
                <li>
                    <div x-data="{ open: {{ request()->routeIs('admin.reports.*') || request()->routeIs('admin.analytics.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" 
                                class="group flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm leading-6 font-semibold {{ request()->routeIs('admin.reports.*') || request()->routeIs('admin.analytics.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                            </svg>
                            Reports & Analytics
                            <svg class="ml-auto h-5 w-5 shrink-0 transition-transform" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </button>
                        <ul x-show="open" x-transition class="mt-1 px-2 space-y-1">
                            <li>
                                <a href="{{ route('admin.analytics.overview') }}" 
                                   class="group flex gap-x-3 rounded-md py-2 pl-6 pr-2 text-sm leading-6 {{ request()->routeIs('admin.analytics.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}">
                                    Analytics Dashboard
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.reports.index') }}" 
                                   class="group flex gap-x-3 rounded-md py-2 pl-6 pr-2 text-sm leading-6 {{ request()->routeIs('admin.reports.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}">
                                    Generate Reports
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Settings -->
                {{-- <a href="{{ route('admin.settings.index') }}">Settings</a> --}}
            </ul>
        </li>

        <!-- System Status -->
        <li class="mt-auto">
            <div class="bg-indigo-700 rounded-lg p-3">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                    </div>
                    <div class="ml-2">
                        <p class="text-xs font-medium text-white">System Status</p>
                        <p class="text-xs text-indigo-200">All systems operational</p>
                    </div>
                </div>
            </div>
        </li>
    </ul>
</nav>