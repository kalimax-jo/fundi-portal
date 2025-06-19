<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InspectionPackage;

class InspectionPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding default inspection packages...');
        
        InspectionPackage::createDefaultPackages();
        
        $this->command->info('Default inspection packages seeded successfully!');
    }
} 