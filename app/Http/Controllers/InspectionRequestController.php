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
        $packages = InspectionPackage::active()->get();
        $user = auth()->user();

        $isIndividual = $user->isIndividualClient();

        $properties = collect();
        $businessPartners = collect();
        $propertyTypes = [];
        $districts = [];

        if ($isIndividual) {
            $types = array_keys(Property::getSubtypesByType());
            foreach ($types as $type) {
                $propertyTypes[$type] = ucfirst($type);
            }
            $districts = collect(Property::getRwandaDistricts())->flatten()->toArray();
        } else {
            $properties = Property::all();
            $businessPartners = $user->businessPartners()->active()->get();
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

        $rules = [
            'package_id' => 'required|exists:inspection_packages,id',
            'purpose' => 'required|in:rental,sale,purchase,loan_collateral,insurance,maintenance,other',
            'urgency' => 'required|in:normal,urgent,emergency',
            'preferred_date' => 'nullable|date|after:today',
            'preferred_time_slot' => 'required|in:morning,afternoon,evening,flexible',
            'special_instructions' => 'nullable|string|max:1000',
        ];

        if ($isIndividual) {
            $rules = array_merge($rules, [
                'address' => 'required|string|max:255',
                'district' => 'required|string|max:100',
                'property_type' => 'required|in:residential,commercial,industrial,mixed',
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

            if ($isIndividual) {
                $property = Property::create([
                    'owner_name' => $user->full_name,
                    'owner_phone' => $user->phone,
                    'owner_email' => $user->email,
                    'address' => $request->address,
                    'district' => $request->district,
                    'property_type' => $request->property_type,
                ]);
                $propertyId = $property->id;
            }

            $inspectionRequest = InspectionRequest::create([
                'request_number' => InspectionRequest::generateRequestNumber(),
                'requester_type' => $isIndividual ? 'individual' : 'business_partner',
                'requester_user_id' => $user->id,
                'business_partner_id' => $isIndividual ? null : $request->business_partner_id,
                'property_id' => $propertyId,
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
