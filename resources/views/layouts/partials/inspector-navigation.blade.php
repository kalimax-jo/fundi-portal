<nav class="flex flex-1 flex-col">
    <ul role="list" class="flex flex-1 flex-col gap-y-7">
        <li>
            <ul role="list" class="-mx-2 space-y-1">
                <li>
                    <a href="{{ route('inspector.dashboard') }}" class="{{ request()->routeIs('inspector.dashboard') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }} group flex gap-x-3 rounded-md p-3 text-sm leading-6 font-semibold">
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('inspector.assignments') }}" class="{{ request()->routeIs('inspector.assignments') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }} group flex gap-x-3 rounded-md p-3 text-sm leading-6 font-semibold">
                        My Assignments
                    </a>
                </li>
                <li>
                    <a href="{{ route('inspector.pending') }}" class="{{ request()->routeIs('inspector.pending') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }} group flex gap-x-3 rounded-md p-3 text-sm leading-6 font-semibold">
                        Pending
                    </a>
                </li>
                 <li>
                    <a href="{{ route('inspector.inprogress') }}" class="{{ request()->routeIs('inspector.inprogress') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }} group flex gap-x-3 rounded-md p-3 text-sm leading-6 font-semibold">
                        In Progress
                    </a>
                </li>
                <li>
                    <a href="{{ route('inspector.complete') }}" class="{{ request()->routeIs('inspector.complete') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }} group flex gap-x-3 rounded-md p-3 text-sm leading-6 font-semibold">
                        Completed
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</nav> 