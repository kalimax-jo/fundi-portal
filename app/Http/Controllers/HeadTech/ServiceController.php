<?php

namespace App\Http\Controllers\HeadTech;

use App\Http\Controllers\Controller;
use App\Models\InspectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    /**
     * Display a listing of services.
     */
    public function index(Request $request)
    {
        $query = InspectionService::query();

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $services = $query->paginate(15)->withQueryString();
        
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

        return view('headtech.services.index', compact('services', 'categories', 'stats'));
    }

    /**
     * Show the form for creating a new service.
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

        return view('headtech.services.create', compact('categories', 'equipment'));
    }

    /**
     * Store a newly created service.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:inspection_services',
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
            'estimated_duration_minutes' => 'required|integer|min:5',
            'is_active' => 'boolean',
        ]);

        InspectionService::create($validated);

        return redirect()->route('headtech.services.index')->with('success', 'Service created successfully.');
    }

    /**
     * Display the specified service.
     */
    public function show(InspectionService $service)
    {
        $service->load('packages');
        $usageStats = $service->getUsageStats();
        
        return view('headtech.services.show', compact('service', 'usageStats'));
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(InspectionService $service)
    {
        // Forcefully decode requires_equipment if it's a JSON string
        if (is_string($service->requires_equipment)) {
            $service->requires_equipment = json_decode($service->requires_equipment, true);
        }
        // Ensure it's an array if it was null or failed to decode
        $service->requires_equipment = $service->requires_equipment ?? [];

        $categories = collect([
            'exterior' => 'Exterior',
            'interior' => 'Interior',
            'plumbing' => 'Plumbing',
            'electrical' => 'Electrical',
            'foundation' => 'Foundation',
            'environmental' => 'Environmental',
            'safety' => 'Safety'
        ])->mapWithKeys(function ($name, $key) {
            return [$key => ['name' => $name]];
        });

        $equipment = [
            'moisture_meter' => 'Moisture Meter',
            'thermal_camera' => 'Thermal Camera',
            'electrical_tester' => 'Electrical Tester',
            'camera' => 'Camera',
            'ladder' => 'Ladder',
            'safety_equipment' => 'Safety Equipment'
        ];

        return view('headtech.services.edit', compact('service', 'categories', 'equipment'));
    }

    /**
     * Update the specified service.
     */
    public function update(Request $request, InspectionService $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:inspection_services,name,' . $service->id,
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
            'requires_equipment' => 'nullable|array',
            'requires_equipment.*' => 'string',
            'estimated_duration_minutes' => 'required|integer|min:5',
            'is_active' => 'boolean',
        ]);
        
        // Handle the checkbox for is_active
        $validated['is_active'] = $request->has('is_active');
        $validated['requires_equipment'] = $request->input('requires_equipment', []);

        $service->update($validated);

        return redirect()->route('headtech.services.show', $service)->with('success', 'Service updated successfully.');
    }

    /**
     * Remove the specified service.
     */
    public function destroy(InspectionService $service)
    {
        if ($service->packages()->exists()) {
            return redirect()->route('headtech.services.index')->with('error', 'Cannot delete service that is part of a package.');
        }
        $service->delete();
        return redirect()->route('headtech.services.index')->with('success', 'Service deleted successfully.');
    }

    /**
     * Toggle the active status of a service.
     */
    public function toggleStatus(InspectionService $service)
    {
        $service->update(['is_active' => !$service->is_active]);
        return back()->with('success', 'Service status updated.');
    }
}