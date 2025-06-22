<?php

namespace App\Http\Controllers\Admin;

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
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by client type
        if ($request->has('client_type') && $request->client_type) {
            $query->where('target_client_type', $request->client_type);
        }

        // Filter by pricing type
        if ($request->has('pricing_type') && $request->pricing_type) {
            if ($request->pricing_type === 'fixed') {
                $query->where('is_custom_quote', false);
            } else {
                $query->where('is_custom_quote', true);
            }
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status === 'active');
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
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

        return view('admin.packages.index', compact('packages', 'stats'));
    }

    /**
     * Show the form for creating a new package
     */
    public function create()
    {
        $services = InspectionService::active()->get();
        $clientTypes = InspectionPackage::getTargetClientTypes();
        
        return view('admin.packages.create', compact('services', 'clientTypes'));
    }

    /**
     * Store a newly created package
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:inspection_packages',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'duration_hours' => 'required|integer|min:1',
            'is_custom_quote' => 'boolean',
            'target_client_type' => 'required|in:individual,business,both',
            'is_active' => 'boolean',
            'services' => 'nullable|array',
            'services.*' => 'exists:inspection_services,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $package = InspectionPackage::create([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'description' => $request->description,
                'price' => $request->price,
                'currency' => $request->currency,
                'duration_hours' => $request->duration_hours,
                'is_custom_quote' => $request->boolean('is_custom_quote'),
                'target_client_type' => $request->target_client_type,
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Attach services if provided
            if ($request->has('services')) {
                foreach ($request->services as $index => $serviceId) {
                    $package->services()->attach($serviceId, [
                        'is_mandatory' => true,
                        'sort_order' => $index
                    ]);
                }
            }

            return redirect()->route('admin.packages.index')
                ->with('success', 'Package created successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create package: ' . $e->getMessage())
                ->withInput();
        }
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
        
        return view('admin.packages.show', compact('package'));
    }

    /**
     * Show the form for editing the package
     */
    public function edit(InspectionPackage $package)
    {
        $package->load('services');
        $services = InspectionService::active()->get();
        $clientTypes = InspectionPackage::getTargetClientTypes();
        
        return view('admin.packages.edit', compact('package', 'services', 'clientTypes'));
    }

    /**
     * Update the specified package
     */
    public function update(Request $request, InspectionPackage $package)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:inspection_packages,name,' . $package->id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'duration_hours' => 'required|integer|min:1',
            'is_custom_quote' => 'boolean',
            'target_client_type' => 'required|in:individual,business,both',
            'is_active' => 'boolean',
            'services' => 'nullable|array',
            'services.*' => 'exists:inspection_services,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $package->update([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'description' => $request->description,
                'price' => $request->price,
                'currency' => $request->currency,
                'duration_hours' => $request->duration_hours,
                'is_custom_quote' => $request->boolean('is_custom_quote'),
                'target_client_type' => $request->target_client_type,
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Update services
            $package->services()->detach();
            if ($request->has('services')) {
                foreach ($request->services as $index => $serviceId) {
                    $package->services()->attach($serviceId, [
                        'is_mandatory' => true,
                        'sort_order' => $index
                    ]);
                }
            }

            return redirect()->route('admin.packages.show', $package)
                ->with('success', 'Package updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update package: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified package
     */
    public function destroy(InspectionPackage $package)
    {
        try {
            // Check if package has associated requests
            if ($package->inspectionRequests()->exists()) {
                return redirect()->back()
                    ->with('error', 'Cannot delete package that has associated inspection requests.');
            }

            $package->services()->detach();
            $package->delete();

            return redirect()->route('admin.packages.index')
                ->with('success', 'Package deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete package: ' . $e->getMessage());
        }
    }

    /**
     * Toggle package status
     */
    public function toggleStatus(InspectionPackage $package)
    {
        try {
            $package->update(['is_active' => !$package->is_active]);

            return response()->json([
                'success' => true,
                'message' => 'Package status updated successfully.',
                'is_active' => $package->is_active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update package status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show package analytics
     */
    public function analytics()
    {
        $analytics = [
            'total_packages' => InspectionPackage::count(),
            'active_packages' => InspectionPackage::where('is_active', true)->count(),
            'custom_quote_packages' => InspectionPackage::where('is_custom_quote', true)->count(),
            'fixed_price_packages' => InspectionPackage::where('is_custom_quote', false)->count(),
        ];

        return view('admin.packages.analytics', compact('analytics'));
    }

    /**
     * Add service to package
     */
    public function addService(Request $request, InspectionPackage $package)
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:inspection_services,id',
            'is_mandatory' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $package->services()->attach($request->service_id, [
                'is_mandatory' => $request->boolean('is_mandatory', true),
                'sort_order' => $request->get('sort_order', 0),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Service added to package successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add service: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove service from package
     */
    public function removeService(InspectionPackage $package, InspectionService $service)
    {
        try {
            $package->services()->detach($service->id);

            return response()->json([
                'success' => true,
                'message' => 'Service removed from package successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove service: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update service in package
     */
    public function updateService(Request $request, InspectionPackage $package, InspectionService $service)
    {
        $validator = Validator::make($request->all(), [
            'is_mandatory' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $package->services()->updateExistingPivot($service->id, [
                'is_mandatory' => $request->boolean('is_mandatory', true),
                'sort_order' => $request->get('sort_order', 0),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Service updated in package successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service: ' . $e->getMessage()
            ], 500);
        }
    }
} 