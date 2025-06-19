@props(['request', 'showActions' => false, 'showLink' => false, 'linkRoute' => null, 'role' => 'admin'])
<li>
    @if($showLink && $linkRoute)
        <a href="{{ route($linkRoute, $request) }}" class="block hover:bg-gray-50">
    @endif
    <div class="px-4 py-4 sm:px-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center min-w-0 flex-1">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                        <span class="text-sm font-medium text-gray-700">
                            {{ substr($request->request_number, -4) }}
                        </span>
                    </div>
                </div>
                <div class="ml-4 min-w-0 flex-1">
                    <div class="flex items-center">
                        <p class="text-sm font-medium text-indigo-600 truncate">
                            {{ $request->request_number }}
                        </p>
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $request->urgency === 'emergency' ? 'bg-red-100 text-red-800' : 
                               ($request->urgency === 'urgent' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst($request->urgency) }}
                        </span>
                    </div>
                    <div class="mt-1">
                        <p class="text-sm text-gray-900">
                            {{ $request->requester->full_name ?? ($request->client->name ?? '-') }}
                            @if($request->businessPartner)
                            <span class="text-gray-500">via {{ $request->businessPartner->name }}</span>
                            @endif
                        </p>
                        <p class="text-sm text-gray-500">
                            {{ $request->property->address ?? '-' }} • {{ $request->package->display_name ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex flex-col items-end">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    {{ $request->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                       ($request->status === 'assigned' ? 'bg-blue-100 text-blue-800' : 
                       ($request->status === 'in_progress' ? 'bg-indigo-100 text-indigo-800' : 
                       ($request->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'))) }}">
                    {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                </span>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $request->created_at->format('M j, Y') }}
                </p>
                @if($request->assignedInspector)
                <p class="text-xs text-gray-400">
                    {{ $request->assignedInspector->user->full_name }}
                </p>
                @endif
            </div>
        </div>
        @if($showActions)
        <div class="mt-2 flex space-x-2">
            @if($role === 'admin')
                <a href="{{ route('admin.inspection-requests.show', $request) }}" class="text-blue-600 hover:underline">View</a>
                <a href="{{ route('admin.inspection-requests.edit', $request) }}" class="text-yellow-600 hover:underline">Edit</a>
                <form action="{{ route('admin.inspection-requests.destroy', $request) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Delete this request?')">Delete</button>
                </form>
            @elseif($role === 'headtech')
                <a href="{{ route('headtech.inspection-requests.show', $request) }}" class="text-blue-600 hover:underline">View</a>
                <a href="{{ route('headtech.inspection-requests.edit', $request) }}" class="text-yellow-600 hover:underline">Edit</a>
                <form action="{{ route('headtech.inspection-requests.destroy', $request) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Delete this request?')">Delete</button>
                </form>
            @endif
        </div>
        @endif
    </div>
    @if($showLink && $linkRoute)
        </a>
    @endif
</li> 