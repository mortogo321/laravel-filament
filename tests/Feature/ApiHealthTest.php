<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiHealthTest extends TestCase
{
    public function test_api_health_returns_ok(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertOk()
            ->assertJsonPath('status', 'ok')
            ->assertJsonStructure(['status', 'app', 'time']);
    }

    public function test_framework_up_probe_returns_ok(): void
    {
        $response = $this->get('/up');

        $response->assertOk();
    }

    public function test_welcome_page_returns_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertOk();
    }

    public function test_admin_login_page_is_reachable(): void
    {
        $response = $this->get('/admin/login');

        $response->assertOk();
    }
}
