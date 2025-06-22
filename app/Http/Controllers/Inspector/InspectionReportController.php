<?php

namespace App\Http\Controllers\Inspector;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InspectionRequest;
use App\Models\InspectionReport;
use Illuminate\Support\Facades\Auth;

class InspectionReportController extends Controller
{
    // Show the inspection report form (create or edit)
    public function showForm($requestId)
    {
        $user = Auth::user();
        $inspector = $user->inspector;
        $request = InspectionRequest::with(['package.services', 'property', 'requester', 'businessPartner'])
            ->where('id', $requestId)
            ->where('assigned_inspector_id', $inspector->id)
            ->firstOrFail();

        $report = InspectionReport::firstOrCreate([
            'inspection_request_id' => $request->id,
            'inspector_id' => $inspector->id,
        ]);

        $services = $request->package ? $request->package->services : collect();

        return view('inspectors.reports.form', compact('request', 'report', 'services'));
    }

    // Auto-save report data (AJAX)
    public function autoSave(Request $request, $reportId)
    {
        $user = Auth::user();
        $report = InspectionReport::where('id', $reportId)
            ->where('inspector_id', $user->inspector->id)
            ->firstOrFail();
        $data = $request->input('data', []);
        $progress = $request->input('progress', 0);
        $report->data = $data;
        $report->progress = $progress;
        $report->save();
        return response()->json(['success' => true]);
    }

    // Complete the report
    public function complete(Request $request, $reportId)
    {
        $user = Auth::user();
        $report = InspectionReport::where('id', $reportId)
            ->where('inspector_id', $user->inspector->id)
            ->firstOrFail();

        $inspectionRequest = $report->inspectionRequest;
        $services = $inspectionRequest->package->services;
        $data = $report->data ?? [];

        foreach ($services as $service) {
            $notesKey = 'service_notes_' . $service->id;
            $photoKey = 'service_photo_' . $service->id;

            if ($request->has($notesKey)) {
                $data[$notesKey] = $request->input($notesKey);
            }

            if ($request->hasFile($photoKey)) {
                $path = $request->file($photoKey)->store('reports/' . $report->id, 'public');
                $data[$photoKey] = $path;
            }
        }
        
        $data['general_comments'] = $request->input('general_comments');

        $report->data = $data;
        $report->status = 'completed';
        $report->progress = 100;
        $report->completed_at = now();
        $report->save();

        // Mark the inspection request as completed
        $inspectionRequest->status = 'completed';
        $inspectionRequest->completed_at = now();
        $inspectionRequest->save();

        return redirect()->route('inspector.dashboard')->with('success', 'Inspection report completed.');
    }
}
