<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class ClientUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating demo client user...');
        
        // Create individual client role if it doesn't exist
        $clientRole = Role::firstOrCreate(
            ['name' => 'individual_client'],
            [
                'display_name' => 'Individual Client',
                'description' => 'Property owners and individual users',
                'permissions' => [
                    'create_inspection_requests',
                    'view_own_inspections',
                    'view_inspection_reports',
                    'make_payments',
                    'download_reports',
                    'manage_properties'
                ]
            ]
        );
        
        // Create demo client user
        $clientUser = User::firstOrCreate(
            ['email' => 'client@example.com'],
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'client@example.com',
                'phone' => '+250788123456',
                'password' => Hash::make('password123'),
                'status' => 'active'
            ]
        );
        
        // Assign client role if not already assigned
        if (!$clientUser->hasRole('individual_client')) {
            $clientUser->roles()->attach($clientRole->id, [
                'assigned_at' => now(),
                'assigned_by' => $clientUser->id
            ]);
        }
        
        $this->command->info('Demo client user created successfully!');
        $this->command->info('Email: client@example.com');
        $this->command->info('Password: password123');
    }
} 