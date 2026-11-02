<?php

namespace Tests\Feature;

use App\Services\WhatsAppAIResponder;
use App\services\GeminiService;
use Mockery;
use Tests\TestCase;

class WhatsAppAIResponderTest extends TestCase
{
    public function test_english_personal_application_question_is_blocked(): void
    {
        $service = Mockery::mock(GeminiService::class);
        $service->shouldNotReceive('generate');
        $this->app->instance(GeminiService::class, $service);

        $reply = app(WhatsAppAIResponder::class)
            ->generateReply('What is my application status?');

        $this->assertStringContainsString('For your privacy', $reply);
    }

    public function test_roman_urdu_personal_fee_question_is_blocked(): void
    {
        $service = Mockery::mock(GeminiService::class);
        $service->shouldNotReceive('generate');
        $this->app->instance(GeminiService::class, $service);

        $reply = app(WhatsAppAIResponder::class)
            ->generateReply('Meri fee ka status kya hai?');

        $this->assertStringContainsString('Aapki privacy', $reply);
    }

    public function test_urdu_script_personal_fee_question_is_blocked(): void
    {
        $service = Mockery::mock(GeminiService::class);
        $service->shouldNotReceive('generate');
        $this->app->instance(GeminiService::class, $service);

        $reply = app(WhatsAppAIResponder::class)
            ->generateReply('میری فیس کا کیا اسٹیٹس ہے؟');

        $this->assertStringContainsString('Aapki privacy', $reply);
    }

    public function test_cnic_question_is_blocked(): void
    {
        $service = Mockery::mock(GeminiService::class);
        $service->shouldNotReceive('generate');
        $this->app->instance(GeminiService::class, $service);

        $reply = app(WhatsAppAIResponder::class)
            ->generateReply('Mera CNIC kya hai?');

        $this->assertStringContainsString('Aapki privacy', $reply);
    }

    public function test_otp_message_is_blocked(): void
    {
        $service = Mockery::mock(GeminiService::class);
        $service->shouldNotReceive('generate');
        $this->app->instance(GeminiService::class, $service);

        $reply = app(WhatsAppAIResponder::class)
            ->generateReply('Mera OTP 123456 hai');

        $this->assertStringContainsString('Aapki privacy', $reply);
    }

    public function test_general_question_uses_mocked_gemini_service(): void
    {
        $service = Mockery::mock(GeminiService::class);

        $service->shouldReceive('generate')
            ->once()
            ->with(Mockery::type('string'))
            ->andReturn('You can explore our study abroad courses.');

        $this->app->instance(GeminiService::class, $service);

        $reply = app(WhatsAppAIResponder::class)
            ->generateReply('What courses does UniBridge offer?');

        $this->assertSame(
            'You can explore our study abroad courses.',
            $reply
        );
    }
}
