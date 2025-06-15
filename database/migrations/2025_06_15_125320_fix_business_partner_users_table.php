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
        // Update business_partners table
        Schema::table('business_partners', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('business_partners', 'email')) {
                $table->string('email')->nullable()->after('name');
            }
            if (!Schema::hasColumn('business_partners', 'phone')) {
                $table->string('phone', 20)->nullable()->after('email');
            }
            if (!Schema::hasColumn('business_partners', 'website')) {
                $table->string('website')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('business_partners', 'city')) {
                $table->string('city', 100)->nullable()->after('address');
            }
            if (!Schema::hasColumn('business_partners', 'country')) {
                $table->string('country', 100)->default('Rwanda')->after('city');
            }
            if (!Schema::hasColumn('business_partners', 'tier')) {
                $table->enum('tier', ['bronze', 'silver', 'gold', 'platinum'])->default('bronze')->after('type');
            }
            if (!Schema::hasColumn('business_partners', 'billing_cycle')) {
                $table->enum('billing_cycle', ['monthly', 'quarterly', 'annually'])->default('monthly')->after('billing_type');
            }
            if (!Schema::hasColumn('business_partners', 'credit_limit')) {
                $table->decimal('credit_limit', 15, 2)->nullable()->after('discount_percentage');
            }
            if (!Schema::hasColumn('business_partners', 'contract_end_date')) {
                $table->date('contract_end_date')->nullable()->after('partnership_start_date');
            }
            if (!Schema::hasColumn('business_partners', 'notes')) {
                $table->text('notes')->nullable();
            }
        });

        // Fix business_partner_users table - add missing columns
        Schema::table('business_partner_users', function (Blueprint $table) {
            if (!Schema::hasColumn('business_partner_users', 'added_by')) {
                $table->foreignId('added_by')->nullable()->constrained('users')->onDelete('set null')->after('is_primary_contact');
            }
            if (!Schema::hasColumn('business_partner_users', 'added_at')) {
                $table->timestamp('added_at')->nullable()->after('added_by');
            }
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
            if (Schema::hasColumn('business_partner_users', 'added_by')) {
                $table->dropForeign(['added_by']);
                $table->dropColumn('added_by');
            }
            if (Schema::hasColumn('business_partner_users', 'added_at')) {
                $table->dropColumn('added_at');
            }
        });
    }
};