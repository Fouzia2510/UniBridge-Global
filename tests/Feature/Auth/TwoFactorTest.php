<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TwoFactorTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        return User::factory()->create([
            'role' => User::ROLE_STUDENT,
            'is_approved' => true,
        ]);
    }

    private function prepareOtpSession(
        string $otp = '123456',
        int $attempts = 0,
        ?string $expiresAt = null,
        ?string $sentAt = null,
    ): array {
        return [
            'two_factor_verified' => false,
            'two_factor_otp_hash' => Hash::make($otp),
            'two_factor_otp_expires_at' =>
                $expiresAt ?? now()->addMinutes(5)->toDateTimeString(),
            'two_factor_otp_sent_at' =>
                $sentAt ?? now()->subSeconds(61)->toDateTimeString(),
            'two_factor_otp_attempts' => $attempts,
        ];
    }

    public function test_valid_otp_verifies_user_and_clears_otp(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)
            ->withSession($this->prepareOtpSession())
            ->post(route('two-factor.verify.store'), [
                'otp' => '123456',
            ]);

        $response->assertRedirect($user->dashboardRoute());

        $this->assertTrue(session('two_factor_verified'));
        $this->assertFalse(session()->has('two_factor_otp_hash'));
        $this->assertFalse(session()->has('two_factor_otp_expires_at'));
        $this->assertFalse(session()->has('two_factor_otp_attempts'));
    }

    public function test_incorrect_otp_increments_attempt_count(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)
            ->withSession($this->prepareOtpSession())
            ->post(route('two-factor.verify.store'), [
                'otp' => '654321',
            ]);

        $response->assertSessionHasErrors('otp');
        $response->assertSessionHas('two_factor_otp_attempts', 1);
        $response->assertSessionHas('two_factor_otp_hash');
    }

    public function test_fifth_incorrect_attempt_clears_otp(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)
            ->withSession($this->prepareOtpSession(attempts: 4))
            ->post(route('two-factor.verify.store'), [
                'otp' => '654321',
            ]);

        $response->assertSessionHasErrors('otp');
        $response->assertSessionMissing('two_factor_otp_hash');
        $response->assertSessionMissing('two_factor_otp_expires_at');
        $response->assertSessionMissing('two_factor_otp_attempts');
    }

    public function test_expired_otp_is_rejected_and_cleared(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)
            ->withSession($this->prepareOtpSession(
                expiresAt: now()->subSecond()->toDateTimeString()
            ))
            ->post(route('two-factor.verify.store'), [
                'otp' => '123456',
            ]);

        $response->assertSessionHasErrors('otp');
        $response->assertSessionMissing('two_factor_otp_hash');
    }

    public function test_resend_is_blocked_during_sixty_second_cooldown(): void
    {
        $user = $this->makeUser();

        $session = $this->prepareOtpSession(
            sentAt: now()->subSeconds(30)->toDateTimeString()
        );

        $response = $this->actingAs($user)
            ->withSession($session)
            ->post(route('two-factor.resend'));

        $response->assertSessionHasErrors('otp');
        $response->assertSessionHas('two_factor_otp_hash');
    }

    public function test_otp_must_contain_six_digits(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)
            ->withSession($this->prepareOtpSession())
            ->post(route('two-factor.verify.store'), [
                'otp' => '123',
            ]);

        $response->assertSessionHasErrors('otp');
    }
}
