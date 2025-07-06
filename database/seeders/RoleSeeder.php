<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
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

        $this->command->info('Roles seeded successfully!');
    }
} 