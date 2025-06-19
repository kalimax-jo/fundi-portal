<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InspectionService;

class InspectionServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding default inspection services...');
        
        InspectionService::createDefaultServices();
        
        $this->command->info('Default inspection services seeded successfully!');
    }
} 