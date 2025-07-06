<?php

namespace App\Http\Controllers\BusinessPartner;

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
            'user_id' => auth('partner')->id(),
            'is_authenticated' => auth('partner')->check(),
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
            'total_properties' => $partner->properties->count(),
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

        // Quick stats for cards
        $quickStats = [
            'users' => [
                'total' => $stats['total_users'],
                'active' => $stats['active_users'],
                'recent_logins' => $stats['recent_logins'],
                'growth' => $partner->users()->where('created_at', '>=', now()->subDays(30))->count()
            ],
            'requests' => [
                'total' => $stats['total_requests'],
                'pending' => $stats['pending_requests'],
                'completed' => $stats['completed_requests'],
                'this_month' => $stats['this_month_requests']
            ],
            'financial' => [
                'total_spent' => $stats['total_amount_spent'],
                'pending_amount' => $stats['pending_billing_amount'],
                'this_month_spent' => $partner->billings()
                    ->where('status', 'paid')
                    ->whereMonth('paid_at', now()->month)
                    ->whereYear('paid_at', now()->year)
                    ->sum('final_amount')
            ]
        ];

        return view('business-partner.dashboard.index', compact(
            'partner', 
            'stats', 
            'recentRequests', 
            'recentActivities',
            'quickStats'
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
     * Remove a user from the business partner
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
        $currentUser = Auth::user();
        if ($user->id === $currentUser->id) {
            return redirect()->route('partner.users.index')->with('error', 'You cannot remove yourself from the organization.');
        }
        try {
            $partner->users()->detach($user->id);
            return redirect()->route('partner.users.index')->with('success', 'User removed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to remove user: ' . $e->getMessage());
        }
    }

    /**
     * Set a user as primary contact
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
            $partner->users()->updateExistingPivot($user->id, ['is_primary_contact' => false]);
            $partner->users()->updateExistingPivot($user->id, ['is_primary_contact' => true]);
            return redirect()->route('partner.users.index')->with('success', 'Primary contact updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update primary contact: ' . $e->getMessage());
        }
    }

    /**
     * Display inspection requests for the business partner
     */
    public function inspectionRequests(Request $request)
    {
        $partner = $this->getCurrentPartner($request);
        if (!$partner) {
            return redirect()->route('partner.dashboard')->with('error', 'No partner found.');
        }
        $inspectionRequests = $partner->inspectionRequests()
            ->with(['property', 'inspector.user'])
            ->latest()
            ->paginate(15);
        return view('business-partner.inspection-requests.index', compact('partner', 'inspectionRequests'));
    }

    /**
     * Display properties for the business partner
     */
    public function properties(Request $request)
    {
        $partner = $this->getCurrentPartner($request);
        if (!$partner) {
            return redirect()->route('partner.dashboard')->with('error', 'No partner found.');
        }
        $properties = $partner->properties()
            ->with(['inspectionRequests'])
            ->latest()
            ->paginate(15);
        return view('business-partner.properties.index', compact('partner', 'properties'));
    }



    /**
     * Display a single user's details
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
} 