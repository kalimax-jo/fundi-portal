<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Roles
        $this->command->info('Seeding roles...');
        $roles = [
            ['name' => 'admin', 'display_name' => 'Administrator'],
            ['name' => 'head_technician', 'display_name' => 'Head Technician'],
            ['name' => 'inspector', 'display_name' => 'Inspector'],
            ['name' => 'client', 'display_name' => 'Client'],
            ['name' => 'business_partner', 'display_name' => 'Business Partner'],
        ];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }

        // 2. Create Admin User
        $this->command->info('Seeding admin user...');
        $adminRole = Role::where('name', 'admin')->first();
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@fundi.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'password' => bcrypt('password'),
                'status' => 'active',
            ]
        );
        if (!$adminUser->roles->contains($adminRole->id)) {
            $adminUser->roles()->attach($adminRole->id);
        }

        // 3. Call other seeders
        $this->command->info('Calling other seeders...');
        $this->call([
            AssignHeadTechnicianRoleSeeder::class,
            ClientUserSeeder::class,
            InspectionPackageSeeder::class,
            InspectionServiceSeeder::class,
        ]);

        $this->command->info('Database seeding completed successfully.');
    }
}
