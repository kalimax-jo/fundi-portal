@extends('layouts.headtech')

@section('title', 'Inspectors')

@section('content')
<div class="py-8">
    <h1 class="text-2xl font-bold mb-4">Inspectors</h1>
    <a href="{{ route('headtech.inspectors.create') }}" class="mb-4 inline-block px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Add Inspector</a>
    <table class="min-w-full bg-white">
        <thead>
            <tr>
                <th class="px-4 py-2">Name</th>
                <th class="px-4 py-2">Email</th>
                <th class="px-4 py-2">Phone</th>
                <th class="px-4 py-2">Level</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inspectors as $inspector)
            <tr>
                <td class="border px-4 py-2">{{ $inspector->user->full_name }}</td>
                <td class="border px-4 py-2">{{ $inspector->user->email }}</td>
                <td class="border px-4 py-2">{{ $inspector->user->phone }}</td>
                <td class="border px-4 py-2">{{ ucfirst($inspector->certification_level) }}</td>
                <td class="border px-4 py-2">{{ ucfirst($inspector->availability_status) }}</td>
                <td class="border px-4 py-2">
                    <a href="{{ route('headtech.inspectors.show', $inspector) }}" class="text-blue-600 hover:underline">View</a> |
                    <a href="{{ route('headtech.inspectors.edit', $inspector) }}" class="text-yellow-600 hover:underline">Edit</a> |
                    <form action="{{ route('headtech.inspectors.destroy', $inspector) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Delete this inspector?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-4">{{ $inspectors->links() }}</div>
</div>
@endsection 