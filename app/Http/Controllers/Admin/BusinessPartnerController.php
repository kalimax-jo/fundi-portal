<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessPartner;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BusinessPartnerController extends Controller
{
    /**
     * Display a listing of business partners
     */
    public function index(Request $request)
    {
        $query = BusinessPartner::query()->with(['users' => function($query) {
            $query->wherePivot('is_primary_contact', true);
        }]);

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%')
                  ->orWhere('contact_person', 'like', '%' . $searchTerm . '%')
                  ->orWhere('registration_number', 'like', '%' . $searchTerm . '%');
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by partnership status
        if ($request->filled('status')) {
            $query->where('partnership_status', $request->status);
        }

        // Filter by tier
        if ($request->filled('tier')) {
            $query->where('tier', $request->tier);
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $allowedSortFields = ['name', 'type', 'partnership_status', 'tier', 'created_at', 'total_inspections'];
        if (in_array($sortField, $allowedSortFields)) {
            if ($sortField === 'total_inspections') {
                $query->withCount('inspectionRequests as total_inspections')
                      ->orderBy('total_inspections', $sortDirection);
            } else {
                $query->orderBy($sortField, $sortDirection);
            }
        }

        $partners = $query->paginate(15)->withQueryString();

        // Get statistics for the header
        $stats = [
            'total' => BusinessPartner::count(),
            'active' => BusinessPartner::where('partnership_status', 'active')->count(),
            'inactive' => BusinessPartner::where('partnership_status', 'inactive')->count(),
            'suspended' => BusinessPartner::where('partnership_status', 'suspended')->count(),
        ];

        // Get partner types for filter
        $partnerTypes = [
            'bank' => 'Bank',
            'insurance' => 'Insurance Company',
            'microfinance' => 'Microfinance Institution',
            'mortgage' => 'Mortgage Company',
            'investment' => 'Investment Firm'
        ];

        return view('admin.business-partners.index', compact(
            'partners', 
            'stats', 
            'partnerTypes'
        ));
    }

    /**
     * Show the form for creating a new business partner
     */
    public function create()
    {
        $partnerTypes = [
            'bank' => 'Bank',
            'insurance' => 'Insurance Company',
            'microfinance' => 'Microfinance Institution',
            'mortgage' => 'Mortgage Company',
            'investment' => 'Investment Firm'
        ];

        $tiers = [
            'bronze' => 'Bronze',
            'silver' => 'Silver',
            'gold' => 'Gold',
            'platinum' => 'Platinum'
        ];

        return view('admin.business-partners.create', compact('partnerTypes', 'tiers'));
    }

    /**
     * Store a newly created business partner
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:business_partners',
            'type' => 'required|in:bank,insurance,microfinance,mortgage,investment',
            'registration_number' => 'nullable|string|max:100',
            'email' => 'required|email|max:255|unique:business_partners',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'contact_person' => 'required|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'required|email|max:255',
            'tier' => 'required|in:bronze,silver,gold,platinum',
            'discount_percentage' => 'nullable|numeric|min:0|max:50',
            'billing_cycle' => 'required|in:monthly,quarterly,annually',
            'credit_limit' => 'nullable|numeric|min:0',
            'partnership_start_date' => 'required|date',
            'contract_end_date' => 'nullable|date|after:partnership_start_date',
            'notes' => 'nullable|string',
            
            // Primary contact user details
            'primary_contact_first_name' => 'required|string|max:100',
            'primary_contact_last_name' => 'required|string|max:100',
            'primary_contact_email' => 'required|email|max:255|unique:users,email',
            'primary_contact_phone' => 'nullable|string|max:20|unique:users,phone',
            'primary_contact_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Create the business partner
            $partner = BusinessPartner::create([
                'name' => $request->name,
                'type' => $request->type,
                'registration_number' => $request->registration_number,
                'email' => $request->email,
                'phone' => $request->phone,
                'website' => $request->website,
                'address' => $request->address,
                'city' => $request->city,
                'country' => $request->country,
                'contact_person' => $request->contact_person,
                'contact_phone' => $request->contact_phone,
                'contact_email' => $request->contact_email,
                'tier' => $request->tier,
                'discount_percentage' => $request->discount_percentage ?? 0,
                'billing_cycle' => $request->billing_cycle,
                'credit_limit' => $request->credit_limit,
                'partnership_start_date' => $request->partnership_start_date,
                'contract_end_date' => $request->contract_end_date,
                'partnership_status' => 'active',
                'notes' => $request->notes,
            ]);

            // Create primary contact user
            $businessPartnerRole = Role::where('name', 'business_partner')->first();
            
            $primaryContact = User::create([
                'first_name' => $request->primary_contact_first_name,
                'last_name' => $request->primary_contact_last_name,
                'email' => $request->primary_contact_email,
                'phone' => $request->primary_contact_phone,
                'password' => Hash::make($request->primary_contact_password),
                'status' => 'active',
            ]);

            // Assign business partner role
            $primaryContact->roles()->attach($businessPartnerRole->id, [
                'assigned_at' => now(),
                'assigned_by' => auth()->id(),
            ]);

            // Associate user with business partner as primary contact
            $partner->users()->attach($primaryContact->id, [
                'access_level' => 'admin',
                'is_primary_contact' => true,
                'added_by' => auth()->id(),
                'added_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('admin.business-partners.show', $partner)
                ->with('success', 'Business partner created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create business partner: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified business partner
     */
    public function show(BusinessPartner $businessPartner)
    {
        $businessPartner->load([
            'users' => function ($query) {
                $query->orderBy('business_partner_users.is_primary_contact', 'desc')
                      ->orderBy('business_partner_users.added_at', 'asc');
            },
            'inspectionRequests' => function ($query) {
                $query->latest()->take(10);
            },
            'billings' => function ($query) {
                $query->latest()->take(5);
            }
        ]);

        // Calculate statistics
        $stats = [
            'total_inspections' => $businessPartner->getTotalInspections(),
            'current_month_inspections' => $businessPartner->getCurrentMonthInspections(),
            'total_amount_spent' => $businessPartner->getTotalAmountSpent(),
            'pending_billing_amount' => $businessPartner->getPendingBillingAmount(),
            'partnership_duration_months' => $businessPartner->getPartnershipDurationInMonths(),
            'average_monthly_inspections' => $businessPartner->getPartnershipDurationInMonths() > 0 
                ? round($businessPartner->getTotalInspections() / $businessPartner->getPartnershipDurationInMonths(), 1) 
                : 0,
        ];

        // Get recent activity data for charts
        $monthlyInspections = $businessPartner->inspectionRequests()
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->orderByRaw('YEAR(created_at), MONTH(created_at)')
            ->get();

        return view('admin.business-partners.show', compact(
            'businessPartner', 
            'stats', 
            'monthlyInspections'
        ));
    }

    /**
     * Show the form for editing the business partner
     */
    public function edit(BusinessPartner $businessPartner)
    {
        $partnerTypes = [
            'bank' => 'Bank',
            'insurance' => 'Insurance Company',
            'microfinance' => 'Microfinance Institution',
            'mortgage' => 'Mortgage Company',
            'investment' => 'Investment Firm'  
        ];

        $tiers = [
            'bronze' => 'Bronze',
            'silver' => 'Silver',
            'gold' => 'Gold',
            'platinum' => 'Platinum'
        ];

        return view('admin.business-partners.edit', compact('businessPartner', 'partnerTypes', 'tiers'));
    }

    /**
     * Update the specified business partner
     */
    public function update(Request $request, BusinessPartner $businessPartner)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', Rule::unique('business_partners')->ignore($businessPartner->id)],
            'type' => 'required|in:bank,insurance,microfinance,mortgage,investment',
            'registration_number' => 'nullable|string|max:100',
            'email' => ['required', 'email', 'max:255', Rule::unique('business_partners')->ignore($businessPartner->id)],
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'contact_person' => 'required|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'required|email|max:255',
            'tier' => 'required|in:bronze,silver,gold,platinum',
            'discount_percentage' => 'nullable|numeric|min:0|max:50',
            'billing_cycle' => 'required|in:monthly,quarterly,annually',
            'credit_limit' => 'nullable|numeric|min:0',
            'partnership_start_date' => 'required|date',
            'contract_end_date' => 'nullable|date|after:partnership_start_date',
            'partnership_status' => 'required|in:active,inactive,suspended',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $businessPartner->update($request->only([
                'name', 'type', 'registration_number', 'email', 'phone', 
                'website', 'address', 'city', 'country', 'contact_person',
                'contact_phone', 'contact_email', 'tier', 'discount_percentage',
                'billing_cycle', 'credit_limit', 'partnership_start_date',
                'contract_end_date', 'partnership_status', 'notes'
            ]));

            return redirect()->route('admin.business-partners.show', $businessPartner)
                ->with('success', 'Business partner updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update business partner: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified business partner
     */
    public function destroy(BusinessPartner $businessPartner)
    {
        try {
            // Check if business partner has any active inspection requests
            $activeInspections = $businessPartner->inspectionRequests()
                ->whereIn('status', ['pending', 'assigned', 'in_progress'])
                ->count();

            if ($activeInspections > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete business partner with active inspection requests.');
            }

            DB::beginTransaction();

            // Detach all users
            $businessPartner->users()->detach();

            // Delete the business partner
            $businessPartner->delete();

            DB::commit();

            return redirect()->route('admin.business-partners.index')
                ->with('success', 'Business partner deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to delete business partner: ' . $e->getMessage());
        }
    }

    /**
     * Toggle business partner status
     */
    public function toggleStatus(Request $request, BusinessPartner $businessPartner)
    {
        try {
            $newStatus = $businessPartner->partnership_status === 'active' ? 'inactive' : 'active';
            
            $businessPartner->update([
                'partnership_status' => $newStatus
            ]);

            $message = $businessPartner->partnership_status === 'active' 
                ? 'Business partner activated successfully.' 
                : 'Business partner deactivated successfully.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'new_status' => $newStatus
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show business partner users
     */
    public function users(BusinessPartner $businessPartner)
    {
        $businessPartner->load([
            'users' => function ($query) {
                $query->orderBy('business_partner_users.is_primary_contact', 'desc')
                      ->orderBy('business_partner_users.added_at', 'asc');
            }
        ]);

        return view('admin.business-partners.users', compact('businessPartner'));
    }

    /**
     * Add user to business partner
     */
    public function addUser(Request $request, BusinessPartner $businessPartner)
    {
        $validator = Validator::make($request->all(), [
            'user_type' => 'required|in:existing,new',
            'user_id' => 'required_if:user_type,existing|exists:users,id',
            'access_level' => 'required|in:admin,user,viewer',
            
            // New user fields
            'first_name' => 'required_if:user_type,new|string|max:100',
            'last_name' => 'required_if:user_type,new|string|max:100',
            'email' => 'required_if:user_type,new|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20|unique:users,phone',
            'password' => 'required_if:user_type,new|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            if ($request->user_type === 'new') {
                // Create new user
                $businessPartnerRole = Role::where('name', 'business_partner')->first();
                
                $user = User::create([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'password' => Hash::make($request->password),
                    'status' => 'active',
                ]);

                // Assign business partner role
                $user->roles()->attach($businessPartnerRole->id, [
                    'assigned_at' => now(),
                    'assigned_by' => auth()->id(),
                ]);
            } else {
                // Use existing user
                $user = User::findOrFail($request->user_id);
                
                // Check if user is already associated with this partner
                if ($businessPartner->users->contains($user->id)) {
                    return redirect()->back()
                        ->with('error', 'User is already associated with this business partner.');
                }
            }

            // Associate user with business partner
            $businessPartner->users()->attach($user->id, [
                'access_level' => $request->access_level,
                'is_primary_contact' => false,
                'added_by' => auth()->id(),
                'added_at' => now(),
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'User added to business partner successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to add user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove user from business partner
     */
    public function removeUser(BusinessPartner $businessPartner, User $user)
    {
        try {
            // Check if this is the primary contact
            $pivot = $businessPartner->users()->where('user_id', $user->id)->first();
            if ($pivot && $pivot->pivot->is_primary_contact) {
                return redirect()->back()
                    ->with('error', 'Cannot remove primary contact. Please set another user as primary contact first.');
            }

            $businessPartner->users()->detach($user->id);

            return redirect()->back()
                ->with('success', 'User removed from business partner successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to remove user: ' . $e->getMessage());
        }
    }

    /**
     * Set user as primary contact
     */
    public function setPrimaryContact(Request $request, BusinessPartner $businessPartner, User $user)
    {
        try {
            // Check if user is associated with this partner
            if (!$businessPartner->users->contains($user->id)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User is not associated with this business partner.'
                    ], 404);
                }
                return redirect()->back()
                    ->with('error', 'User is not associated with this business partner.');
            }

            $businessPartner->setPrimaryContact($user);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Primary contact updated successfully.'
                ]);
            }

            return redirect()->back()
                ->with('success', 'Primary contact updated successfully.');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update primary contact: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Failed to update primary contact: ' . $e->getMessage());
        }
    }

    /**
     * Update user access level
     */
    public function updateUserAccess(Request $request, BusinessPartner $businessPartner, User $user)
    {
        $validator = Validator::make($request->all(), [
            'access_level' => 'required|in:admin,user,viewer',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Check if user is associated with this partner
            if (!$businessPartner->users->contains($user->id)) {
                return redirect()->back()
                    ->with('error', 'User is not associated with this business partner.');
            }

            // Update the pivot table
            $businessPartner->users()->updateExistingPivot($user->id, [
                'access_level' => $request->access_level
            ]);

            return redirect()->back()
                ->with('success', 'User access level updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update user access: ' . $e->getMessage());
        }
    }
}