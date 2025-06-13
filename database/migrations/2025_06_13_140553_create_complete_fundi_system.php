<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Roles table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('display_name', 100);
            $table->text('description')->nullable();
            $table->json('permissions')->nullable();
            $table->timestamps();
        });

        // 2. User roles table
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->timestamp('assigned_at')->useCurrent();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->unique(['user_id', 'role_id']);
        });

        // 3. Business partners table
        Schema::create('business_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['bank', 'insurance', 'microfinance', 'mortgage', 'investment']);
            $table->string('license_number', 100)->nullable();
            $table->string('registration_number', 100)->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('logo')->nullable();
            $table->date('partnership_start_date')->nullable();
            $table->enum('partnership_status', ['active', 'inactive', 'suspended'])->default('active');
            $table->enum('billing_type', ['monthly', 'per_inspection', 'custom'])->default('monthly');
            $table->decimal('discount_percentage', 5, 2)->default(0.00);
            $table->timestamps();
            $table->index(['type']);
            $table->index(['partnership_status']);
        });

        // 4. Business partner users table
        Schema::create('business_partner_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_partner_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('position', 100)->nullable();
            $table->string('department', 100)->nullable();
            $table->enum('access_level', ['admin', 'user', 'viewer'])->default('user');
            $table->boolean('is_primary_contact')->default(false);
            $table->timestamps();
            $table->unique(['business_partner_id', 'user_id']);
        });

        // 5. Properties table
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('property_code', 20)->unique();
            $table->string('owner_name')->nullable();
            $table->string('owner_phone', 20)->nullable();
            $table->string('owner_email')->nullable();
            $table->enum('property_type', ['residential', 'commercial', 'industrial', 'mixed']);
            $table->string('property_subtype', 100)->nullable();
            $table->text('address');
            $table->string('district', 100)->nullable();
            $table->string('sector', 100)->nullable();
            $table->string('cell', 100)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('built_year')->nullable();
            $table->decimal('total_area_sqm', 10, 2)->nullable();
            $table->integer('floors_count')->default(1);
            $table->integer('bedrooms_count')->nullable();
            $table->integer('bathrooms_count')->nullable();
            $table->decimal('market_value', 15, 2)->nullable();
            $table->date('last_inspection_date')->nullable();
            $table->json('property_photos')->nullable();
            $table->text('additional_notes')->nullable();
            $table->timestamps();
            $table->index(['property_type']);
            $table->index(['district', 'sector']);
            $table->index(['latitude', 'longitude']);
        });

        // 6. Inspection packages table
        Schema::create('inspection_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('RWF');
            $table->integer('duration_hours')->default(4);
            $table->boolean('is_custom_quote')->default(false);
            $table->enum('target_client_type', ['individual', 'business', 'both'])->default('both');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['target_client_type']);
            $table->index(['is_active']);
        });

        // 7. Inspection services table
        Schema::create('inspection_services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('category', ['exterior', 'interior', 'plumbing', 'electrical', 'foundation', 'environmental', 'safety']);
            $table->json('requires_equipment')->nullable();
            $table->integer('estimated_duration_minutes')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['category']);
        });

        // 8. Package services table
        Schema::create('package_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('inspection_packages')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('inspection_services')->onDelete('cascade');
            $table->boolean('is_mandatory')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['package_id', 'service_id']);
        });

        // 9. Inspectors table
        Schema::create('inspectors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('inspector_code', 20)->unique();
            $table->enum('certification_level', ['basic', 'advanced', 'expert'])->default('basic');
            $table->json('specializations')->nullable();
            $table->integer('experience_years')->default(0);
            $table->date('certification_expiry')->nullable();
            $table->json('equipment_assigned')->nullable();
            $table->enum('availability_status', ['available', 'busy', 'offline'])->default('available');
            $table->decimal('current_location_lat', 10, 8)->nullable();
            $table->decimal('current_location_lng', 11, 8)->nullable();
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('total_inspections')->default(0);
            $table->timestamps();
            $table->index(['availability_status']);
            $table->index(['rating']);
            $table->index(['current_location_lat', 'current_location_lng']);
        });

        // 10. Inspector certifications table
        Schema::create('inspector_certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspector_id')->constrained()->onDelete('cascade');
            $table->string('certification_name');
            $table->string('issuing_body')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('certificate_file')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 11. Inspection requests table
        Schema::create('inspection_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number', 20)->unique();
            $table->enum('requester_type', ['individual', 'business_partner']);
            $table->foreignId('requester_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('business_partner_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('package_id')->constrained('inspection_packages')->onDelete('restrict');
            $table->enum('purpose', ['rental', 'sale', 'purchase', 'loan_collateral', 'insurance', 'maintenance', 'other']);
            $table->enum('urgency', ['normal', 'urgent', 'emergency'])->default('normal');
            $table->date('preferred_date')->nullable();
            $table->enum('preferred_time_slot', ['morning', 'afternoon', 'evening', 'flexible'])->default('flexible');
            $table->text('special_instructions')->nullable();
            $table->decimal('loan_amount', 15, 2)->nullable();
            $table->string('loan_reference', 100)->nullable();
            $table->string('applicant_name')->nullable();
            $table->string('applicant_phone', 20)->nullable();
            $table->enum('status', ['pending', 'assigned', 'in_progress', 'completed', 'cancelled', 'on_hold'])->default('pending');
            $table->foreignId('assigned_inspector_id')->nullable()->constrained('inspectors')->onDelete('set null');
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('assigned_at')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->time('scheduled_time')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->decimal('total_cost', 10, 2)->default(0.00);
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'refunded'])->default('pending');
            $table->timestamps();
            $table->index(['status']);
            $table->index(['requester_type', 'requester_user_id']);
            $table->index(['assigned_inspector_id']);
            $table->index(['scheduled_date']);
        });

        // 12. Inspection status history table
        Schema::create('inspection_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_request_id')->constrained()->onDelete('cascade');
            $table->string('old_status', 50)->nullable();
            $table->string('new_status', 50);
            $table->foreignId('changed_by')->constrained('users')->onDelete('cascade');
            $table->text('change_reason')->nullable();
            $table->timestamp('changed_at')->useCurrent();
        });

        // 13. Payments table
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_request_id')->constrained()->onDelete('cascade');
            $table->string('transaction_reference', 100)->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('RWF');
            $table->enum('payment_method', ['mtn_momo', 'airtel_money', 'visa', 'mastercard', 'bank_transfer']);
            $table->string('gateway_provider', 50)->nullable();
            $table->string('gateway_transaction_id')->nullable();
            $table->string('gateway_reference')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded'])->default('pending');
            $table->text('failure_reason')->nullable();
            $table->string('payer_name')->nullable();
            $table->string('payer_phone', 20)->nullable();
            $table->string('payer_email')->nullable();
            $table->timestamp('initiated_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->index(['status']);
            $table->index(['transaction_reference']);
            $table->index(['gateway_transaction_id']);
        });

        // 14. Payment logs table
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->onDelete('cascade');
            $table->string('action');
            $table->json('request_data')->nullable();
            $table->json('response_data')->nullable();
            $table->string('status_before')->nullable();
            $table->string('status_after')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('logged_at')->useCurrent();
        });

        // 15. System settings table
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key_name', 100)->unique();
            $table->text('value')->nullable();
            $table->enum('data_type', ['string', 'integer', 'decimal', 'boolean', 'json'])->default('string');
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false);
            $table->string('group_name', 50)->default('general');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 16. Notification templates table
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->enum('type', ['email', 'sms', 'push', 'whatsapp']);
            $table->string('subject')->nullable();
            $table->text('body');
            $table->json('variables')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 17. Notifications table
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('template_id')->nullable()->constrained('notification_templates')->onDelete('set null');
            $table->enum('type', ['email', 'sms', 'push', 'whatsapp']);
            $table->string('subject')->nullable();
            $table->text('message');
            $table->string('recipient');
            $table->enum('status', ['pending', 'sent', 'delivered', 'failed', 'bounced'])->default('pending');
            $table->integer('delivery_attempts')->default(0);
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->string('related_type', 100)->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
            $table->timestamps();
        });

        // Insert default data
        $this->insertDefaultData();
    }

    /**
     * Insert default data
     */
    private function insertDefaultData(): void
    {
        // Insert default roles
        DB::table('roles')->insert([
            [
                'name' => 'admin',
                'display_name' => 'System Administrator',
                'description' => 'Full system access and management',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'individual_client',
                'display_name' => 'Individual Client',
                'description' => 'Property owners and individual users',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'business_partner',
                'display_name' => 'Business Partner',
                'description' => 'Financial institutions and corporate clients',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'head_technician',
                'display_name' => 'Head of Technician',
                'description' => 'Operations manager for inspections',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'inspector',
                'display_name' => 'Certified Inspector',
                'description' => 'Field inspection personnel',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Insert default inspection packages
        DB::table('inspection_packages')->insert([
            [
                'name' => 'A_CHECK',
                'display_name' => 'A-Check Package',
                'description' => 'Exterior, interior, plumbing, electrical, air, and fire safety. Recommended before and after rental.',
                'price' => 200000.00,
                'currency' => 'RWF',
                'duration_hours' => 3,
                'is_custom_quote' => false,
                'target_client_type' => 'both',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'B_CHECK',
                'display_name' => 'B-Check Package',
                'description' => 'A-Check + foundation cracks, garden trees, fence, and flooding risks. Recommended for buy or sell.',
                'price' => 400000.00,
                'currency' => 'RWF',
                'duration_hours' => 5,
                'is_custom_quote' => false,
                'target_client_type' => 'both',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'C_CHECK',
                'display_name' => 'C-Check Package',
                'description' => 'Comprehensive inspection (A+B), environmental hazards, and septic tank. Recommended every 5 years.',
                'price' => 0.00,
                'currency' => 'RWF',
                'duration_hours' => 8,
                'is_custom_quote' => true,
                'target_client_type' => 'both',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'notifications',
            'notification_templates',
            'system_settings',
            'payment_logs',
            'payments',
            'inspection_status_history',
            'inspection_requests',
            'inspector_certifications',
            'inspectors',
            'package_services',
            'inspection_services',
            'inspection_packages',
            'properties',
            'business_partner_users',
            'business_partners',
            'user_roles',
            'roles'
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }
};