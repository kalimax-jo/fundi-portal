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
        Schema::table('business_partners', function (Blueprint $table) {
            // Add missing columns that the controller expects
            $table->string('email')->nullable()->after('name');
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('website')->nullable()->after('phone');
            $table->string('city', 100)->nullable()->after('address');
            $table->string('country', 100)->default('Rwanda')->after('city');
            $table->enum('tier', ['bronze', 'silver', 'gold', 'platinum'])->default('bronze')->after('type');
            $table->enum('billing_cycle', ['monthly', 'quarterly', 'annually'])->default('monthly')->after('billing_type');
            $table->decimal('credit_limit', 15, 2)->nullable()->after('discount_percentage');
            $table->date('contract_end_date')->nullable()->after('partnership_start_date');
            $table->text('notes')->nullable()->after('discount_percentage');

            // Update existing columns to match controller expectations
            $table->text('address')->nullable()->change();
        });

        // Also need to update business_partner_users table
        Schema::table('business_partner_users', function (Blueprint $table) {
            $table->foreignId('added_by')->nullable()->constrained('users')->onDelete('set null')->after('is_primary_contact');
            $table->timestamp('added_at')->nullable()->after('added_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_partners', function (Blueprint $table) {
            $table->dropColumn([
                'email', 'phone', 'website', 'city', 'country', 
                'tier', 'billing_cycle', 'credit_limit', 
                'contract_end_date', 'notes'
            ]);
        });

        Schema::table('business_partner_users', function (Blueprint $table) {
            $table->dropForeign(['added_by']);
            $table->dropColumn(['added_by', 'added_at']);
        });
    }
};