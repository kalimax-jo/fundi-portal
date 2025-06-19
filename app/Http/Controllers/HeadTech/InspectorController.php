<?php

namespace App\Http\Controllers\HeadTech;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inspector;
use App\Models\User;

class InspectorController extends Controller
{
    public function index()
    {
        $inspectors = Inspector::with('user')->paginate(15);
        return view('headtech.inspectors.index', compact('inspectors'));
    }

    public function create()
    {
        return view('headtech.inspectors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'certification_level' => 'required|in:basic,advanced,expert',
        ]);
        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => bcrypt('password'), // Default password, should be changed
        ]);
        $inspector = Inspector::create([
            'user_id' => $user->id,
            'inspector_code' => 'INS' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            'certification_level' => $validated['certification_level'],
            'availability_status' => 'available',
        ]);
        return redirect()->route('headtech.inspectors.index')->with('success', 'Inspector created successfully.');
    }

    public function show(Inspector $inspector)
    {
        $inspector->load('user');
        return view('headtech.inspectors.show', compact('inspector'));
    }

    public function edit(Inspector $inspector)
    {
        $inspector->load('user');
        return view('headtech.inspectors.edit', compact('inspector'));
    }

    public function update(Request $request, Inspector $inspector)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $inspector->user_id,
            'phone' => 'required|string|max:20',
            'certification_level' => 'required|in:basic,advanced,expert',
        ]);
        $inspector->user->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ]);
        $inspector->update([
            'certification_level' => $validated['certification_level'],
        ]);
        return redirect()->route('headtech.inspectors.index')->with('success', 'Inspector updated successfully.');
    }

    public function destroy(Inspector $inspector)
    {
        $inspector->user->delete();
        $inspector->delete();
        return redirect()->route('headtech.inspectors.index')->with('success', 'Inspector deleted successfully.');
    }
} 