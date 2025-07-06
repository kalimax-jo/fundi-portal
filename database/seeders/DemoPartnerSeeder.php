<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BusinessPartner;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DemoPartnerSeeder extends Seeder
{
    public function run(): void
    {
        // Check if demo partner already exists
        $partner = BusinessPartner::where('subdomain', 'demopartner')->first();
        
        if (!$partner) {
            // Create demo business partner
            $partner = BusinessPartner::create([
                'name' => 'Demo Partner',
                'subdomain' => 'demopartner',
                'email' => 'demo@partner.com',
                'phone' => '123456789',
                'type' => 'bank',
                'tier' => 'gold',
                'contact_person' => 'Demo Admin',
                'contact_email' => 'admin@demopartner.com',
                'contact_phone' => '123456789',
                'address' => '123 Demo St',
                'city' => 'Demo City',
                'country' => 'DemoLand',
                'partnership_start_date' => now(),
                'partnership_status' => 'active',
                'billing_type' => 'monthly',
                'billing_cycle' => 'monthly',
                'discount_percentage' => 0.00,
            ]);
            echo "Created new demo partner.\n";
        } else {
            // Update existing partner
            $partner->update([
                'name' => 'Demo Partner',
                'email' => 'demo@partner.com',
                'phone' => '123456789',
                'type' => 'bank',
                'tier' => 'gold',
                'contact_person' => 'Demo Admin',
                'contact_email' => 'admin@demopartner.com',
                'contact_phone' => '123456789',
                'address' => '123 Demo St',
                'city' => 'Demo City',
                'country' => 'DemoLand',
                'partnership_status' => 'active',
                'billing_type' => 'monthly',
                'billing_cycle' => 'monthly',
                'discount_percentage' => 0.00,
            ]);
            echo "Updated existing demo partner.\n";
        }

        // Check if demo user already exists
        $user = User::where('email', 'user@demopartner.com')->first();
        
        if (!$user) {
            // Create demo user
            $user = User::create([
                'first_name' => 'Demo',
                'last_name' => 'User',
                'email' => 'user@demopartner.com',
                'password' => Hash::make('password123'),
                'status' => 'active',
            ]);
            echo "Created new demo user.\n";
        } else {
            // Update existing user password
            $user->update([
                'first_name' => 'Demo',
                'last_name' => 'User',
                'password' => Hash::make('password123'),
                'status' => 'active',
            ]);
            echo "Updated existing demo user.\n";
        }

        // Associate user with partner (remove existing association first)
        $partner->users()->detach($user->id);
        $partner->users()->attach($user->id, [
            'access_level' => 'admin',
            'is_primary_contact' => true,
        ]);

        echo "Demo partner and user setup completed!\n";
        echo "Partner ID: " . $partner->id . "\n";
        echo "User ID: " . $user->id . "\n";
        echo "Login URL: http://demopartner.fundi.info/partner/login\n";
        echo "Email: user@demopartner.com\n";
        echo "Password: password123\n";
    }
} 