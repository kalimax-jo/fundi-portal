<?php

namespace App\Http\Controllers;

use App\Models\InspectionPackage;
use App\Models\InspectionRequest;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class InspectionRequestController extends Controller
{
    /**
     * Show the form for creating a new inspection request.
     */
    public function create()
    {
        $user = auth()->user();
        
        // Check if user is individual client - fallback for admin users
        $isIndividual = $user->isIndividualClient();
        
        // For admin users, default to business partner mode (can select existing properties)
        if ($user->isAdmin()) {
            $isIndividual = false;
        }

        $packages = InspectionPackage::active()->get();
        $properties = collect();
        $businessPartners = collect();
        $propertyTypes = [];
        $districts = [];

        if ($isIndividual) {
            // For individual clients
            $propertyTypes = [
                'residential' => 'Residential',
                'commercial' => 'Commercial', 
                'industrial' => 'Industrial',
                'mixed' => 'Mixed Use'
            ];
            
            $districts = [
                'Gasabo', 'Kicukiro', 'Nyarugenge', 'Bugesera', 'Gatsibo', 
                'Kayonza', 'Kirehe', 'Ngoma', 'Rwamagana', 'Burera', 
                'Gakenke', 'Gicumbi', 'Musanze', 'Rulindo', 'Gisagara', 
                'Huye', 'Kamonyi', 'Muhanga', 'Nyamagabe', 'Nyanza', 
                'Nyaruguru', 'Ruhango', 'Karongi', 'Ngororero', 'Nyabihu', 
                'Nyamasheke', 'Rubavu', 'Rusizi', 'Rutsiro'
            ];
        } else {
            // For business partners and admins
            $properties = Property::all();
            
            // Try to get business partners, but don't fail if relationship doesn't exist
            try {
                if ($user->isAdmin()) {
                    // Admins can see all business partners
                    $businessPartners = \App\Models\BusinessPartner::where('status', 'active')->get();
                } else {
                    $businessPartners = $user->businessPartners()->where('status', 'active')->get();
                }
            } catch (\Exception $e) {
                $businessPartners = collect();
            }
        }

        return view('inspection-requests.create', [
            'packages' => $packages,
            'properties' => $properties,
            'businessPartners' => $businessPartners,
            'isIndividual' => $isIndividual,
            'propertyTypes' => $propertyTypes,
            'districts' => $districts,
        ]);
    }

    /**
     * Store a newly created inspection request.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $isIndividual = $user->isIndividualClient();
        
        // For admin users, determine mode based on form data
        if ($user->isAdmin()) {
            $isIndividual = !$request->has('property_id') || empty($request->property_id);
        }

        // Base validation rules
        $rules = [
            'package_id' => 'required|exists:inspection_packages,id',
            'purpose' => 'required|in:rental,sale,purchase,loan_collateral,insurance,maintenance,other',
            'urgency' => 'required|in:normal,urgent,emergency',
            'preferred_date' => 'nullable|date|after:today',
            'preferred_time_slot' => 'required|in:morning,afternoon,evening,flexible',
            'special_instructions' => 'nullable|string|max:1000',
            'loan_amount' => 'nullable|numeric|min:0',
            'loan_reference' => 'nullable|string|max:100',
            'applicant_name' => 'nullable|string|max:255',
            'applicant_phone' => 'nullable|string|max:20',
        ];

        // Add conditional rules for individual clients
        if ($isIndividual) {
            $rules = array_merge($rules, [
                'address' => 'required|string|max:255',
                'district' => 'required|string|max:100',
                'sector' => 'nullable|string|max:100',
                'cell' => 'nullable|string|max:100',
                'property_type' => 'required|in:residential,commercial,industrial,mixed',
                'property_size' => 'nullable|numeric|min:0',
                'bedrooms' => 'nullable|integer|min:0',
                'bathrooms' => 'nullable|integer|min:0',
                'construction_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
                'property_condition' => 'nullable|in:excellent,good,fair,poor,under_construction',
                'property_description' => 'nullable|string|max:1000',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
            ]);
        } else {
            $rules = array_merge($rules, [
                'property_id' => 'required|exists:properties,id',
            ]);
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $package = InspectionPackage::findOrFail($request->package_id);
            $propertyId = $request->property_id;

            // Create property for individual clients or when no property is selected
            if ($isIndividual || empty($propertyId)) {
                $property = Property::create([
                    'property_code' => $this->generatePropertyCode(),
                    'owner_name' => $user->full_name ?? ($user->first_name . ' ' . $user->last_name),
                    'owner_phone' => $user->phone,
                    'owner_email' => $user->email,
                    'address' => $request->address,
                    'district' => $request->district,
                    'sector' => $request->sector,
                    'cell' => $request->cell,
                    'property_type' => $request->property_type,
                    'total_area_sqm' => $request->property_size,
                    'bedrooms_count' => $request->bedrooms,
                    'bathrooms_count' => $request->bathrooms,
                    'built_year' => $request->construction_year,
                    'additional_notes' => $request->property_description,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                ]);
                $propertyId = $property->id;
            }

            // Determine requester type
            $requesterType = 'individual';
            $businessPartnerId = null;
            
            if ($user->isBusinessPartner()) {
                $requesterType = 'business_partner';
                $businessPartnerId = $request->business_partner_id;
            } elseif ($user->isAdmin()) {
                // For admin, default to individual unless business partner is specified
                if ($request->business_partner_id) {
                    $requesterType = 'business_partner';
                    $businessPartnerId = $request->business_partner_id;
                }
            }

            // Create inspection request
            $inspectionRequest = InspectionRequest::create([
                'request_number' => $this->generateRequestNumber(),
                'requester_type' => $requesterType,
                'requester_user_id' => $user->id,
                'business_partner_id' => $businessPartnerId,
                'property_id' => $propertyId,
                'package_id' => $package->id,
                'purpose' => $request->purpose,
                'urgency' => $request->urgency,
                'preferred_date' => $request->preferred_date,
                'preferred_time_slot' => $request->preferred_time_slot,
                'special_instructions' => $request->special_instructions,
                'loan_amount' => $request->loan_amount,
                'loan_reference' => $request->loan_reference,
                'applicant_name' => $request->applicant_name,
                'applicant_phone' => $request->applicant_phone,
                'status' => 'pending',
                'total_cost' => $package->price,
                'payment_status' => 'pending',
            ]);

            DB::commit();

            return redirect()->route('dashboard')->with('success', 'Inspection request submitted successfully. Request number: ' . $inspectionRequest->request_number);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to submit request: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Generate a unique request number
     */
    private function generateRequestNumber()
    {
        $date = Carbon::now()->format('Ymd');
        $sequence = InspectionRequest::whereDate('created_at', Carbon::today())->count() + 1;
        return 'REQ' . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate a unique property code
     */
    private function generatePropertyCode()
    {
        $prefix = 'PROP';
        $year = date('Y');
        
        $lastProperty = Property::where('property_code', 'like', "{$prefix}{$year}-%")
            ->orderBy('property_code', 'desc')
            ->first();
        
        if ($lastProperty) {
            $lastNumber = (int) substr($lastProperty->property_code, -4);
            $sequence = $lastNumber + 1;
        } else {
            $sequence = 1;
        }
        
        return sprintf('%s%s-%04d', $prefix, $year, $sequence);
    }

    /**
     * Show user's inspection requests
     */
    public function myRequests()
    {
        $user = auth()->user();
        
        $requests = $user->inspectionRequests()
            ->with(['package', 'property', 'report'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('inspection-requests.my-requests', compact('requests'));
    }

    /**
     * Show user's properties
     */
    public function myProperties()
    {
        $user = auth()->user();

        // A user's properties can be linked by email or phone.
        $properties = Property::where('owner_email', $user->email)
            ->orWhere('owner_phone', $user->phone)
            ->withCount('inspectionRequests')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('properties.my-properties', compact('properties'));
    }

    /**
     * Show user profile
     */
    public function profile()
    {
        $user = auth()->user();
        return view('profile.index', compact('user'));
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);
        $user->update($request->only('first_name', 'last_name', 'email', 'phone'));
        return redirect()->route('profile')->with('success', 'Profile updated successfully.');
    }

    public function show(InspectionRequest $inspectionRequest)
    {
        // Ensure the request belongs to the authenticated user or an admin
        if ($inspectionRequest->requester_user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $inspectionRequest->load(['package', 'property', 'inspector.user', 'report']);

        return view('inspection-requests.show', compact('inspectionRequest'));
    }

    public function downloadReport(InspectionRequest $inspectionRequest)
    {
        // Ensure the request belongs to the authenticated user
        if ($inspectionRequest->requester_user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $report = $inspectionRequest->report()->firstOrFail();

        if ($report->status !== 'completed') {
            return redirect()->back()->with('error', 'Only completed reports can be downloaded.');
        }
        
        $services = $report->inspectionRequest->package->services;

        // We can reuse the same PDF view from the inspector's section
        $pdf = Pdf::loadView('inspectors.reports.pdf', compact('report', 'services'));
        
        return $pdf->download('inspection-report-'.$report->inspectionRequest->request_number.'.pdf');
    }
}