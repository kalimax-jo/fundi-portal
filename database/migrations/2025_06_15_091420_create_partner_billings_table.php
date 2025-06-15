<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('partner_billings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_partner_id')->constrained()->onDelete('cascade');
            $table->string('billing_reference', 100)->unique();
            $table->enum('billing_period_type', ['monthly', 'quarterly', 'annually', 'custom']);
            $table->date('billing_period_start');
            $table->date('billing_period_end');
            $table->integer('total_inspections')->default(0);
            $table->decimal('base_amount', 15, 2)->default(0.00);
            $table->decimal('discount_amount', 15, 2)->default(0.00);
            $table->decimal('discount_percentage', 5, 2)->default(0.00);
            $table->decimal('tax_amount', 15, 2)->default(0.00);
            $table->decimal('tax_percentage', 5, 2)->default(18.00); // Rwanda VAT
            $table->decimal('final_amount', 15, 2)->default(0.00);
            $table->string('currency', 3)->default('RWF');
            $table->enum('status', ['draft', 'pending', 'sent', 'paid', 'overdue', 'cancelled'])->default('draft');
            $table->date('due_date')->nullable();
            $table->date('sent_date')->nullable();
            $table->date('paid_date')->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->string('payment_reference', 100)->nullable();
            $table->text('notes')->nullable();
            $table->json('inspection_details')->nullable(); // Store inspection breakdown
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['business_partner_id', 'status']);
            $table->index(['billing_period_start', 'billing_period_end']);
            $table->index(['status']);
            $table->index(['due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_billings');
    }
};