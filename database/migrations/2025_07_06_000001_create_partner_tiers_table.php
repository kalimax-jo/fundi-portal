<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partner_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_partner_id')->constrained('business_partners')->onDelete('cascade');
            $table->foreignId('tier_id')->constrained('tiers')->onDelete('cascade');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_tiers');
    }
}; 