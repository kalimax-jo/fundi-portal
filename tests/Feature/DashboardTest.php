<?php

namespace Tests\Feature;

use Tests\TestCase;

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
