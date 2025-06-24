<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessPartner;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class BusinessPartnerController extends Controller
{
    /**
     * Display a listing of business partners
     */
    public function index(Request $request)
    {
        $query = BusinessPartner::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('contact_person', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('partnership_status', $request->status);
        }

        $partners = $query->with(['users'])
            ->withCount(['inspectionRequests', 'users'])
            ->paginate(20);

        // Calculate stats
        $stats = [
            'total_partners' => BusinessPartner::count(),
            'active' => BusinessPartner::where('partnership_status', 'active')->count(),
            'inactive' => BusinessPartner::where('partnership_status', 'inactive')->count(),
            'suspended' => BusinessPartner::where('partnership_status', 'suspended')->count(),
            'total_inspections' => \App\Models\InspectionRequest::whereNotNull('business_partner_id')->count(),
            'total_users' => DB::table('business_partner_users')->distinct('user_id')->count('user_id'),
        ];

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
            
            // New sync and failover validation
            'deployment_type' => ['required', Rule::in(['centralized', 'dedicated'])],
            'sync_url' => 'nullable|url|required_if:deployment_type,dedicated',
            'api_key' => 'nullable|string|max:255|required_if:deployment_type,dedicated',
            'sync_type' => ['nullable', Rule::in(['public_api', 'vpn']), 'required_if:deployment_type,dedicated'],
            'failover_active' => 'nullable|boolean',

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

            $partnerData = $request->only([
                'name', 'type', 'registration_number', 'email', 'phone', 'website',
                'address', 'city', 'country', 'contact_person', 'contact_phone',
                'contact_email', 'tier', 'discount_percentage', 'billing_cycle',
                'credit_limit', 'partnership_start_date', 'contract_end_date', 'notes',
                'deployment_type', 'sync_url', 'api_key', 'sync_type'
            ]);
            
            $partnerData['failover_active'] = $request->has('failover_active');
            $partnerData['partnership_status'] = 'active';

            // Create the business partner
            $partner = BusinessPartner::create($partnerData);

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
            $pivotData = [
                'access_level' => 'admin',
                'is_primary_contact' => true,
            ];

            if (Schema::hasColumn('business_partner_users', 'added_by')) {
                $pivotData['added_by'] = auth()->id();
            }
            if (Schema::hasColumn('business_partner_users', 'added_at')) {
                $pivotData['added_at'] = now();
            }

            $partner->users()->attach($primaryContact->id, $pivotData);

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
                $query->orderBy('business_partner_users.is_primary_contact', 'desc');
                if (Schema::hasColumn('business_partner_users', 'added_at')) {
                    $query->orderBy('business_partner_users.added_at', 'asc');
                } else {
                    $query->orderBy('business_partner_users.created_at', 'asc');
                }
            },
            'inspectionRequests' => function ($query) {
                $query->latest()->take(10);
            }
        ]);

        // Calculate statistics
        $stats = [
            'total_inspections' => $businessPartner->inspectionRequests->count(),
            'current_month_inspections' => $businessPartner->inspectionRequests()
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
            'total_users' => $businessPartner->users->count(),
            'partnership_duration_months' => $businessPartner->partnership_start_date 
                ? Carbon::parse($businessPartner->partnership_start_date)->diffInMonths(Carbon::now())
                : 0,
        ];

        return view('admin.business-partners.show', compact(
            'businessPartner', 
            'stats'
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
            'name' => 'required|string|max:255|unique:business_partners,name,' . $businessPartner->id,
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
            'partnership_status' => ['required', Rule::in(['active', 'inactive', 'suspended'])],
            'notes' => 'nullable|string',

            // New sync and failover validation
            'deployment_type' => ['required', Rule::in(['centralized', 'dedicated'])],
            'sync_url' => 'nullable|url|required_if:deployment_type,dedicated',
            'api_key' => 'nullable|string|max:255|required_if:deployment_type,dedicated',
            'sync_type' => ['nullable', Rule::in(['public_api', 'vpn']), 'required_if:deployment_type,dedicated'],
            'failover_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $partnerData = $request->only([
                'name', 'type', 'registration_number', 'email', 'phone', 'website',
                'address', 'city', 'country', 'contact_person', 'contact_phone',
                'contact_email', 'tier', 'discount_percentage', 'billing_cycle',
                'credit_limit', 'partnership_start_date', 'contract_end_date',
                'partnership_status', 'notes',
                'deployment_type', 'sync_url', 'api_key', 'sync_type'
            ]);

            $partnerData['failover_active'] = $request->has('failover_active');

            $businessPartner->update($partnerData);

            // If deployment is not dedicated, nullify sync fields
            if ($request->deployment_type !== 'dedicated') {
                $businessPartner->update([
                    'sync_url' => null,
                    'api_key' => null,
                    'sync_type' => 'public_api', // reset to default
                ]);
            }
            
            DB::commit();

            return redirect()->route('admin.business-partners.show', $businessPartner)
                ->with('success', 'Business partner updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
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
        // Force reload the users relationship
        $businessPartner->unsetRelation('users');
        $businessPartner->load([
            'users' => function ($query) {
                $query->orderBy('business_partner_users.is_primary_contact', 'desc')
                      ->orderBy('business_partner_users.id', 'asc');
            }
        ]);

        return view('admin.business-partners.users', compact('businessPartner'));
    }

    /**
     * Add user to business partner - FIXED VERSION
     */
    /**
 * Add user to business partner - FIXED VALIDATION
 */
