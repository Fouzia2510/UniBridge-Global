<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    private function strongTestPassword(): string
    {
        return 'UniBridge!' . random_int(100000, 999999);
    }

    public function test_password_can_be_updated(): void
    {
        $user = User::factory()->create();
        $newPassword = $this->strongTestPassword();

        $response = $this
            ->actingAs($user)
            ->withSession(['two_factor_verified' => true])
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'password',
                'password' => $newPassword,
                'password_confirmation' => $newPassword,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertTrue(
            Hash::check($newPassword, $user->refresh()->password)
        );
    }

    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        $user = User::factory()->create();
        $newPassword = $this->strongTestPassword();

        $response = $this
            ->actingAs($user)
            ->withSession(['two_factor_verified' => true])
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'wrong-password',
                'password' => $newPassword,
                'password_confirmation' => $newPassword,
            ]);

        $response
            ->assertSessionHasErrorsIn(
                'updatePassword',
                'current_password'
            )
            ->assertRedirect('/profile');
    }

    public function test_password_confirmation_must_match(): void
    {
        $user = User::factory()->create();
        $newPassword = $this->strongTestPassword();

        $this->actingAs($user)
            ->withSession(['two_factor_verified' => true])
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'password',
                'password' => $newPassword,
                'password_confirmation' => 'DifferentPassword123!',
            ])
            ->assertSessionHasErrorsIn(
                'updatePassword',
                'password'
            );

        $this->assertTrue(
            Hash::check('password', $user->refresh()->password)
        );
    }

    public function test_weak_password_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession(['two_factor_verified' => true])
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'password',
                'password' => '123',
                'password_confirmation' => '123',
            ])
            ->assertSessionHasErrorsIn(
                'updatePassword',
                'password'
            );

        $this->assertTrue(
            Hash::check('password', $user->refresh()->password)
        );
    }

    public function test_unauthenticated_user_cannot_update_password(): void
    {
        $newPassword = $this->strongTestPassword();

        $this->put('/password', [
            'current_password' => 'password',
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ])->assertRedirect('/login');
    }

    public function test_session_with_old_password_hash_is_rejected(): void
    {
        $user = User::factory()->create();

        $oldSessionPasswordHash = Hash::make('old-password');

        $response = $this
            ->actingAs($user)
            ->withSession([
                'two_factor_verified' => true,
                'password_hash_web' => $oldSessionPasswordHash,
            ])
            ->get('/profile');

        $response->assertRedirect('/login');

        $this->assertGuest();
    }

    public function test_current_session_remains_authenticated_after_password_update(): void
    {
        $user = User::factory()->create();
        $newPassword = $this->strongTestPassword();

        $this->actingAs($user)
            ->withSession(['two_factor_verified' => true]);

        $this->get('/profile');

        $this->assertTrue(
            session()->has('password_hash_web')
        );

        $this->put('/password', [
            'current_password' => 'password',
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ])->assertSessionHasNoErrors();

        $this->get('/profile');

        $this->assertAuthenticatedAs($user);
    }
}