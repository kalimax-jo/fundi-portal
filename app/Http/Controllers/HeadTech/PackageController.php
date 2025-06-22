<?php

namespace App\Http\Controllers\HeadTech;

use App\Http\Controllers\Controller;
use App\Models\InspectionPackage;
use App\Models\InspectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PackageController extends Controller
{
    /**
     * Display a listing of packages
     */
    public function index(Request $request)
    {
        $query = InspectionPackage::with('services');

        // Search functionality
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('display_name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $packages = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => InspectionPackage::count(),
            'active' => InspectionPackage::where('is_active', true)->count(),
            'inactive' => InspectionPackage::where('is_active', false)->count(),
            'fixed_price' => InspectionPackage::where('is_custom_quote', false)->count(),
            'custom_quote' => InspectionPackage::where('is_custom_quote', true)->count(),
        ];

        return view('headtech.packages.index', compact('packages', 'stats'));
    }

    /**
     * Show the form for creating a new package
     */
    public function create()
    {
        $services = InspectionService::where('is_active', true)->orderBy('category')->orderBy('name')->get();
        $clientTypes = [
            'individual' => 'Individual Clients',
            'business_partner' => 'Business Partners',
            'both' => 'Both',
        ];
        return view('headtech.packages.create', compact('services', 'clientTypes'));
    }

    /**
     * Store a newly created package
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:inspection_packages,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'is_custom_quote' => 'boolean',
            'target_client_type' => 'required|in:individual,business_partner,both',
            'is_active' => 'boolean',
            'services' => 'nullable|array',
            'services.*' => 'exists:inspection_services,id',
        ]);

        $package = InspectionPackage::create($validated);

            if ($request->has('services')) {
            $package->services()->sync($request->input('services'));
            }

        return redirect()->route('headtech.packages.index')->with('success', 'Package created successfully.');
    }

    /**
     * Display the specified package
     */
    public function show(InspectionPackage $package)
    {
        $package->load([
            'services', 
            'inspectionRequests' => function ($query) {
                $query->with(['requester', 'businessPartner'])->latest()->take(10);
            }
        ]);
        
        return view('headtech.packages.show', compact('package'));
    }

    /**
     * Show the form for editing the package
     */
    public function edit(InspectionPackage $package)
    {
        $services = InspectionService::where('is_active', true)->orderBy('category')->orderBy('name')->get();
        $clientTypes = [
            'individual' => 'Individual Clients',
            'business_partner' => 'Business Partners',
            'both' => 'Both',
        ];
        $packageServiceIds = $package->services()->pluck('inspection_services.id')->toArray();
        
        return view('headtech.packages.edit', compact('package', 'services', 'clientTypes', 'packageServiceIds'));
    }

    /**
     * Update the specified package
     */
    public function update(Request $request, InspectionPackage $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:inspection_packages,name,' . $package->id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'is_custom_quote' => 'boolean',
            'target_client_type' => 'required|in:individual,business_partner,both',
            'is_active' => 'boolean',
            'services' => 'nullable|array',
            'services.*' => 'exists:inspection_services,id',
        ]);

        $package->update($validated);

        if ($request->has('services')) {
            $package->services()->sync($request->input('services'));
        } else {
            $package->services()->detach();
        }

        return redirect()->route('headtech.packages.show', $package)->with('success', 'Package updated successfully.');
    }

    /**
     * Remove the specified package
     */
    public function destroy(InspectionPackage $package)
    {
            if ($package->inspectionRequests()->exists()) {
            return redirect()->route('headtech.packages.index')->with('error', 'Cannot delete a package that has been used in inspection requests.');
            }
            $package->services()->detach();
            $package->delete();
        return redirect()->route('headtech.packages.index')->with('success', 'Package deleted successfully.');
    }

    /**
     * Toggle the active status of a package
     */
    public function toggleStatus(InspectionPackage $package)
    {
            $package->update(['is_active' => !$package->is_active]);
        return back()->with('success', 'Package status updated.');
    }

    /**
     * Add a service to a package
     */
    public function addService(Request $request, InspectionPackage $package)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:inspection_services,id',
            'is_mandatory' => 'boolean',
        ]);
        
        $package->services()->syncWithoutDetaching([
            $validated['service_id'] => ['is_mandatory' => $validated['is_mandatory'] ?? false]
            ]);

        return back()->with('success', 'Service added to package.');
    }

    /**
     * Remove a service from a package
     */
    public function removeService(InspectionPackage $package, InspectionService $service)
    {
            $package->services()->detach($service->id);
        return back()->with('success', 'Service removed from package.');
    }

    /**
     * Update a service within a package (e.g., toggle mandatory status)
     */
    public function updateService(Request $request, InspectionPackage $package, InspectionService $service)
    {
        $validated = $request->validate([
            'is_mandatory' => 'required|boolean',
        ]);

            $package->services()->updateExistingPivot($service->id, [
            'is_mandatory' => $validated['is_mandatory'],
            ]);

        return back()->with('success', 'Service updated in package.');
    }
} 