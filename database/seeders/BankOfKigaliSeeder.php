<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BusinessPartner;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class BankOfKigaliSeeder extends Seeder
{
    public function run(): void
    {
        // Check if Bank of Kigali partner already exists
        $partner = BusinessPartner::where('subdomain', 'bankofkigali')->first();
        
        if (!$partner) {
            // Create Bank of Kigali business partner
            $partner = BusinessPartner::create([
                'name' => 'Bank of Kigali',
                'subdomain' => 'bankofkigali',
                'email' => 'info@bankofkigali.com',
                'phone' => '+250788123456',
                'type' => 'bank',
                'tier' => 'gold',
                'contact_person' => 'Bank Admin',
                'contact_email' => 'admin@bankofkigali.com',
                'contact_phone' => '+250788123456',
                'address' => '123 Kigali St',
                'city' => 'Kigali',
                'country' => 'Rwanda',
                'partnership_start_date' => now(),
                'partnership_status' => 'active',
                'billing_type' => 'monthly',
                'billing_cycle' => 'monthly',
                'discount_percentage' => 0.00,
            ]);
            echo "Created Bank of Kigali partner.\n";
        } else {
            // Update existing partner
            $partner->update([
                'name' => 'Bank of Kigali',
                'email' => 'info@bankofkigali.com',
                'phone' => '+250788123456',
                'type' => 'bank',
                'tier' => 'gold',
                'contact_person' => 'Bank Admin',
                'contact_email' => 'admin@bankofkigali.com',
                'contact_phone' => '+250788123456',
                'address' => '123 Kigali St',
                'city' => 'Kigali',
                'country' => 'Rwanda',
                'partnership_status' => 'active',
                'billing_type' => 'monthly',
                'billing_cycle' => 'monthly',
                'discount_percentage' => 0.00,
            ]);
            echo "Updated existing Bank of Kigali partner.\n";
        }

        // Check if demo user already exists
        $user = User::where('email', 'user@bankofkigali.com')->first();
        
        if (!$user) {
            // Create demo user
            $user = User::create([
                'first_name' => 'Bank',
                'last_name' => 'User',
                'email' => 'user@bankofkigali.com',
                'password' => Hash::make('password123'),
                'status' => 'active',
            ]);
            echo "Created new Bank of Kigali user.\n";
        } else {
            // Update existing user password
            $user->update([
                'first_name' => 'Bank',
                'last_name' => 'User',
                'password' => Hash::make('password123'),
                'status' => 'active',
            ]);
            echo "Updated existing Bank of Kigali user.\n";
        }

        // Associate user with partner (remove existing association first)
        $partner->users()->detach($user->id);
        $partner->users()->attach($user->id, [
            'access_level' => 'admin',
            'is_primary_contact' => true,
        ]);

        echo "Bank of Kigali partner and user setup completed!\n";
        echo "Partner ID: " . $partner->id . "\n";
        echo "User ID: " . $user->id . "\n";
        echo "Login URL: http://bankofkigali.localhost:8000/login\n";
        echo "Email: user@bankofkigali.com\n";
        echo "Password: password123\n";
    }
} 