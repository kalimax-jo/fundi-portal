<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\BusinessPartner;
use App\Models\User;
use App\Models\InspectionRequest;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Helpers\PartnerAccess;

class DashboardController extends Controller
{
    /**
     * Helper to get the current business partner from the request.
     */
    protected function getCurrentPartner(Request $request)
    {
        return $request->attributes->get('business_partner');
    }

    /**
     * Display the business partner dashboard
     */
    public function index(Request $request)
    {
        \Log::info('Partner dashboard accessed', [
            'user_id' => auth()->id(),
            'is_authenticated' => auth()->check(),
            'session_partner' => session('current_business_partner'),
            'request_partner' => $request->attributes->get('business_partner')
        ]);
        
        $partner = $this->getCurrentPartner($request);
        if (!$partner) {
            return redirect()->route('partner.login')->with('error', 'No partner found.');
        }
        
        // Load relationships with eager loading
        $partner->load([
            'users',
            'inspectionRequests' => function ($query) {
                $query->latest()->take(10);
            },
            'properties' => function ($query) {
                $query->latest()->take(10);
            },
            'billings' => function ($query) {
                $query->latest()->take(5);
            }
        ]);

        // Enhanced statistics
        $stats = [
            'total_users' => $partner->users->count(),
            'total_clients' => $partner->users()->where('created_by', '!=', null)->count(),
            'total_properties' => $partner->ownedProperties()->count(),
            'total_requests' => $partner->inspectionRequests->count(),
            'pending_requests' => $partner->inspectionRequests()->where('status', 'pending')->count(),
            'completed_requests' => $partner->inspectionRequests()->where('status', 'completed')->count(),
            'this_month_requests' => $partner->inspectionRequests()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'total_amount_spent' => $partner->getTotalAmountSpent(),
            'pending_billing_amount' => $partner->getPendingBillingAmount(),
            'active_users' => $partner->users()->where('status', 'active')->count(),
            'recent_logins' => $partner->users()
                ->whereNotNull('last_login_at')
                ->where('last_login_at', '>=', now()->subDays(7))
                ->count(),
        ];

        // Recent inspection requests
        $recentRequests = $partner->inspectionRequests()
            ->with(['property', 'inspector.user'])
            ->latest()
            ->take(5)
            ->get();

        // Recent activities (combine various activities)
        $recentActivities = collect();
        
        // Add recent inspection requests as activities
        foreach ($recentRequests as $request) {
            $recentActivities->push([
                'type' => 'inspection_request',
                'title' => 'Inspection Request Created',
                'description' => "Request #{$request->request_code} for " . ($request->property->address ?? 'Unknown Property'),
                'status' => $request->status,
                'created_at' => $request->created_at,
                'icon' => 'clipboard-list',
                'color' => 'blue'
            ]);
        }

        // Add recent user activities
        $recentUserLogins = $partner->users()
            ->whereNotNull('last_login_at')
            ->where('last_login_at', '>=', now()->subDays(3))
            ->orderBy('last_login_at', 'desc')
            ->take(3)
            ->get();

        foreach ($recentUserLogins as $user) {
            $recentActivities->push([
                'type' => 'user_login',
                'title' => 'User Login',
                'description' => "{$user->first_name} {$user->last_name} logged in",
                'created_at' => $user->last_login_at,
                'icon' => 'user',
                'color' => 'green'
            ]);
        }

        // Add recent billing activities
        $recentBillings = $partner->billings()
            ->latest()
            ->take(3)
            ->get();

        foreach ($recentBillings as $billing) {
            $recentActivities->push([
                'type' => 'billing',
                'title' => 'Billing Generated',
                'description' => "Invoice #{$billing->invoice_number} - {$billing->amount}",
                'status' => $billing->status,
                'created_at' => $billing->created_at,
                'icon' => 'credit-card',
                'color' => 'purple'
            ]);
        }

        // Sort activities by date
        $recentActivities = $recentActivities->sortByDesc('created_at')->take(10);

        // Get current tier information
        $activeTier = \App\Models\PartnerTier::where('business_partner_id', $partner->id)
            ->where('status', 'active')
            ->with('tier')
            ->latest('started_at')
            ->first();
        
        $remainingRequests = null;
        if ($activeTier) {
            $quota = $activeTier->tier->request_quota;
            $used = $partner->inspectionRequests()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
            $remainingRequests = max(0, $quota - $used);
        }

        // Financial Overview: Use PartnerTierPayment for tier subscription payments
        $partnerTierIds = \App\Models\PartnerTier::where('business_partner_id', $partner->id)->pluck('id');
        $totalSpent = \App\Models\PartnerTierPayment::whereIn('partner_tier_id', $partnerTierIds)
            ->where('status', 'paid')
            ->sum('amount');
        $pendingAmount = \App\Models\PartnerTierPayment::whereIn('partner_tier_id', $partnerTierIds)
            ->where('status', 'pending')
            ->sum('amount');
        $thisMonthSpent = \App\Models\PartnerTierPayment::whereIn('partner_tier_id', $partnerTierIds)
            ->where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        $quickStats = [
            'users' => [
                'total' => $stats['total_users'],
                'active' => $stats['active_users'],
                'recent_logins' => $stats['recent_logins'],
                'growth' => $partner->users()->where('users.created_at', '>=', now()->subDays(30))->count()
            ],
            'requests' => [
                'total' => $stats['total_requests'],
                'pending' => $stats['pending_requests'],
                'completed' => $stats['completed_requests'],
                'this_month' => $stats['this_month_requests']
            ],
            'financial' => [
                'total_spent' => $totalSpent,
                'pending_amount' => $pendingAmount,
                'this_month_spent' => $thisMonthSpent,
                'currency' => 'RWF',
            ],
        ];

        return view('business-partner.dashboard.index', compact(
            'partner', 
            'stats', 
            'recentRequests', 
            'recentActivities',
            'quickStats',
            'activeTier',
            'remainingRequests'
        ));
    }

