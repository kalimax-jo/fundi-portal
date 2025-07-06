<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tier;
use App\Models\InspectionPackage;
use Illuminate\Http\Request;

class TierController extends Controller
{
    public function index()
    {
        $tiers = Tier::with('inspectionPackages')->get();
        return view('admin.tiers.index', compact('tiers'));
    }

    public function create()
    {
        $packages = InspectionPackage::active()->forBusinessPartners()->get();
        return view('admin.tiers.create', compact('packages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'request_quota' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'inspection_packages' => 'required|array',
            'inspection_packages.*' => 'exists:inspection_packages,id',
        ]);

        $tier = Tier::create($validated);
        $tier->inspectionPackages()->sync($validated['inspection_packages']);

        return redirect()->route('admin.tiers.index')->with('success', 'Tier created successfully.');
    }

    public function show(Tier $tier)
    {
        $tier->load('inspectionPackages');
        return view('admin.tiers.show', compact('tier'));
    }

    public function edit(Tier $tier)
    {
        $packages = InspectionPackage::active()->forBusinessPartners()->get();
        $tier->load('inspectionPackages');
        return view('admin.tiers.edit', compact('tier', 'packages'));
    }

    public function update(Request $request, Tier $tier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'request_quota' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'inspection_packages' => 'required|array',
            'inspection_packages.*' => 'exists:inspection_packages,id',
        ]);

        $tier->update($validated);
        $tier->inspectionPackages()->sync($validated['inspection_packages']);

        return redirect()->route('admin.tiers.index')->with('success', 'Tier updated successfully.');
    }

    public function destroy(Tier $tier)
    {
        $tier->delete();
        return redirect()->route('admin.tiers.index')->with('success', 'Tier deleted successfully.');
    }
} 