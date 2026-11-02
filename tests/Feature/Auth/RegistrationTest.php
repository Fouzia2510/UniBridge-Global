<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => User::ROLE_STUDENT,
            'roll_number' => 'TEST-001',
            'department' => 'Computer Science',
        ]);

        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => User::ROLE_STUDENT,
            'is_approved' => true,
        ]);

        $response->assertRedirect(route('two-factor.verify'));
    }
}