    /**
     * Display user management page
     */
    public function users(Request $request)
    {
        $partner = $this->getCurrentPartner($request);
        if (!$partner) {
            return redirect()->route('partner.dashboard')->with('error', 'No partner found.');
        }
        $partner->load(['users' => function ($query) {
            $query->orderBy('business_partner_users.is_primary_contact', 'desc')
                  ->orderBy('business_partner_users.created_at', 'asc');
        }]);
        return view('business-partner.users.index', compact('partner'));
    }

    /**
     * Show the form for creating a new user
     */
    public function createUser(Request $request)
    {
        $partner = $this->getCurrentPartner($request);
        if (!$partner) {
            return redirect()->route('partner.dashboard')->with('error', 'No partner found.');
        }
        return view('business-partner.users.create', compact('partner'));
    }

    /**
     * Store a newly created user
     */
    public function storeUser(Request $request)
    {
        $partner = $this->getCurrentPartner($request);
        $user = Auth::user();
        if (!$partner) {
            return redirect()->route('partner.dashboard')->with('error', 'No partner found.');
        }
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'is_primary_contact' => 'boolean',
            'can_create_inspections' => 'boolean',
            'can_view_reports' => 'boolean',
            'can_manage_users' => 'boolean',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        try {
            $newUser = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'created_by' => $user->id,
            ]);
            $businessPartnerRole = \App\Models\Role::where('name', 'business_partner')->first();
            if ($businessPartnerRole) {
                $newUser->roles()->attach($businessPartnerRole->id);
            }
            $partner->users()->attach($newUser->id, [
                'is_primary_contact' => $request->boolean('is_primary_contact', false),
                'can_create_inspections' => $request->boolean('can_create_inspections', true),
                'can_view_reports' => $request->boolean('can_view_reports', true),
                'can_manage_users' => $request->boolean('can_manage_users', false),
                'assigned_at' => now(),
                'assigned_by' => $user->id,
            ]);
            if ($request->boolean('is_primary_contact', false)) {
                $partner->users()->wherePivot('user_id', '!=', $newUser->id)
                    ->updateExistingPivot($newUser->id, ['is_primary_contact' => false]);
            }
            return redirect()->route('partner.users.index')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create user: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing a user
     */
    public function editUser(Request $request, User $user)
    {
        $partner = $this->getCurrentPartner($request);
        if (!$partner) {
            return redirect()->route('partner.dashboard')->with('error', 'No partner found.');
        }
        if (!$partner->users()->where('user_id', $user->id)->exists()) {
            return redirect()->route('partner.users.index')->with('error', 'User not found in your organization.');
        }
        $userPermissions = $partner->users()->where('user_id', $user->id)->first()->pivot;
        return view('business-partner.users.edit', compact('partner', 'user', 'userPermissions'));
    }