public function addUser(Request $request, BusinessPartner $businessPartner)
{
    Log::info('Starting addUser process', [
        'business_partner_id' => $businessPartner->id,
        'user_type' => $request->user_type,
        'request_data' => $request->except(['password', 'password_confirmation'])
    ]);

    // Fixed validation rules
    $rules = [
        'user_type' => 'required|in:existing,new',
        'access_level' => 'required|in:admin,user,viewer',
    ];

    // Add conditional validation based on user type
    if ($request->user_type === 'existing') {
        $rules['user_id'] = 'required|exists:users,id';
    } else {
        // New user validation
        $rules['first_name'] = 'required|string|max:100';
        $rules['last_name'] = 'required|string|max:100';
        $rules['email'] = 'required|email|max:255|unique:users,email';
        $rules['phone'] = 'nullable|string|max:20|unique:users,phone';
        $rules['password'] = 'required|string|min:8|confirmed';
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        Log::error('Validation failed', ['errors' => $validator->errors()]);
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    // Rest of the method stays the same...
    try {
        DB::beginTransaction();

        if ($request->user_type === 'new') {
            // Create new user
            $businessPartnerRole = Role::where('name', 'business_partner')->first();
            
            if (!$businessPartnerRole) {
                // Create the role if it doesn't exist
                $businessPartnerRole = Role::create([
                    'name' => 'business_partner',
                    'display_name' => 'Business Partner',
                    'description' => 'Business partner user role',
                    'permissions' => [
                        'create_inspection_requests',
                        'view_own_inspections',
                        'view_inspection_reports',
                        'manage_partner_users',
                        'view_billing',
                        'download_reports'
                    ]
                ]);
            }
            
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'status' => 'active',
            ]);

            Log::info('User created', ['user_id' => $user->id, 'email' => $user->email]);

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
                Log::warning('User already associated', ['user_id' => $user->id, 'bp_id' => $businessPartner->id]);
                return redirect()->back()
                    ->with('error', 'User is already associated with this business partner.');
            }
        }

        // Prepare basic pivot data
        $pivotData = [
            'access_level' => $request->access_level,
            'is_primary_contact' => false,
        ];

        // Check if optional columns exist before adding them
        if (Schema::hasColumn('business_partner_users', 'added_by')) {
            $pivotData['added_by'] = auth()->id();
        }
        if (Schema::hasColumn('business_partner_users', 'added_at')) {
            $pivotData['added_at'] = now();
        }

        Log::info('Attaching user to business partner', [
            'user_id' => $user->id,
            'business_partner_id' => $businessPartner->id,
            'pivot_data' => $pivotData
        ]);

        // Associate user with business partner
        $businessPartner->users()->attach($user->id, $pivotData);

        // Verify attachment
        $attached = DB::table('business_partner_users')
            ->where('business_partner_id', $businessPartner->id)
            ->where('user_id', $user->id)
            ->exists();
            
        Log::info('User attachment result', [
            'attached' => $attached,
            'user_id' => $user->id,
            'bp_id' => $businessPartner->id
        ]);

        if (!$attached) {
            throw new \Exception('Failed to attach user to business partner');
        }

        DB::commit();

        Log::info('User successfully added to business partner');

        return redirect()->back()
            ->with('success', 'User added to business partner successfully.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Failed to add user', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return redirect()->back()
            ->with('error', 'Failed to add user: ' . $e->getMessage())
            ->withInput();
    }
}
    /**
  * Set user as primary contact
 */
public function setPrimaryContact(Request $request, BusinessPartner $businessPartner, User $user)
{
    try {
        // Check if user is associated with this partner
        $userExists = $businessPartner->users()->where('user_id', $user->id)->exists();
        
        if (!$userExists) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not associated with this business partner.'
                ], 404);
            }
            return redirect()->back()
                ->with('error', 'User is not associated with this business partner.');
        }

        // Simple direct database update - no model method
        DB::beginTransaction();

        // Remove primary contact status from all users for this business partner
        DB::table('business_partner_users')
            ->where('business_partner_id', $businessPartner->id)
            ->update(['is_primary_contact' => 0]);

        // Set new primary contact
        DB::table('business_partner_users')
            ->where('business_partner_id', $businessPartner->id)
            ->where('user_id', $user->id)
            ->update(['is_primary_contact' => 1]);

        DB::commit();

        // Always return JSON response for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Primary contact updated successfully.'
            ]);
        }

        return redirect()->back()
            ->with('success', 'Primary contact updated successfully.');

    } catch (\Exception $e) {
        DB::rollBack();
        
        \Log::error('Failed to set primary contact', [
            'error' => $e->getMessage(),
            'user_id' => $user->id,
            'business_partner_id' => $businessPartner->id
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the primary contact.'
            ], 500);
        }
        
        return redirect()->back()
            ->with('error', 'An error occurred while updating the primary contact.');
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