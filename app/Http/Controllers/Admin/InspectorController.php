<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inspector;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InspectorController extends Controller
{
    /**
     * Display a listing of inspectors
     */
    public function index(Request $request)
    {
        $query = Inspector::with(['user', 'user.roles']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('inspector_code', 'like', "%{$search}%");
        }

        // Filter by availability status
        if ($request->has('availability') && $request->availability) {
            $query->where('availability_status', $request->availability);
        }

        // Filter by certification level
        if ($request->has('certification') && $request->certification) {
            $query->where('certification_level', $request->certification);
        }

        // Filter by specialization
        if ($request->has('specialization') && $request->specialization) {
            $query->whereJsonContains('specializations', $request->specialization);
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        if ($sortBy === 'name') {
            $query->join('users', 'inspectors.user_id', '=', 'users.id')
                  ->orderBy('users.first_name', $sortDirection);
        } else {
            $query->orderBy($sortBy, $sortDirection);
        }

        $inspectors = $query->paginate(15)->withQueryString();

        // Get filter options
        $availabilityStatuses = ['available', 'busy', 'offline'];
        $certificationLevels = ['basic', 'advanced', 'expert'];
        $specializations = $this->getAvailableSpecializations();

        return view('admin.inspectors.index', compact(
            'inspectors', 
            'availabilityStatuses', 
            'certificationLevels', 
            'specializations'
        ));
    }

    /**
     * Show the form for creating a new inspector
     */
    public function create()
    {
        $specializations = $this->getAvailableSpecializations();
        $certificationLevels = ['basic', 'advanced', 'expert'];
        $availabilityStatuses = ['available', 'busy', 'offline'];
        $equipmentOptions = $this->getEquipmentOptions();
        
        return view('admin.inspectors.create', compact(
            'specializations', 
            'certificationLevels', 
            'availabilityStatuses', 
            'equipmentOptions'
        ));
    }

    /**
     * Store a newly created inspector
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // User fields
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:8|confirmed',
            
            // Inspector fields
            'certification_level' => 'required|in:basic,advanced,expert',
            'specializations' => 'nullable|array',
            'specializations.*' => 'string',
            'experience_years' => 'required|integer|min:0|max:50',
            'certification_expiry' => 'required|date|after:today',
            'equipment_assigned' => 'nullable|array',
            'equipment_assigned.*' => 'string',
            'availability_status' => 'required|in:available,busy,offline',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Create user first
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'status' => 'active',
            ]);

            // Assign inspector role
            $inspectorRole = Role::where('name', 'inspector')->first();
            if ($inspectorRole) {
                $user->roles()->attach($inspectorRole->id, [
                    'assigned_at' => now(),
                    'assigned_by' => auth()->id(),
                ]);
            }

            // Generate unique inspector code
            $inspectorCode = $this->generateInspectorCode();

            // Create inspector profile
            $inspector = Inspector::create([
                'user_id' => $user->id,
                'inspector_code' => $inspectorCode,
                'certification_level' => $request->certification_level,
                'specializations' => $request->specializations ?? [],
                'experience_years' => $request->experience_years,
                'certification_expiry' => $request->certification_expiry,
                'equipment_assigned' => $request->equipment_assigned ?? [],
                'availability_status' => $request->availability_status,
                'rating' => 0.00,
                'total_inspections' => 0,
            ]);

            DB::commit();

            return redirect()->route('admin.inspectors.index')
                ->with('success', 'Inspector created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create inspector: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified inspector
     */
    public function show(Inspector $inspector)
    {
        $inspector->load(['user', 'certifications']);

        // Get inspector statistics
        $stats = [
            'total_inspections' => $inspector->total_inspections,
            'completed_this_month' => 0, // Will implement when we have inspection requests
            'average_rating' => $inspector->rating,
            'certification_expires' => $inspector->certification_expiry,
            'days_until_expiry' => $inspector->certification_expiry ? 
                Carbon::parse($inspector->certification_expiry)->diffInDays(now()) : 0,
            'account_age' => $inspector->created_at->diffForHumans(),
            'last_login' => $inspector->user->last_login_at,
        ];

        return view('admin.inspectors.show', compact('inspector', 'stats'));
    }

    /**
     * Show the form for editing the inspector
     */
    public function edit(Inspector $inspector)
    {
        $inspector->load('user');
        $specializations = $this->getAvailableSpecializations();
        $certificationLevels = ['basic', 'advanced', 'expert'];
        $availabilityStatuses = ['available', 'busy', 'offline'];
        $equipmentOptions = $this->getEquipmentOptions();
        
        return view('admin.inspectors.edit', compact(
            'inspector',
            'specializations', 
            'certificationLevels', 
            'availabilityStatuses', 
            'equipmentOptions'
        ));
    }

    /**
     * Update the specified inspector
     */
    public function update(Request $request, Inspector $inspector)
    {
        $validator = Validator::make($request->all(), [
            // User fields
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:users,email,' . $inspector->user_id,
            'phone' => 'required|string|max:20|unique:users,phone,' . $inspector->user_id,
            'password' => 'nullable|string|min:8|confirmed',
            
            // Inspector fields
            'certification_level' => 'required|in:basic,advanced,expert',
            'specializations' => 'nullable|array',
            'specializations.*' => 'string',
            'experience_years' => 'required|integer|min:0|max:50',
            'certification_expiry' => 'required|date|after:today',
            'equipment_assigned' => 'nullable|array',
            'equipment_assigned.*' => 'string',
            'availability_status' => 'required|in:available,busy,offline',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Update user data
            $userData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
            ];

            // Update password if provided
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $inspector->user->update($userData);

            // Update inspector data
            $inspector->update([
                'certification_level' => $request->certification_level,
                'specializations' => $request->specializations ?? [],
                'experience_years' => $request->experience_years,
                'certification_expiry' => $request->certification_expiry,
                'equipment_assigned' => $request->equipment_assigned ?? [],
                'availability_status' => $request->availability_status,
            ]);

            DB::commit();

            return redirect()->route('admin.inspectors.show', $inspector)
                ->with('success', 'Inspector updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update inspector: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified inspector
     */
    public function destroy(Inspector $inspector)
    {
        try {
            DB::beginTransaction();

            // Check if inspector has active assignments (implement when we have inspection requests)
            // For now, we'll just delete the inspector

            // Delete inspector profile
            $inspector->delete();

            // Delete user account
            $inspector->user->delete();

            DB::commit();

            return redirect()->route('admin.inspectors.index')
                ->with('success', 'Inspector deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to delete inspector: ' . $e->getMessage());
        }
    }

    /**
     * Toggle inspector availability status
     */
    public function toggleAvailability(Inspector $inspector)
    {
        try {
            $newStatus = $inspector->availability_status === 'available' ? 'offline' : 'available';
            $inspector->update(['availability_status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => "Inspector availability changed to {$newStatus}.",
                'new_status' => $newStatus
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update availability: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get inspector assignments overview
     */
    public function assignments(Request $request)
    {
        $inspectors = Inspector::with(['user'])
            ->where('availability_status', '!=', 'offline')
            ->get();

        // This will be expanded when we have inspection requests
        return view('admin.inspectors.assignments', compact('inspectors'));
    }

    /**
     * Get individual inspector assignments
     */
    public function inspectorAssignments(Inspector $inspector)
    {
        // This will be implemented when we have inspection requests
        $assignments = collect([]); // Placeholder
        
        return view('admin.inspectors.inspector-assignments', compact('inspector', 'assignments'));
    }

    // Helper methods

    /**
     * Generate unique inspector code
     */
    private function generateInspectorCode(): string
    {
        do {
            $code = 'INS' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (Inspector::where('inspector_code', $code)->exists());

        return $code;
    }

    /**
     * Get available specializations
     */
    private function getAvailableSpecializations(): array
    {
        return [
            'residential' => 'Residential Properties',
            'commercial' => 'Commercial Buildings',
            'industrial' => 'Industrial Facilities',
            'electrical' => 'Electrical Systems',
            'plumbing' => 'Plumbing Systems',
            'structural' => 'Structural Engineering',
            'hvac' => 'HVAC Systems',
            'fire_safety' => 'Fire Safety',
            'environmental' => 'Environmental Assessment',
            'energy_efficiency' => 'Energy Efficiency',
        ];
    }

    /**
     * Get equipment options
     */
    private function getEquipmentOptions(): array
    {
        return [
            'moisture_meter' => 'Moisture Meter',
            'thermal_camera' => 'Thermal Imaging Camera',
            'electrical_tester' => 'Electrical Circuit Tester',
            'gas_detector' => 'Gas Leak Detector',
            'ladder' => 'Extension Ladder',
            'measuring_tools' => 'Measuring Tools',
            'flashlight' => 'Professional Flashlight',
            'camera' => 'Digital Camera',
            'tablet' => 'Inspection Tablet',
            'safety_gear' => 'Safety Equipment',
        ];
    }
}