    /**
     * Update the specified user
     */
    public function updateUser(Request $request, User $user)
    {
        $partner = $this->getCurrentPartner($request);
        if (!$partner) {
            return redirect()->route('partner.dashboard')->with('error', 'No partner found.');
        }
        if (!$partner->users()->where('user_id', $user->id)->exists()) {
            return redirect()->route('partner.users.index')->with('error', 'User not found in your organization.');
        }
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'is_primary_contact' => 'boolean',
            'can_create_inspections' => 'boolean',
            'can_view_reports' => 'boolean',
            'can_manage_users' => 'boolean',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        try {
            $user->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
            ]);
            $partner->users()->updateExistingPivot($user->id, [
                'is_primary_contact' => $request->boolean('is_primary_contact', false),
                'can_create_inspections' => $request->boolean('can_create_inspections', true),
                'can_view_reports' => $request->boolean('can_view_reports', true),
                'can_manage_users' => $request->boolean('can_manage_users', false),
            ]);
            if ($request->boolean('is_primary_contact', false)) {
                $partner->users()->wherePivot('user_id', '!=', $user->id)
                    ->updateExistingPivot($user->id, ['is_primary_contact' => false]);
            }
            return redirect()->route('partner.users.index')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update user: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove a user from the partner organization
     */
    public function removeUser(Request $request, User $user)
    {
        $partner = $this->getCurrentPartner($request);
        if (!$partner) {
            return redirect()->route('partner.dashboard')->with('error', 'No partner found.');
        }
        if (!$partner->users()->where('user_id', $user->id)->exists()) {
            return redirect()->route('partner.users.index')->with('error', 'User not found in your organization.');
        }
        try {
            $partner->users()->detach($user->id);
            // Optionally delete the user if not attached to any other partner
            if ($user->businessPartners()->count() === 0) {
                $user->delete();
            }
            return redirect()->route('partner.users.index')->with('success', 'User removed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to remove user: ' . $e->getMessage());
        }
    }

    /**
     * Set a user as the primary contact
     */
    public function setPrimaryContact(Request $request, User $user)
    {
        $partner = $this->getCurrentPartner($request);
        if (!$partner) {
            return redirect()->route('partner.dashboard')->with('error', 'No partner found.');
        }
        if (!$partner->users()->where('user_id', $user->id)->exists()) {
            return redirect()->route('partner.users.index')->with('error', 'User not found in your organization.');
        }
        try {
            $partner->setPrimaryContact($user);
            return redirect()->route('partner.users.index')->with('success', 'Primary contact updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update primary contact: ' . $e->getMessage());
        }
    }

    /**
     * Show user details
     */
    public function showUser(Request $request, User $user)
    {
        $partner = $this->getCurrentPartner($request);
        if (!$partner) {
            return redirect()->route('partner.dashboard')->with('error', 'No partner found.');
        }
        if (!$partner->users()->where('user_id', $user->id)->exists()) {
            return redirect()->route('partner.users.index')->with('error', 'User not found in your organization.');
        }
        $userPermissions = $partner->users()->where('user_id', $user->id)->first()->pivot;
        return view('business-partner.users.show', compact('partner', 'user', 'userPermissions'));
    }

