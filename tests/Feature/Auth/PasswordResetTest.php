<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    private function strongTestPassword(): string
    {
        return 'UniBridge!' . bin2hex(random_bytes(8));
    }

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        Notification::assertSentTo(
            $user,
            ResetPassword::class,
            function ($notification) {
                $response = $this->get(
                    '/reset-password/' . $notification->token
                );

                $response->assertStatus(200);

                return true;
            }
        );
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $newPassword = $this->strongTestPassword();

        $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        Notification::assertSentTo(
            $user,
            ResetPassword::class,
            function ($notification) use ($user, $newPassword) {
                $response = $this->post('/reset-password', [
                    'token' => $notification->token,
                    'email' => $user->email,
                    'password' => $newPassword,
                    'password_confirmation' => $newPassword,
                ]);

                $response
                    ->assertSessionHasNoErrors()
                    ->assertRedirect(route('login'));

                $user->refresh();

                $this->assertTrue(
                    Hash::check($newPassword, $user->password)
                );

                $this->assertDatabaseMissing('password_reset_tokens', [
                    'email' => $user->email,
                ]);

                return true;
            }
        );
    }

    public function test_invalid_reset_token_is_rejected(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $originalPassword = $user->password;
        $newPassword = $this->strongTestPassword();

        $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        $this->post('/reset-password', [
            'token' => 'invalid-reset-token',
            'email' => $user->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ])->assertSessionHasErrors('email');

        $user->refresh();

        $this->assertSame($originalPassword, $user->password);
    }

    public function test_used_reset_token_cannot_be_reused(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $firstPassword = $this->strongTestPassword();
        $secondPassword = $this->strongTestPassword();

        $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        Notification::assertSentTo(
            $user,
            ResetPassword::class,
            function ($notification) use (
                $user,
                $firstPassword,
                $secondPassword
            ) {
                $firstResponse = $this->post('/reset-password', [
                    'token' => $notification->token,
                    'email' => $user->email,
                    'password' => $firstPassword,
                    'password_confirmation' => $firstPassword,
                ]);

                $firstResponse
                    ->assertSessionHasNoErrors()
                    ->assertRedirect(route('login'));

                $secondResponse = $this->post('/reset-password', [
                    'token' => $notification->token,
                    'email' => $user->email,
                    'password' => $secondPassword,
                    'password_confirmation' => $secondPassword,
                ]);

                $secondResponse->assertSessionHasErrors('email');

                $user->refresh();

                $this->assertTrue(
                    Hash::check($firstPassword, $user->password)
                );

                return true;
            }
        );
    }
}
