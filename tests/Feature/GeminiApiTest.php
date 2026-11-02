<?php

namespace Tests\Feature;

use App\Models\User;
use App\services\GeminiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class GeminiApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_generate_text(): void
    {
        $service = Mockery::mock(GeminiService::class);

        $service->shouldReceive('generate')
    ->once()
    ->with(Mockery::type('string'))
    ->andReturn('Review your notes in short, focused sessions.');

        $this->app->instance(GeminiService::class, $service);

        $response = $this
            ->actingAs(User::factory()->create())
            ->withSession([
                'two_factor_verified' => true,
            ])
            ->postJson('/api/gemini/generate', [
                'prompt' => 'Give me one study tip.',
            ]);

        $response->assertOk()
            ->assertJson([
                'text' => 'Review your notes in short, focused sessions.',
            ]);
    }

    public function test_prompt_is_required(): void
    {
        $response = $this
            ->actingAs(User::factory()->create())
            ->withSession([
                'two_factor_verified' => true,
            ])
            ->postJson('/api/gemini/generate', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('prompt');
    }
}