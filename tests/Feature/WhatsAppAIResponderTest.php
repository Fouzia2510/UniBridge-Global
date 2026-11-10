<?php

namespace Tests\Feature;

use App\Services\WhatsAppAIResponder;
use App\services\OllamaService;
use Mockery;
use Tests\TestCase;

class WhatsAppAIResponderTest extends TestCase
{
    public function test_english_personal_application_question_is_blocked(): void
    {
        $service = Mockery::mock(OllamaService::class);
        $service->shouldNotReceive('generate');
        $this->app->instance(OllamaService::class, $service);

        $reply = app(WhatsAppAIResponder::class)
            ->generateReply('What is my application status?');

        $this->assertStringContainsString('For your privacy', $reply);
    }

    public function test_roman_urdu_personal_fee_question_is_blocked(): void
    {
        $service = Mockery::mock(OllamaService::class);
        $service->shouldNotReceive('generate');
        $this->app->instance(OllamaService::class, $service);

        $reply = app(WhatsAppAIResponder::class)
            ->generateReply('Meri fee ka status kya hai?');

        $this->assertStringContainsString('Aapki privacy', $reply);
    }

    public function test_urdu_script_personal_fee_question_is_blocked(): void
    {
        $service = Mockery::mock(OllamaService::class);
        $service->shouldNotReceive('generate');
        $this->app->instance(OllamaService::class, $service);

        $reply = app(WhatsAppAIResponder::class)
            ->generateReply('میری فیس کا کیا اسٹیٹس ہے؟');

        $this->assertStringContainsString('آپ کی رازداری', $reply);
    }

    public function test_cnic_question_is_blocked(): void
    {
        $service = Mockery::mock(OllamaService::class);
        $service->shouldNotReceive('generate');
        $this->app->instance(OllamaService::class, $service);

        $reply = app(WhatsAppAIResponder::class)
            ->generateReply('Mera CNIC kya hai?');

        $this->assertStringContainsString('Aapki privacy', $reply);
    }

    public function test_otp_message_is_blocked(): void
    {
        $service = Mockery::mock(OllamaService::class);
        $service->shouldNotReceive('generate');
        $this->app->instance(OllamaService::class, $service);

        $reply = app(WhatsAppAIResponder::class)
            ->generateReply('Mera OTP 123456 hai');

        $this->assertStringContainsString('Aapki privacy', $reply);
    }

    public function test_general_question_uses_mocked_ollama_service(): void
    {
        $service = Mockery::mock(OllamaService::class);

        $service->shouldReceive('generate')
            ->once()
            ->with(Mockery::type('string'))
            ->andReturn('Compare courses on each university official website.');

        $this->app->instance(OllamaService::class, $service);

        $reply = app(WhatsAppAIResponder::class)
            ->generateReply('How can I start searching for universities in the UK?');

        $this->assertSame(
            'Compare courses on each university official website.',
            $reply
        );
    }
    public function test_generated_fake_website_url_is_blocked(): void
    {
        $service = Mockery::mock(OllamaService::class);
        $service->shouldReceive('generate')
            ->once()
            ->andReturn('Visit https://www.unibridge.org/study-abroad/uk for details.');

        $this->app->instance(OllamaService::class, $service);

        $reply = app(WhatsAppAIResponder::class)
            ->generateReply('How can I find UK universities?');

        $this->assertStringContainsString(
            'I cannot verify this specific information',
            $reply
        );
    }

