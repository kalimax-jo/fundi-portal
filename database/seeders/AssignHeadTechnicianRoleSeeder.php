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
        // Change this email to your own user email if needed
        $userEmail = 'admin@example.com';

        $user = User::where('email', $userEmail)->first();
        if (!$user) {
            $this->command->error("User with email {$userEmail} not found.");
            return;
        }

        $role = Role::firstOrCreate(['name' => 'head_technician'], ['display_name' => 'Head Technician']);

        if (!$user->roles->contains($role->id)) {
            $user->roles()->attach($role->id);
            $this->command->info("Assigned 'head_technician' role to user {$user->email}.");
        } else {
            $this->command->info("User {$user->email} already has 'head_technician' role.");
        }
    }
} 