<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class BusinessPartnerRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $businessPartnerRoles = [
            [
                'name' => 'business_partner_admin',
                'display_name' => 'Business Partner Admin',
                'description' => 'Administrator within a business partner organization. Can manage users, view all data, and manage settings.',
                'permissions' => [
                    'manage_users',
                    'view_reports',
                    'manage_billing',
                    'create_inspection_requests',
                    'view_all_data',
                    'manage_settings'
                ]
            ],
            [
                'name' => 'loan_officer',
                'display_name' => 'Loan Officer',
                'description' => 'Can create and manage inspection requests for loan applications.',
                'permissions' => [
                    'create_inspection_requests',
                    'view_own_requests',
                    'view_properties',
                    'download_reports'
                ]
            ],
            [
                'name' => 'billing_manager',
                'display_name' => 'Billing Manager',
                'description' => 'Can view and manage billing information, payments, and financial reports.',
                'permissions' => [
                    'view_billing',
                    'manage_payments',
                    'view_financial_reports',
                    'view_inspection_requests'
                ]
            ],
            [
                'name' => 'property_manager',
                'display_name' => 'Property Manager',
                'description' => 'Can manage properties and view property-related inspection reports.',
                'permissions' => [
                    'manage_properties',
                    'view_property_reports',
                    'create_inspection_requests',
                    'view_own_requests'
                ]
            ],
            [
                'name' => 'viewer',
                'display_name' => 'Viewer',
                'description' => 'Read-only access to inspection requests and reports.',
                'permissions' => [
                    'view_inspection_requests',
                    'view_reports',
                    'download_reports'
                ]
            ]
        ];

        foreach ($businessPartnerRoles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']], 
                [
                    'display_name' => $role['display_name'],
                    'description' => $role['description'],
                    'permissions' => json_encode($role['permissions'])
                ]
            );
        }

        $this->command->info('Business Partner roles seeded successfully!');
    }
} 