    /**
     * Display inspection requests
     */
    public function inspectionRequests(Request $request)
    {
        $partner = $this->getCurrentPartner($request);
        if (!$partner) {
            return redirect()->route('partner.dashboard')->with('error', 'No partner found.');
        }
        $inspectionRequests = $partner->inspectionRequests()->with(['property', 'inspector.user'])->latest()->paginate(15);
        $clients = $partner->users()->whereNotNull('created_by')->get();
        return view('business-partner.inspection-requests.index', compact('partner', 'inspectionRequests', 'clients'));
    }

    /**
     * Show the form for creating a new inspection request
     */
    public function createInspectionRequest(Request $request)
    {
        $partner = $this->getCurrentPartner($request);
        if (!PartnerAccess::can('create_request', $partner)) {
            return redirect()->route('business-partner.inspection-requests.index')
                ->with('error', 'You cannot create a new request: either your quota is used up, you have no active tier, or your tier does not allow any packages.');
        }
        if (!$partner) {
            return redirect()->route('partner.dashboard')->with('error', 'No partner found.');
        }
        // Get active tier and allowed packages
        $activeTier = \App\Models\PartnerTier::where('business_partner_id', $partner->id)
            ->where('status', 'active')
            ->with('tier.inspectionPackages')
            ->latest('started_at')
            ->first();
        $allowedPackages = $activeTier ? $activeTier->tier->inspectionPackages : collect();
        $properties = $partner->ownedProperties()->get();
        $clients = $partner->users()->get();
        return view('business-partner.inspection-requests.create', compact('partner', 'allowedPackages', 'properties', 'clients'));
    }

