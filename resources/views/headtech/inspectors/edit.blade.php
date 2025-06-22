@extends('layouts.headtech')

@section('title', 'Edit Inspector')

@section('content')
<div class="py-8 max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Edit Inspector</h1>
    <form action="{{ route('headtech.inspectors.update', $inspector->id) }}" method="POST" class="bg-white rounded shadow p-6 space-y-4">
        @csrf
        @method('PATCH')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs text-gray-500 mb-1">First Name</label>
                <input type="text" name="first_name" value="{{ old('first_name', $inspector->user->first_name ?? '') }}" required class="w-full rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Last Name</label>
                <input type="text" name="last_name" value="{{ old('last_name', $inspector->user->last_name ?? '') }}" required class="w-full rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $inspector->user->email ?? '') }}" required class="w-full rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $inspector->user->phone ?? '') }}" required class="w-full rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Certification Level</label>
                <select name="certification_level" required class="w-full rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <option value="basic" @if(old('certification_level', $inspector->certification_level) == 'basic') selected @endif>Basic</option>
                    <option value="advanced" @if(old('certification_level', $inspector->certification_level) == 'advanced') selected @endif>Advanced</option>
                    <option value="expert" @if(old('certification_level', $inspector->certification_level) == 'expert') selected @endif>Expert</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Experience Years</label>
                <input type="number" name="experience_years" min="0" value="{{ old('experience_years', $inspector->experience_years) }}" class="w-full rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Certification Expiry</label>
                <input type="date" name="certification_expiry" value="{{ old('certification_expiry', $inspector->certification_expiry ? $inspector->certification_expiry->format('Y-m-d') : '') }}" class="w-full rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Specializations <span class="text-gray-400">(comma separated)</span></label>
            <input type="text" name="specializations" value="{{ old('specializations', is_array($inspector->specializations) ? implode(', ', $inspector->specializations) : $inspector->specializations) }}" class="w-full rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="e.g. plumbing, electrical">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Equipment Assigned <span class="text-gray-400">(comma separated)</span></label>
            <input type="text" name="equipment_assigned" value="{{ old('equipment_assigned', is_array($inspector->equipment_assigned) ? implode(', ', $inspector->equipment_assigned) : $inspector->equipment_assigned) }}" class="w-full rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="e.g. ladder, multimeter">
        </div>
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Reset Password <span class="text-gray-400 text-xs">(leave blank to keep current password)</span></label>
            <input type="password" name="password" id="password" class="w-full border rounded px-3 py-2" minlength="8">
        </div>
        <div class="flex justify-end gap-2">
            <a href="{{ route('headtech.inspectors.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm">Update Inspector</button>
        </div>
    </form>
</div>
@endsection 