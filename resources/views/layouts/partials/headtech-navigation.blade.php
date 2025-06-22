<nav class="flex flex-1 flex-col">
    <ul role="list" class="flex flex-1 flex-col gap-y-7">
        <li>
            <ul role="list" class="-mx-2 space-y-1">
                <!-- Dashboard -->
                <li>
                    <a href="{{ route('headtech.dashboard') }}"
                       class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('headtech.dashboard') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}">
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                        Dashboard
                    </a>
                </li>
                <!-- Inspectors -->
                <li>
                    <a href="{{ route('headtech.inspectors.index') }}"
                       class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('headtech.inspectors.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}">
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                        Inspectors
                    </a>
                </li>
                <!-- Inspection Requests -->
                <li>
                    <div x-data="{ open: {{ request()->routeIs('headtech.inspection-requests.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                                class="group flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm leading-6 font-semibold {{ request()->routeIs('headtech.inspection-requests.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}">
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
                                <a href="{{ route('headtech.inspection-requests.index') }}"
                                   class="group flex gap-x-3 rounded-md py-2 pl-6 pr-2 text-sm leading-6 {{ request()->routeIs('headtech.inspection-requests.index') || request()->routeIs('headtech.inspection-requests.show') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}">
                                    All Requests
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('headtech.inspection-requests.index', ['status' => 'pending']) }}"
                                   class="group flex gap-x-3 rounded-md py-2 pl-6 pr-2 text-sm leading-6 {{ (request()->routeIs('headtech.inspection-requests.index') && request('status') === 'pending') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}">
                                    Pending Requests
                                </a>
                            </li>
                            
                            <li>
                                <a href="{{ route('headtech.assignments.index') }}"
                                   class="group flex gap-x-3 rounded-md py-2 pl-6 pr-2 text-sm leading-6 {{ request()->routeIs('headtech.assignments.index') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}">
                                    Assignments
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <!-- Packages -->
                <li>
                    <a href="{{ route('headtech.packages.index') }}"
                       class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('headtech.packages.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}">
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Packages
                    </a>
                </li>
                <!-- Services -->
                <li>
                    <a href="{{ route('headtech.services.index') }}"
                       class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('headtech.services.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}">
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        Services
                    </a>
                </li>
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