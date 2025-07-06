<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class AssignHeadTechnicianRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create a dedicated Head Technician User
        $headTechEmail = 'headtech@fundi.com';

        $headTechUser = User::firstOrCreate(
            ['email' => $headTechEmail],
            [
                'first_name' => 'Head',
                'last_name' => 'Technician',
                'password' => bcrypt('password'),
                'status' => 'active',
            ]
        );

        $role = Role::where('name', 'head_technician')->first();

        if ($role && !$headTechUser->roles->contains($role->id)) {
            $headTechUser->roles()->attach($role->id);
            $this->command->info("Created Head Technician user ({$headTechEmail}) and assigned role.");
        } else if (!$role) {
            $this->command->error('Head Technician role not found. Please run RoleSeeder first.');
        } else {
            $this->command->info("Head Technician user ({$headTechEmail}) already has the role.");
        }
    }
} 