    /**
     * Store a newly created inspection request
     */
    public function storeInspectionRequest(Request $request)
    {
        $partner = $this->getCurrentPartner($request);
        if (!PartnerAccess::can('create_request', $partner)) {
            abort(403, 'You do not have permission to create inspection requests.');
        }

        $validated = $request->validate([
            'preferred_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string',
            'client_id' => 'nullable|exists:users,id',
            // Property selection (either existing or new)
            'property_id' => 'nullable|exists:properties,id',
            // New property fields
            'new_property_address' => 'nullable|string|required_without:property_id',
            'new_property_code' => 'nullable|string',
            'new_property_district' => 'nullable|string|required_with:new_property_address',
            'new_property_sector' => 'nullable|string',
            'new_property_cell' => 'nullable|string',
            // New client fields
            'new_client_full_name' => 'nullable|string|required_with:new_property_address',
            'new_client_national_id' => 'nullable|string',
            'new_client_email' => 'nullable|email|required_with:new_property_address',
            'new_client_phone' => 'nullable|string|required_with:new_property_address',
        ]);

        \DB::beginTransaction();
        try {
            // 1. Handle property and client creation if needed
            if ($request->filled('new_property_address')) {
                // Create new client
                $client = \App\Models\User::create([
                    'first_name' => $request->new_client_full_name,
                    'last_name' => '',
                    'email' => $request->new_client_email,
                    'phone' => $request->new_client_phone,
                    'status' => 'active',
                    'created_by' => auth()->id(),
                ]);
                // Attach client to partner
                $partner->users()->attach($client->id, [
                    'access_level' => 'user',
                ]);
                // Create new property
                $property = \App\Models\Property::create([
                    'property_code' => $request->new_property_code ?? \App\Models\Property::generatePropertyCode(),
                    'owner_name' => $request->new_client_full_name,
                    'owner_phone' => $request->new_client_phone,
                    'owner_email' => $request->new_client_email,
                    'address' => $request->new_property_address,
                    'district' => $request->new_property_district,
                    'sector' => $request->new_property_sector,
                    'cell' => $request->new_property_cell,
                    'client_national_id' => $request->new_client_national_id,
                    'client_name' => $request->new_client_full_name,
                    'business_partner_id' => $partner->id,
                ]);
                // Attach property to partner
                $partner->properties()->save($property);
                $propertyId = $property->id;
                $clientId = $client->id;
            } else {
                $propertyId = $request->property_id;
                $clientId = $request->client_id;
            }

            // 2. Auto-select inspection package based on partner's current active tier
            $activeTier = \App\Models\PartnerTier::where('business_partner_id', $partner->id)
                ->where('status', 'active')
                ->with('tier.inspectionPackages')
                ->latest('started_at')
                ->first();
            $inspectionPackageId = null;
            if ($activeTier && $activeTier->tier && $activeTier->tier->inspectionPackages->count() > 0) {
                $inspectionPackageId = $activeTier->tier->inspectionPackages->first()->id;
            }

            // 3. If no client selected, use property owner email to create/find user
            $generatedCredentials = null;
            if (!$clientId && $propertyId) {
                $property = \App\Models\Property::find($propertyId);
                if ($property && $property->owner_email) {
                    $client = \App\Models\User::where('email', $property->owner_email)->first();
                    if (!$client) {
                        // Generate username and password
                        $username = explode('@', $property->owner_email)[0] . rand(100,999);
                        $password = \Str::random(8);
                        $client = \App\Models\User::create([
                            'first_name' => $property->owner_name ?? 'Client',
                            'last_name' => '',
                            'email' => $property->owner_email,
                            'phone' => $property->owner_phone,
                            'status' => 'active',
                            'created_by' => auth()->id(),
                            'password' => bcrypt($password),
                        ]);
                        $partner->users()->attach($client->id, [
                            'access_level' => 'user',
                        ]);
                        $generatedCredentials = [
                            'username' => $client->email,
                            'password' => $password,
                        ];
                    }
                    $clientId = $client->id;
                }
            }

            // 4. Create the inspection request
            $inspectionRequest = \App\Models\InspectionRequest::create([
                'requester_type' => 'business_partner',
                'requester_user_id' => auth()->id(),
                'business_partner_id' => $partner->id,
                'property_id' => $propertyId,
                'package_id' => $inspectionPackageId,
                'preferred_date' => $request->preferred_date,
                'special_instructions' => $request->notes,
                'status' => 'pending',
            ]);
            // Optionally associate client with the request (if needed)
            if ($clientId) {
                $inspectionRequest->client_id = $clientId;
                $inspectionRequest->save();
            }
            \DB::commit();
            // Optionally: flash credentials to session for notification
            if ($generatedCredentials) {
                session()->flash('client_credentials', $generatedCredentials);
            }
            return redirect()->route('business-partner.inspection-requests.index')->with('success', 'Inspection request created successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create inspection request: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified inspection request
     */
    public function showInspectionRequest(Request $request, $id)
    {
        $partner = $this->getCurrentPartner($request);
        if (!$partner) {
            return redirect()->route('partner.dashboard')->with('error', 'No partner found.');
        }
        $inspectionRequest = $partner->inspectionRequests()->with(['property', 'inspector.user'])->findOrFail($id);
        return view('business-partner.inspection-requests.show', compact('partner', 'inspectionRequest'));
    }

    /**
     * Display properties
     */
    public function properties(Request $request)
    {
        $partner = $this->getCurrentPartner($request);
        if (!$partner) {
            return redirect()->route('partner.dashboard')->with('error', 'No partner found.');
        }
        $properties = $partner->ownedProperties()->latest()->paginate(15);
        return view('business-partner.properties.index', compact('partner', 'properties'));
    }

    /**
     * Show the form for creating a new property
     */
    public function createProperty(Request $request)
    {
        $partner = $this->getCurrentPartner($request);
        if (!$partner) {
            return redirect()->route('partner.dashboard')->with('error', 'No partner found.');
        }
        $clients = $partner->users()->whereNotNull('created_by')->get();
        $districtGroups = \App\Models\Property::getRwandaDistricts();
        $districts = collect($districtGroups)->flatten()->unique()->values()->all();
        $propertyTypes = [
            'residential' => 'Residential',
            'commercial' => 'Commercial',
            'industrial' => 'Industrial',
            'mixed' => 'Mixed Use',
        ];
        return view('business-partner.properties.create', compact('partner', 'clients', 'districts', 'propertyTypes'));
    }

    /**
     * Store a newly created property
     */
    public function storeProperty(Request $request)
    {
        $partner = $this->getCurrentPartner($request);
        if (!$partner) {
            return redirect()->route('partner.dashboard')->with('error', 'No partner found.');
        }

        $validated = $request->validate([
            'property_code' => 'nullable|string|max:50|unique:properties,property_code',
            'address' => 'required|string|max:255',
            'district' => 'required|string|max:100',
            'sector' => 'nullable|string|max:100',
            'cell' => 'nullable|string|max:100',
            'owner_name' => 'required|string|max:255',
            'owner_phone' => 'required|string|max:20',
            'owner_email' => 'nullable|email|max:255',
            'client_national_id' => 'nullable|string|max:50',
            'client_name' => 'nullable|string|max:255',
            'property_type' => 'required|string|max:50',
            // Map form fields to DB fields
            'property_size' => 'nullable|numeric|min:0',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'construction_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'additional_notes' => 'nullable|string|max:1000',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'market_value' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        // Map form fields to DB fields
        $validated['total_area_sqm'] = $validated['property_size'] ?? null;
        $validated['bedrooms_count'] = $validated['bedrooms'] ?? null;
        $validated['bathrooms_count'] = $validated['bathrooms'] ?? null;
        $validated['built_year'] = $validated['construction_year'] ?? null;

        try {
            $property = new \App\Models\Property($validated);
            $property->property_code = $validated['property_code'] ?? \App\Models\Property::generatePropertyCode();
            $property->business_partner_id = $partner->id;
            $property->save();

            return redirect()->route('business-partner.properties.index')
                ->with('success', 'Property created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create property: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified property
     */
    public function showProperty(Request $request, $id)
    {
        $partner = $this->getCurrentPartner($request);
        if (!$partner) {
            return redirect()->route('partner.dashboard')->with('error', 'No partner found.');
        }

        $property = $partner->ownedProperties()->findOrFail($id);
        $inspectionRequests = $property->inspectionRequests()->with(['inspector.user'])->latest()->paginate(10);

        return view('business-partner.properties.show', compact('partner', 'property', 'inspectionRequests'));
    }

    /**
     * Show the form for editing the specified property
     */
    public function editProperty(Request $request, $id)
    {
        $partner = $this->getCurrentPartner($request);
        if (!$partner) {
            return redirect()->route('partner.dashboard')->with('error', 'No partner found.');
        }

        $property = $partner->ownedProperties()->findOrFail($id);
        return view('business-partner.properties.edit', compact('partner', 'property'));
    }

    /**
     * Update the specified property
     */
    public function updateProperty(Request $request, $id)
    {
        $partner = $this->getCurrentPartner($request);
        if (!$partner) {
            return redirect()->route('partner.dashboard')->with('error', 'No partner found.');
        }

        $property = $partner->ownedProperties()->findOrFail($id);

        $validated = $request->validate([
            'property_code' => 'nullable|string|max:50|unique:properties,property_code,' . $property->id,
            'address' => 'required|string|max:255',
            'district' => 'required|string|max:100',
            'sector' => 'nullable|string|max:100',
            'cell' => 'nullable|string|max:100',
            'owner_name' => 'required|string|max:255',
            'owner_phone' => 'required|string|max:20',
            'owner_email' => 'nullable|email|max:255',
            'client_national_id' => 'nullable|string|max:50',
            'client_name' => 'nullable|string|max:255',
            'property_type' => 'nullable|string|max:50',
            'total_area_sqm' => 'nullable|numeric|min:0',
            'bedrooms_count' => 'nullable|integer|min:0',
            'bathrooms_count' => 'nullable|integer|min:0',
            'built_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'additional_notes' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive',
        ]);

        try {
            $property->update($validated);
            return redirect()->route('business-partner.properties.show', $property->id)
                ->with('success', 'Property updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update property: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified property
     */
    public function destroyProperty(Request $request, $id)
    {
        $partner = $this->getCurrentPartner($request);
        if (!$partner) {
            return redirect()->route('partner.dashboard')->with('error', 'No partner found.');
        }

        $property = $partner->ownedProperties()->findOrFail($id);

        // Check if property has any inspection requests
        if ($property->inspectionRequests()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete property that has inspection requests.');
        }

        try {
            $property->delete();
            return redirect()->route('business-partner.properties.index')
                ->with('success', 'Property deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete property: ' . $e->getMessage());
        }
    }

    /**
     * Display reports
     */
    public function reports(Request $request)
    {
        $partner = $this->getCurrentPartner($request);
        if (!$partner) {
            return redirect()->route('partner.dashboard')->with('error', 'No partner found.');
        }
        return view('business-partner.reports.index', compact('partner'));
    }

    /**
     * Update the status of a property (inline from table)
     */
    public function updatePropertyStatus(Request $request, $id)
    {
        $partner = $this->getCurrentPartner($request);
        if (!$partner) {
            return redirect()->route('partner.dashboard')->with('error', 'No partner found.');
        }
        $property = $partner->ownedProperties()->findOrFail($id);
        $validated = $request->validate([
            'status' => 'required|in:active,inactive',
        ]);
        $property->status = $validated['status'];
        $property->save();
        return redirect()->back()->with('success', 'Property status updated successfully.');
    }

    /**
     * Show the form for editing an inspection request
     */
    public function editInspectionRequest(Request $request, $id)
    {
        $partner = $this->getCurrentPartner($request);
        if (!$partner) {
            return redirect()->route('partner.dashboard')->with('error', 'No partner found.');
        }
        $inspectionRequest = $partner->inspectionRequests()->with(['property', 'client'])->findOrFail($id);
        $clients = $partner->users()->get();
        return view('business-partner.inspection-requests.edit', compact('partner', 'inspectionRequest', 'clients'));
    }

    /**
     * Update the specified inspection request
     */
    public function updateInspectionRequest(Request $request, $id)
    {
        $partner = $this->getCurrentPartner($request);
        if (!$partner) {
            return redirect()->route('partner.dashboard')->with('error', 'No partner found.');
        }
        $inspectionRequest = $partner->inspectionRequests()->findOrFail($id);
        $validated = $request->validate([
            'preferred_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string',
            'client_id' => 'nullable|exists:users,id',
        ]);
        $inspectionRequest->preferred_date = $validated['preferred_date'];
        $inspectionRequest->special_instructions = $validated['notes'] ?? null;
        $inspectionRequest->client_id = $validated['client_id'] ?? null;
        $inspectionRequest->save();
        return redirect()->route('business-partner.inspection-requests.index')->with('success', 'Inspection request updated successfully.');
    }

    /**
     * Show a specific inspection report for the business partner and allow download
     */
    public function showReport(Request $request, $reportId)
    {
        $partner = $this->getCurrentPartner($request);
        if (!$partner) {
            return redirect()->route('partner.dashboard')->with('error', 'No partner found.');
        }
        $report = \App\Models\InspectionReport::with(['inspectionRequest.property', 'inspectionRequest.package', 'inspectionRequest.inspector.user'])
            ->findOrFail($reportId);
        // Ensure the report belongs to this partner
        if ($report->inspectionRequest->business_partner_id !== $partner->id) {
            abort(403, 'You do not have access to this report.');
        }
        return view('business-partner.reports.show', compact('partner', 'report'));
    }

    /**
     * Download a specific inspection report as PDF for the business partner
     */
    public function downloadReport(Request $request, $reportId)
    {
        $partner = $this->getCurrentPartner($request);
        if (!$partner) {
            return redirect()->route('partner.dashboard')->with('error', 'No partner found.');
        }
        
        $report = \App\Models\InspectionReport::with(['inspectionRequest.property', 'inspectionRequest.package.services', 'inspectionRequest.inspector.user'])
            ->findOrFail($reportId);
            
        // Ensure the report belongs to this partner
        if ($report->inspectionRequest->business_partner_id !== $partner->id) {
            abort(403, 'You do not have access to this report.');
        }

        if ($report->status !== 'completed') {
            return redirect()->back()->with('error', 'Only completed reports can be downloaded.');
        }

        $services = $report->inspectionRequest->package->services;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('inspectors.reports.pdf', compact('report', 'services'));
        
        return $pdf->download('inspection-report-'.$report->inspectionRequest->request_number.'.pdf');
    }
} 