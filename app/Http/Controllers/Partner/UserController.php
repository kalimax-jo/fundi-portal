<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BusinessPartner;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Helpers\PartnerAccess;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $partner = $request->attributes->get('business_partner');
        $users = $partner->users()->get();
        return view('partner.users.index', compact('users', 'partner'));
    }

    public function create(Request $request)
    {
        $partner = $request->attributes->get('business_partner');
        if (!PartnerAccess::can('create_user', $partner)) {
            abort(403, 'You do not have permission to create users.');
        }
        return view('partner.users.create', compact('partner'));
    }

    public function store(Request $request)
    {
        $partner = $request->attributes->get('business_partner');
        if (!PartnerAccess::can('create_user', $partner)) {
            abort(403, 'You do not have permission to create users.');
        }
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'access_level' => 'required|in:admin,user,viewer',
            'password' => 'required|string|min:8|confirmed',
        ]);
        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'status' => 'active',
        ]);
        $partner->users()->attach($user->id, [
            'access_level' => $validated['access_level'],
            'is_primary_contact' => false,
        ]);
        return redirect()->route('partner.users.index')->with('success', 'User created successfully.');
    }

    public function edit(Request $request, User $user)
    {
        $partner = $request->attributes->get('business_partner');
        if (!PartnerAccess::can('edit_user', $partner)) {
            abort(403, 'You do not have permission to edit users.');
        }
        // Only allow editing if user belongs to this partner
        if (!$partner->users->contains($user->id)) {
            abort(403);
        }
        return view('partner.users.edit', compact('user', 'partner'));
    }

    public function update(Request $request, User $user)
    {
        $partner = $request->attributes->get('business_partner');
        if (!PartnerAccess::can('edit_user', $partner)) {
            abort(403, 'You do not have permission to update users.');
        }
        if (!$partner->users->contains($user->id)) {
            abort(403);
        }
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'access_level' => 'required|in:admin,user,viewer',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->phone = $validated['phone'];
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();
        // Update access level in pivot
        $partner->users()->updateExistingPivot($user->id, [
            'access_level' => $validated['access_level'],
        ]);
        return redirect()->route('partner.users.index')->with('success', 'User updated successfully.');
    }

    public function remove(Request $request, User $user)
    {
        $partner = $request->attributes->get('business_partner');
        if (!PartnerAccess::can('remove_user', $partner)) {
            abort(403, 'You do not have permission to remove users.');
        }
        if (!$partner->users->contains($user->id)) {
            abort(403);
        }
        $partner->users()->detach($user->id);
        // Optionally, delete the user if not attached to any other partner
        if ($user->businessPartners()->count() === 0) {
            $user->delete();
        }
        return redirect()->route('partner.users.index')->with('success', 'User deleted successfully.');
    }
} 