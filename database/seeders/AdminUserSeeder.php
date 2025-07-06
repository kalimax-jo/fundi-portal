<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Find the admin role
        $adminRole = Role::where('name', 'admin')->first();

        if (!$adminRole) {
            $this->command->error('Admin role not found. Please run RoleSeeder first.');
            return;
        }

        // Create the admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@fundi.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'password' => bcrypt('password'),
                'status' => 'active',
            ]
        );

        // Assign the admin role to the user
        if (!$adminUser->roles->contains($adminRole->id)) {
            $adminUser->roles()->attach($adminRole->id);
            $this->command->info('Admin user created and assigned admin role.');
        } else {
            $this->command->info('Admin user already exists and has admin role.');
        }
    }
} 