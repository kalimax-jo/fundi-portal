<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('business_partners', function (Blueprint $table) {
            $table->enum('deployment_type', ['centralized', 'dedicated'])->default('centralized')->after('partnership_status');
            $table->string('sync_url')->nullable()->after('deployment_type');
            $table->string('api_key')->nullable()->after('sync_url');
            $table->enum('sync_type', ['public_api', 'vpn'])->default('public_api')->after('api_key');
            $table->boolean('failover_active')->default(false)->after('sync_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_partners', function (Blueprint $table) {
            $table->dropColumn(['deployment_type', 'sync_url', 'api_key', 'sync_type', 'failover_active']);
        });
    }
}; 