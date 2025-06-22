<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InspectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    /**
     * Display a listing of services
     */
    public function index(Request $request)
    {
        $query = InspectionService::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status === 'active');
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $services = $query->paginate(15)->withQueryString();

        // Get categories for filter
        $categories = InspectionService::distinct()
            ->pluck('category')
            ->mapWithKeys(function ($category) {
                return [$category => ['name' => ucfirst(str_replace('_', ' ', $category))]];
            })
            ->sort();

        $stats = [
            'total' => InspectionService::count(),
            'active' => InspectionService::where('is_active', true)->count(),
            'inactive' => InspectionService::where('is_active', false)->count(),
            'by_category' => InspectionService::query()
                ->select('category', DB::raw('count(*) as count'))
                ->groupBy('category')
                ->pluck('count', 'category'),
        ];

        return view('admin.services.index', compact('services', 'categories', 'stats'));
    }

    /**
     * Show the form for creating a new service
     */
    public function create()
    {
        $categories = [
            'exterior' => 'Exterior',
            'interior' => 'Interior',
            'plumbing' => 'Plumbing',
            'electrical' => 'Electrical',
            'foundation' => 'Foundation',
            'environmental' => 'Environmental',
            'safety' => 'Safety'
        ];

        $equipment = [
            'moisture_meter' => 'Moisture Meter',
            'thermal_camera' => 'Thermal Camera',
            'electrical_tester' => 'Electrical Tester',
            'camera' => 'Camera',
            'ladder' => 'Ladder',
            'safety_equipment' => 'Safety Equipment'
        ];

        return view('admin.services.create', compact('categories', 'equipment'));
    }

    /**
     * Store a newly created service
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:inspection_services',
            'description' => 'nullable|string',
            'category' => 'required|in:exterior,interior,plumbing,electrical,foundation,environmental,safety',
            'requires_equipment' => 'nullable|array',
            'requires_equipment.*' => 'string',
            'estimated_duration_minutes' => 'required|integer|min:15|max:480',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $service = InspectionService::create([
                'name' => $request->name,
                'description' => $request->description,
                'category' => $request->category,
                'requires_equipment' => $request->requires_equipment ?? [],
                'estimated_duration_minutes' => $request->estimated_duration_minutes,
                'is_active' => $request->boolean('is_active', true),
            ]);

            return redirect()->route('admin.services.index')
                ->with('success', 'Service created successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create service: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified service
     */
    public function show(InspectionService $service)
    {
        $service->load('packages');
        $usageStats = $service->getUsageStats();
        
        return view('admin.services.show', compact('service', 'usageStats'));
    }

    /**
     * Show the form for editing the service
     */
    public function edit(InspectionService $service)
    {
        $categories = [
            'exterior' => 'Exterior',
            'interior' => 'Interior',
            'plumbing' => 'Plumbing',
            'electrical' => 'Electrical',
            'foundation' => 'Foundation',
            'environmental' => 'Environmental',
            'safety' => 'Safety'
        ];

        $equipment = [
            'moisture_meter' => 'Moisture Meter',
            'thermal_camera' => 'Thermal Camera',
            'electrical_tester' => 'Electrical Tester',
            'camera' => 'Camera',
            'ladder' => 'Ladder',
            'safety_equipment' => 'Safety Equipment'
        ];

        return view('admin.services.edit', compact('service', 'categories', 'equipment'));
    }

    /**
     * Update the specified service
     */
    public function update(Request $request, InspectionService $service)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:inspection_services,name,' . $service->id,
            'description' => 'nullable|string',
            'category' => 'required|in:exterior,interior,plumbing,electrical,foundation,environmental,safety',
            'requires_equipment' => 'nullable|array',
            'requires_equipment.*' => 'string',
            'estimated_duration_minutes' => 'required|integer|min:15|max:480',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $service->update([
                'name' => $request->name,
                'description' => $request->description,
                'category' => $request->category,
                'requires_equipment' => $request->requires_equipment ?? [],
                'estimated_duration_minutes' => $request->estimated_duration_minutes,
                'is_active' => $request->boolean('is_active', true),
            ]);

            return redirect()->route('admin.services.show', $service)
                ->with('success', 'Service updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update service: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified service
     */
    public function destroy(InspectionService $service)
    {
        try {
            // Check if service is used in any packages
            if ($service->packages()->exists()) {
                return redirect()->back()
                    ->with('error', 'Cannot delete service that is included in packages.');
            }

            $service->delete();

            return redirect()->route('admin.services.index')
                ->with('success', 'Service deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete service: ' . $e->getMessage());
        }
    }

    /**
     * Toggle service status
     */
    public function toggleStatus(InspectionService $service)
    {
        try {
            $service->update(['is_active' => !$service->is_active]);

            return response()->json([
                'success' => true,
                'message' => 'Service status updated successfully.',
                'is_active' => $service->is_active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show service analytics
     */
    public function analytics()
    {
        $analytics = [
            'total_services' => InspectionService::count(),
            'active_services' => InspectionService::where('is_active', true)->count(),
            'services_by_category' => InspectionService::selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->get()
                ->pluck('count', 'category'),
        ];

        return view('admin.services.analytics', compact('analytics'));
    }

    /**
     * Show services by category
     */
    public function byCategory($category)
    {
        $services = InspectionService::where('category', $category)
            ->orderBy('name')
            ->paginate(15);

        $categoryName = ucfirst($category);

        return view('admin.services.by-category', compact('services', 'categoryName'));
    }

    /**
     * Show services by equipment
     */
    public function byEquipment($equipment)
    {
        $services = InspectionService::whereJsonContains('requires_equipment', $equipment)
            ->orderBy('name')
            ->paginate(15);

        $equipmentName = str_replace('_', ' ', ucfirst($equipment));

        return view('admin.services.by-equipment', compact('services', 'equipmentName'));
    }

    /**
     * Bulk update services
     */
    public function bulkUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_ids' => 'required|array',
            'service_ids.*' => 'exists:inspection_services,id',
            'action' => 'required|in:activate,deactivate,delete',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $services = InspectionService::whereIn('id', $request->service_ids);

            switch ($request->action) {
                case 'activate':
                    $services->update(['is_active' => true]);
                    $message = 'Services activated successfully.';
                    break;
                case 'deactivate':
                    $services->update(['is_active' => false]);
                    $message = 'Services deactivated successfully.';
                    break;
                case 'delete':
                    // Check if any services are used in packages
                    $usedServices = $services->whereHas('packages')->pluck('name');
                    if ($usedServices->count() > 0) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Cannot delete services that are included in packages: ' . $usedServices->implode(', ')
                        ], 400);
                    }
                    $services->delete();
                    $message = 'Services deleted successfully.';
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform bulk update: ' . $e->getMessage()
            ], 500);
        }
    }
} 