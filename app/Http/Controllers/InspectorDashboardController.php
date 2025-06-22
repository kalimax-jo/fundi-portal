<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Inspector;
use App\Models\InspectionRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class InspectorDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $inspector = Inspector::where('user_id', $user->id)->first();
        $assignedRequests = [];
        $inProgressRequests = [];
        if ($inspector) {
            $assignedRequests = $inspector->inspectionRequests()->where('status', 'assigned')->orderByDesc('assigned_at')->get();
            $inProgressRequests = $inspector->inspectionRequests()->where('status', 'in_progress')->orderByDesc('started_at')->get();
        }
        return view('inspectors.dashboard', compact('inspector', 'assignedRequests', 'inProgressRequests'));
    }

    public function assignments(Request $request)
    {
        $user = \Auth::user();
        $inspector = \App\Models\Inspector::where('user_id', $user->id)->first();
        $allAssignments = collect();
        if ($inspector) {
            $query = $inspector->inspectionRequests()
                ->with(['requester', 'property', 'package'])
                ->whereIn('status', ['assigned', 'in_progress'])
                ->orderByDesc('assigned_at');
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
                    $q->where('request_number', 'like', "%$search%")
                      ->orWhereHas('property', function($q2) use ($search) {
                          $q2->where('address', 'like', "%$search%")
                             ->orWhere('property_code', 'like', "%$search%")
                             ->orWhere('property_type', 'like', "%$search%")
                             ;
                      })
                      ->orWhereHas('package', function($q2) use ($search) {
                          $q2->where('display_name', 'like', "%$search%")
                             ->orWhere('name', 'like', "%$search%")
                             ;
                      });
                });
            }
            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }
            if ($request->filled('urgency')) {
                $query->where('urgency', $request->input('urgency'));
            }
            $allAssignments = $query->paginate(10)->withQueryString();
        }
        return view('inspectors.assignments', compact('inspector', 'allAssignments'));
    }

    public function pending()
    {
        $user = \Auth::user();
        $inspector = \App\Models\Inspector::where('user_id', $user->id)->first();
        $pendingRequests = collect();
        if ($inspector) {
            $pendingRequests = $inspector->inspectionRequests()->where('status', 'assigned')->orderByDesc('assigned_at')->get();
        }
        return view('inspectors.pending', compact('inspector', 'pendingRequests'));
    }

    public function inprogress()
    {
        $user = \Auth::user();
        $inspector = \App\Models\Inspector::where('user_id', $user->id)->first();
        $inProgressRequests = collect();
        if ($inspector) {
            $inProgressRequests = $inspector->inspectionRequests()->where('status', 'in_progress')->orderByDesc('started_at')->get();
        }
        return view('inspectors.inprogress', compact('inspector', 'inProgressRequests'));
    }

    public function complete()
    {
        $user = \Auth::user();
        $inspector = \App\Models\Inspector::where('user_id', $user->id)->first();
        $completedRequests = collect();
        if ($inspector) {
            $completedRequests = $inspector->inspectionRequests()->where('status', 'completed')->orderByDesc('completed_at')->get();
        }
        return view('inspectors.complete', compact('inspector', 'completedRequests'));
    }

    public function show($id)
    {
        $user = \Auth::user();
        $inspector = \App\Models\Inspector::where('user_id', $user->id)->first();
        $request = null;
        if ($inspector) {
            $request = $inspector->inspectionRequests()->where('id', $id)->firstOrFail();
        }
        return view('inspectors.show', compact('inspector', 'request'));
    }

    public function startInspection($id)
    {
        $user = \Auth::user();
        $inspector = \App\Models\Inspector::where('user_id', $user->id)->first();

        if (!$inspector) {
            return redirect()->back()->with('error', 'Inspector profile not found.');
        }

        $request = $inspector->inspectionRequests()
            ->where('id', $id)
            ->where('status', 'assigned')
            ->first();

        if (!$request) {
            return redirect()->back()->with('error', 'Request not found or not eligible to start.');
        }

        // Update the request status
        $request->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return redirect()->route('inspector.requests.report', $request->id)
            ->with('success', 'Inspection has been started. You can now fill out the report.');
    }

    public function settings()
    {
        $user = Auth::user();
        $inspector = $user->inspector;
        return view('inspectors.settings', compact('user', 'inspector'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
        ]);

        $user->update($request->only('full_name', 'email', 'phone_number'));

        return redirect()->route('inspector.settings')->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('inspector.settings')->with('success', 'Password updated successfully.');
    }

    public function toggleAvailability()
    {
        $inspector = Auth::user()->inspector;
        if ($inspector) {
            // Simple toggle: available -> busy -> available
            $currentStatus = $inspector->availability_status;
            if ($currentStatus == 'available') {
                $inspector->availability_status = 'busy';
            } else {
                $inspector->availability_status = 'available';
            }
            $inspector->save();
            return redirect()->route('inspector.settings')->with('success', 'Availability status updated.');
        }
        return redirect()->route('inspector.settings')->with('error', 'Inspector profile not found.');
    }
} 