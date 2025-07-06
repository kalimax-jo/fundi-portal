<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partner_tier_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_tier_id')->constrained('partner_tiers')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->enum('status', ['paid', 'failed', 'pending'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_tier_payments');
    }
}; 