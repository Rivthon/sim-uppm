<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_can_open_home_and_protected_dashboard_redirects_to_login(): void
    {
        $this->get('/')->assertOk()->assertSee('Masuk Portal');
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/login')->assertOk()->assertSee('Masuk dengan akun PAS');
    }

    public function test_configured_local_admin_can_login_while_database_pas_driver_is_active(): void
    {
        config()->set('services.pas.driver', 'database');
        config()->set('services.pas.local_admin_enabled', true);
        config()->set('services.pas.mock_username', 'admin.uppm');
        config()->set('services.pas.mock_password', 'uppm12345');

        $this->post('/login', [
            'username' => 'admin.uppm',
            'password' => 'uppm12345',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'pas_user_id' => 'PAS-DEMO-001',
            'username' => 'admin.uppm',
            'password' => null,
        ]);
    }
}