    public function test_unverified_unibridge_website_claim_is_blocked(): void
    {
        $service = Mockery::mock(OllamaService::class);
        $service->shouldReceive('generate')
            ->once()
            ->andReturn('You can explore the UniBridge website for a comprehensive list.');

        $this->app->instance(OllamaService::class, $service);

        $reply = app(WhatsAppAIResponder::class)
            ->generateReply('How can I find UK universities?');

        $this->assertStringContainsString(
            'I cannot verify this specific information',
            $reply
        );
    }
    public function test_unverified_unibridge_search_tool_claim_is_blocked(): void
    {
        $service = Mockery::mock(OllamaService::class);
        $service->shouldReceive('generate')
            ->once()
            ->andReturn('You can use UniBridge’s search tool to find options.');

        $this->app->instance(OllamaService::class, $service);

        $reply = app(WhatsAppAIResponder::class)
            ->generateReply('How can I find UK universities?');

        $this->assertStringContainsString(
            'I cannot verify this specific information',
            $reply
        );
    }
    public function test_unverified_unibridge_database_claim_is_blocked(): void
    {
        $service = Mockery::mock(OllamaService::class);
        $service->shouldReceive('generate')
            ->once()
            ->andReturn('UniBridge has a vast database of universities.');

        $this->app->instance(OllamaService::class, $service);

        $reply = app(WhatsAppAIResponder::class)
            ->generateReply('How can I find UK universities?');

        $this->assertStringContainsString(
            'I cannot verify this specific information',
            $reply
        );
    }
    public function test_roman_urdu_wrong_language_uses_consultant_fallback(): void
{
    $service = Mockery::mock(OllamaService::class);

    $service->shouldReceive('generate')
        ->twice()
        ->andReturn(
            'You can check the university website.',
            'You can check the university website.'
        );

    $this->app->instance(OllamaService::class, $service);

    $reply = app(WhatsAppAIResponder::class)
        ->generateReply('Mujhe UK mein study karni hai, kya karoon?');

    $this->assertStringContainsString(
        'consultant se rabta karein',
        $reply
    );
}

public function test_urdu_wrong_language_uses_consultant_fallback(): void
{
    $service = Mockery::mock(OllamaService::class);

    $service->shouldReceive('generate')
        ->twice()
        ->andReturn(
            'You can check the university website.',
            'You can check the university website.'
        );

    $this->app->instance(OllamaService::class, $service);

    $reply = app(WhatsAppAIResponder::class)
        ->generateReply('آپ برطانیہ میں تعلیم کیسے حاصل کر سکتے ہیں؟');

    $this->assertStringContainsString(
        'معذرت',
        $reply
    );
}


    public function test_repetitive_urdu_response_uses_consultant_fallback(): void
    {
        $service = Mockery::mock(OllamaService::class);

        $repetitiveReply = 'آپ آپ آپ برطانیہ میں تعلیم حاصل کر سکتے ہیں۔';

        $service->shouldReceive('generate')
            ->twice()
            ->andReturn($repetitiveReply, $repetitiveReply);

        $this->app->instance(OllamaService::class, $service);

        $reply = app(WhatsAppAIResponder::class)
            ->generateReply('آپ برطانیہ میں تعلیم کیسے حاصل کر سکتے ہیں؟');

        $this->assertStringContainsString('معذرت', $reply);
        $this->assertStringContainsString('UniBridge کنسلٹنٹ', $reply);
    }

    public function test_normal_urdu_response_is_accepted_without_retry(): void
    {
        $service = Mockery::mock(OllamaService::class);

        $normalReply = 'آپ برطانیہ میں تعلیم حاصل کرنے کے لیے یونیورسٹیوں کی ویب سائٹس پر داخلے کی شرائط دیکھ سکتے ہیں۔';

        $service->shouldReceive('generate')
            ->once()
            ->andReturn($normalReply);

        $this->app->instance(OllamaService::class, $service);

        $reply = app(WhatsAppAIResponder::class)
            ->generateReply('آپ برطانیہ میں تعلیم کیسے حاصل کر سکتے ہیں؟');

        $this->assertSame($normalReply, $reply);
    }
    private function assertMultilingualPrivacyBlocked(
        string $message,
        string $expected
    ): void {
        $service = Mockery::mock(OllamaService::class);
        $service->shouldNotReceive('generate');

        $this->app->instance(OllamaService::class, $service);

        $reply = app(WhatsAppAIResponder::class)
            ->generateReply($message);

        $this->assertStringContainsString($expected, $reply);
    }

    public function test_chinese_personal_application_question_is_blocked(): void
    {
        $this->assertMultilingualPrivacyBlocked(
            '我的申请状态是什么？',
            '为了保护您的隐私'
        );
    }

    public function test_japanese_personal_application_question_is_blocked(): void
    {
        $this->assertMultilingualPrivacyBlocked(
            '私の申請状況を教えてください。',
            'プライバシー保護'
        );
    }

    public function test_korean_personal_application_question_is_blocked(): void
    {
        $this->assertMultilingualPrivacyBlocked(
            '제 지원 상태를 알려주세요.',
            '개인정보 보호'
        );
    }

    public function test_hindi_personal_application_question_is_blocked(): void
    {
        $this->assertMultilingualPrivacyBlocked(
            'मेरे आवेदन की स्थिति क्या है?',
            'आपकी गोपनीयता'
        );
    }

    public function test_bengali_personal_application_question_is_blocked(): void
    {
        $this->assertMultilingualPrivacyBlocked(
            'আমার আবেদনের অবস্থা কী?',
            'আপনার গোপনীয়তা'
        );
    }

    public function test_portuguese_personal_application_question_is_blocked(): void
    {
        $this->assertMultilingualPrivacyBlocked(
            'Qual é o status da minha candidatura?',
            'Para proteger sua privacidade'
        );
    }

