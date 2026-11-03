<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect('/dashboard');
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/login')->assertOk()->assertSee('Masuk dengan akun PAS');
    }

    public function test_mock_pas_account_can_login_without_storing_pas_password(): void
    {
        config()->set('services.pas.driver', 'mock');
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
