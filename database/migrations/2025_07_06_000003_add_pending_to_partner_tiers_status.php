<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Modify the enum to add 'pending' (works for MySQL >= 8.0.13)
        DB::statement("ALTER TABLE partner_tiers MODIFY status ENUM('pending', 'active', 'expired', 'cancelled') DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Revert to original enum
        DB::statement("ALTER TABLE partner_tiers MODIFY status ENUM('active', 'expired', 'cancelled') DEFAULT 'active'");
    }
}; 