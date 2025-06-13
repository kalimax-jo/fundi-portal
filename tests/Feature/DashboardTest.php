<?php

namespace Tests\Feature;

use Tests\TestCase;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Authenticated users can access the dashboard.
     */
    public function test_authenticated_user_can_view_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');


class DashboardTest extends TestCase
{
    /**
     * Ensure the dashboard page is accessible.
     */
    public function test_dashboard_page_is_accessible(): void
    {
        $response = $this->get('/dashboard');


        $response->assertStatus(200);
        $response->assertSee('User Dashboard');
    }
}
