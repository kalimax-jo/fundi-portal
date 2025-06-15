<?php

namespace App\Http\Controllers;

use App\Models\InspectionPackage;
use App\Models\InspectionRequest;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InspectionRequestController extends Controller
{
    /**
     * Show the form for creating a new inspection request.
     */
    public function create()
    {
        $properties = Property::all();
        $packages = InspectionPackage::active()->get();
        $businessPartners = auth()->user()->businessPartners()->active()->get();

        return view('inspection-requests.create', compact('properties', 'packages', 'businessPartners'));
    }

    /**
     * Store a newly created inspection request.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'property_id' => 'required|exists:properties,id',
            'package_id' => 'required|exists:inspection_packages,id',
            'purpose' => 'required|in:rental,sale,purchase,loan_collateral,insurance,maintenance,other',
            'urgency' => 'required|in:normal,urgent,emergency',
            'preferred_date' => 'nullable|date|after:today',
            'preferred_time_slot' => 'required|in:morning,afternoon,evening,flexible',
            'special_instructions' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $package = InspectionPackage::findOrFail($request->package_id);

            $inspectionRequest = InspectionRequest::create([
                'request_number' => InspectionRequest::generateRequestNumber(),
                'requester_type' => auth()->user()->isBusinessPartner() ? 'business_partner' : 'individual',
                'requester_user_id' => auth()->id(),
                'business_partner_id' => $request->business_partner_id,
                'property_id' => $request->property_id,
                'package_id' => $package->id,
                'purpose' => $request->purpose,
                'urgency' => $request->urgency,
                'preferred_date' => $request->preferred_date,
                'preferred_time_slot' => $request->preferred_time_slot,
                'special_instructions' => $request->special_instructions,
                'status' => 'pending',
                'total_cost' => $package->price,
                'payment_status' => 'pending',
            ]);

            DB::commit();

            return redirect()->route('dashboard')->with('success', 'Inspection request submitted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to submit request: ' . $e->getMessage())->withInput();
        }
    }
}