    public function test_italian_personal_application_question_is_blocked(): void
    {
        $this->assertMultilingualPrivacyBlocked(
            'Qual è lo stato della mia domanda?',
            'Per proteggere la tua privacy'
        );
    }

    public function test_turkish_personal_application_question_is_blocked(): void
    {
        $this->assertMultilingualPrivacyBlocked(
            'Başvuru durumum nedir?',
            'Gizliliğinizi korumak'
        );
    }

    public function test_spanish_personal_application_question_is_blocked(): void
    {
        $this->assertMultilingualPrivacyBlocked(
            '¿Cuál es el estado de mi solicitud?',
            'Para proteger su privacidad'
        );
    }

    public function test_french_personal_application_question_is_blocked(): void
    {
        $this->assertMultilingualPrivacyBlocked(
            'Quel est le statut de ma candidature ?',
            'Pour protéger votre vie privée'
        );
    }

    public function test_normal_spanish_education_question_uses_ollama(): void
    {
        $service = Mockery::mock(OllamaService::class);

        $service->shouldReceive('generate')
            ->once()
            ->andReturn('Puede explorar nuestros programas universitarios.');

        $this->app->instance(OllamaService::class, $service);

        $reply = app(WhatsAppAIResponder::class)
            ->generateReply('¿Qué universidades ofrecen cursos de ingeniería en el Reino Unido?');

        $this->assertSame(
            'Puede explorar nuestros programas universitarios.',
            $reply
        );
    }

    public function test_normal_french_education_question_uses_ollama(): void
    {
        $service = Mockery::mock(OllamaService::class);

        $service->shouldReceive('generate')
            ->once()
            ->andReturn('Vous pouvez explorer nos programmes universitaires.');

        $this->app->instance(OllamaService::class, $service);

        $reply = app(WhatsAppAIResponder::class)
            ->generateReply('Quelles universités proposent des cours au Royaume-Uni ?');

        $this->assertSame(
            'Vous pouvez explorer nos programmes universitaires.',
            $reply
        );
    }

    public function test_spanish_university_question_with_mi_is_not_blocked(): void
    {
        $service = Mockery::mock(OllamaService::class);

        $service->shouldReceive('generate')
            ->once()
            ->andReturn('Puede buscar universidades según su área de estudio.');

        $this->app->instance(OllamaService::class, $service);

        $reply = app(WhatsAppAIResponder::class)
            ->generateReply('¿Cómo puedo encontrar mi universidad?');

        $this->assertSame(
            'Puede buscar universidades según su área de estudio.',
            $reply
        );
    }

    public function test_french_university_question_with_mon_is_not_blocked(): void
    {
        $service = Mockery::mock(OllamaService::class);

        $service->shouldReceive('generate')
            ->once()
            ->andReturn('Vous pouvez comparer les universités selon votre domaine.');

        $this->app->instance(OllamaService::class, $service);

        $reply = app(WhatsAppAIResponder::class)
            ->generateReply('Comment choisir mon université ?');

        $this->assertSame(
            'Vous pouvez comparer les universités selon votre domaine.',
            $reply
        );
    }

    public function test_repeated_urdu_sentence_uses_consultant_fallback(): void
    {
        $service = Mockery::mock(OllamaService::class);

        $repetitiveReply = 'آپ یونیورسٹی کی ویب سائٹ دیکھ سکتے ہیں۔ آپ یونیورسٹی کی ویب سائٹ دیکھ سکتے ہیں۔';

        $service->shouldReceive('generate')
            ->twice()
            ->andReturn($repetitiveReply, $repetitiveReply);

        $this->app->instance(OllamaService::class, $service);

        $reply = app(WhatsAppAIResponder::class)
            ->generateReply('آپ یونیورسٹی میں داخلہ کیسے لے سکتے ہیں؟');

        $this->assertStringContainsString('معذرت', $reply);
        $this->assertStringContainsString('UniBridge کنسلٹنٹ', $reply);
    }

    public function test_repeated_roman_urdu_sentence_uses_consultant_fallback(): void
    {
        $service = Mockery::mock(OllamaService::class);

        $repetitiveReply = 'Aap university ki website check karein aur requirements dekhein. Aap university ki website check karein aur requirements dekhein.';

        $service->shouldReceive('generate')
            ->twice()
            ->andReturn($repetitiveReply, $repetitiveReply);

        $this->app->instance(OllamaService::class, $service);

        $reply = app(WhatsAppAIResponder::class)
            ->generateReply('Bahar mulk university mein admission ke liye kya documents chahiye?');

        $this->assertStringContainsString('consultant se rabta karein', $reply);
    }
}