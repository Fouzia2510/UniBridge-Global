<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Services\KapsoWhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class WhatsAppReplyWindowTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_approved' => true,
        ]);
    }

    private function createConversation(): WhatsAppConversation
    {
        $conversation = WhatsAppConversation::create([
            'phone_number' => '923001234567',
            'contact_name' => 'Test Contact',
            'status' => 'open',
        ]);

        config([
            'services.kapso.sandbox_mode' => true,
            'services.kapso.sandbox_recipient' => '923001234567',
        ]);

        return $conversation;
    }

    private function postReply(
        User $admin,
        WhatsAppConversation $conversation
    ) {
        return $this->withSession([
            'two_factor_verified' => true,
        ])->actingAs($admin)->post(
            route('admin.whatsapp.reply', $conversation),
            ['message' => 'Test reply']
        );
    }

    public function test_recent_incoming_message_allows_reply(): void
    {
        $this->travelTo(now());

        $admin = $this->createAdmin();
        $conversation = $this->createConversation();

        WhatsAppMessage::create([
            'conversation_id' => $conversation->id,
            'direction' => 'inbound',
            'message_type' => 'text',
            'message_text' => 'Hello',
            'status' => 'received',
            'sent_at' => now()->subHours(2),
        ]);

        $kapso = \Mockery::mock(KapsoWhatsAppService::class);
        $kapso->shouldReceive('sendText')
            ->once()
            ->with('923001234567', 'Test reply')
            ->andReturn([
                'messages' => [[
                    'id' => 'wamid.test',
                    'message_status' => 'accepted',
                ]],
            ]);

        $this->instance(KapsoWhatsAppService::class, $kapso);

        $response = $this->postReply($admin, $conversation);

        $response->assertRedirect(
            route('admin.whatsapp.chat', [
                'conversation' => $conversation->id,
            ])
        );

        $this->assertDatabaseHas('whatsapp_messages', [
            'conversation_id' => $conversation->id,
            'direction' => 'outbound',
            'message_text' => 'Test reply',
        ]);
    }

    public function test_expired_incoming_message_blocks_reply(): void
    {
        $this->travelTo(now());

        $admin = $this->createAdmin();
        $conversation = $this->createConversation();

        WhatsAppMessage::create([
            'conversation_id' => $conversation->id,
            'direction' => 'inbound',
            'message_type' => 'text',
            'message_text' => 'Old message',
            'status' => 'received',
            'sent_at' => now()->subHours(25),
        ]);

        $kapso = \Mockery::mock(KapsoWhatsAppService::class);
        $kapso->shouldNotReceive('sendText');

        $this->instance(KapsoWhatsAppService::class, $kapso);

        $response = $this->postReply($admin, $conversation);

        $response->assertRedirect(
            route('admin.whatsapp.chat', [
                'conversation' => $conversation->id,
            ])
        );

        $response->assertSessionHas('error');

        $this->assertDatabaseMissing('whatsapp_messages', [
            'conversation_id' => $conversation->id,
            'direction' => 'outbound',
            'message_text' => 'Test reply',
        ]);
    }

    public function test_missing_incoming_timestamp_blocks_reply(): void
    {
        $this->travelTo(now());

        $admin = $this->createAdmin();
        $conversation = $this->createConversation();

        WhatsAppMessage::create([
            'conversation_id' => $conversation->id,
            'direction' => 'inbound',
            'message_type' => 'text',
            'message_text' => 'Message without timestamp',
            'status' => 'received',
            'sent_at' => null,
        ]);

        $kapso = \Mockery::mock(KapsoWhatsAppService::class);
        $kapso->shouldNotReceive('sendText');

        $this->instance(KapsoWhatsAppService::class, $kapso);

        $response = $this->postReply($admin, $conversation);

        $response->assertSessionHas('error');
    }

    public function test_recent_outgoing_message_does_not_extend_reply_window(): void
    {
        $this->travelTo(now());

        $admin = $this->createAdmin();
        $conversation = $this->createConversation();

        WhatsAppMessage::create([
            'conversation_id' => $conversation->id,
            'direction' => 'inbound',
            'message_type' => 'text',
            'message_text' => 'Old incoming message',
            'status' => 'received',
            'sent_at' => now()->subHours(25),
        ]);

        WhatsAppMessage::create([
            'conversation_id' => $conversation->id,
            'direction' => 'outbound',
            'message_type' => 'text',
            'message_text' => 'Recent outgoing message',
            'status' => 'accepted',
            'sent_at' => now()->subMinutes(5),
        ]);

        $kapso = \Mockery::mock(KapsoWhatsAppService::class);
        $kapso->shouldNotReceive('sendText');

        $this->instance(KapsoWhatsAppService::class, $kapso);

        $response = $this->postReply($admin, $conversation);

        $response->assertSessionHas('error');
    }

    public function test_expired_window_blocks_media_upload(): void
    {
        $this->travelTo(now());

        $admin = $this->createAdmin();
        $conversation = $this->createConversation();

        WhatsAppMessage::create([
            'conversation_id' => $conversation->id,
            'direction' => 'inbound',
            'message_type' => 'text',
            'message_text' => 'Old message',
            'status' => 'received',
            'sent_at' => now()->subHours(25),
        ]);

        $kapso = \Mockery::mock(KapsoWhatsAppService::class);
        $kapso->shouldNotReceive('uploadMedia');
        $kapso->shouldNotReceive('sendMedia');

        $this->instance(KapsoWhatsAppService::class, $kapso);

        $response = $this->withSession([
            'two_factor_verified' => true,
        ])->actingAs($admin)->post(
            route('admin.whatsapp.media', $conversation),
            [
                'attachment' => UploadedFile::fake()->create(
                    'test.pdf',
                    100,
                    'application/pdf'
                ),
                'caption' => 'Test media',
            ]
        );

        $response->assertRedirect(
            route('admin.whatsapp.chat', [
                'conversation' => $conversation->id,
            ])
        );

        $response->assertSessionHas('error');
    }

    public function test_expired_window_blocks_location_sharing(): void
    {
        $this->travelTo(now());

        $admin = $this->createAdmin();
        $conversation = $this->createConversation();

        WhatsAppMessage::create([
            'conversation_id' => $conversation->id,
            'direction' => 'inbound',
            'message_type' => 'text',
            'message_text' => 'Old message',
            'status' => 'received',
            'sent_at' => now()->subHours(25),
        ]);

        $kapso = \Mockery::mock(KapsoWhatsAppService::class);
        $kapso->shouldNotReceive('sendLocation');

        $this->instance(KapsoWhatsAppService::class, $kapso);

        $response = $this->withSession([
            'two_factor_verified' => true,
        ])->actingAs($admin)->post(
            route('admin.whatsapp.location', $conversation),
            [
                'latitude' => 31.5204,
                'longitude' => 74.3587,
                'location_name' => 'Test Location',
                'location_address' => 'Test Address',
                'location_confirmed' => '1',
            ]
        );

        $response->assertRedirect(
            route('admin.whatsapp.chat', [
                'conversation' => $conversation->id,
            ])
        );

        $response->assertSessionHas('error');
    }
}
