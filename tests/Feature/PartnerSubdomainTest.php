<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\BusinessPartner;
use App\Models\User;

class PartnerSubdomainTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_partner_subdomain_loads_test_dashboard()
    {
        // Create a business partner and user
        $partner = BusinessPartner::factory()->create([
            'subdomain' => 'bankofkigali',
            'is_active' => true,
        ]);
        $user = User::factory()->create();
        $partner->users()->attach($user->id, [
            'is_primary_contact' => true,
            'access_level' => 'admin',
        ]);

        // Simulate login as the partner user
        $this->actingAs($user);

        // Simulate request to the partner subdomain
        $response = $this->get('http://bankofkigali.fundi.info:8000/test-dashboard', [
            'Host' => 'bankofkigali.fundi.info',
        ]);

        $response->assertStatus(200);
        $response->assertSee('Test Partner Dashboard');
        $response->assertSee($user->name);
    }
} 