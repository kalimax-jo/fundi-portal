<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\InspectionStatusHistory;
use App\Models\Inspector;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing records that contain "Inspector ID" references
        $records = InspectionStatusHistory::where('change_reason', 'like', '%Inspector ID%')->get();
        
        foreach ($records as $record) {
            // Extract the old and new inspector IDs from the change_reason
            if (preg_match('/Inspector ID: (\d+) to ID: (\d+)/', $record->change_reason, $matches)) {
                $oldInspectorId = $matches[1];
                $newInspectorId = $matches[2];
                
                // Get inspector names
                $oldInspector = Inspector::with('user')->find($oldInspectorId);
                $newInspector = Inspector::with('user')->find($newInspectorId);
                
                $oldName = $oldInspector && $oldInspector->user ? $oldInspector->user->full_name : 'Unknown Inspector';
                $newName = $newInspector && $newInspector->user ? $newInspector->user->full_name : 'Unknown Inspector';
                
                // Update the change_reason with names
                $newReason = "Inspector reassigned from {$oldName} to {$newName}";
                
                $record->update(['change_reason' => $newReason]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration updates data, so we can't easily reverse it
        // The data would be lost if we tried to reverse it
    }
};
