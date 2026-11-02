<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WhatsAppBroadcast;
use App\Models\WhatsAppBroadcastRecipient;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppGroup;
use App\Models\WhatsAppGroupMember;
use App\Models\WhatsAppMessage;
use App\Services\KapsoWhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsAppTestBroadcastWindowTest extends TestCase
{
    use RefreshDatabase;

    private function prepareTestBroadcast(): array
    {
        config([
            'services.kapso.sandbox_mode' => true,
            'services.kapso.sandbox_recipient' => '923001234567',
        ]);

        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_approved' => true,
        ]);

        $group = WhatsAppGroup::create([
            'name' => 'Sandbox Test Group',
            'category' => 'students',
            'created_by' => $admin->id,
        ]);

        $member = WhatsAppGroupMember::create([
            'whatsapp_group_id' => $group->id,
            'contact_name' => 'Sandbox Contact',
            'phone_number' => '923001234567',
            'contact_type' => 'manual',
            'whatsapp_opt_in' => true,
            'opted_in_at' => now()->subDay(),
        ]);

        $broadcast = WhatsAppBroadcast::create([
            'whatsapp_group_id' => $group->id,
            'created_by' => $admin->id,
            'name' => 'Sandbox Test',
            'message_text' => 'Automated test message',
            'status' => 'draft',
            'recipient_count' => 1,
        ]);

        $recipient = WhatsAppBroadcastRecipient::create([
            'whatsapp_broadcast_id' => $broadcast->id,
            'whatsapp_group_member_id' => $member->id,
            'contact_name' => $member->contact_name,
            'phone_number' => $member->phone_number,
            'status' => 'pending',
        ]);

        $conversation = WhatsAppConversation::create([
            'phone_number' => '923001234567',
            'contact_name' => 'Sandbox Contact',
            'status' => 'open',
        ]);

        return compact(
            'admin',
            'broadcast',
            'recipient',
            'conversation'
        );
    }

    private function submitTestSend(User $admin, WhatsAppBroadcast $broadcast)
    {
        return $this->withSession([
            'two_factor_verified' => true,
        ])->actingAs($admin)->post(
            route('admin.whatsapp.broadcast-test-send', $broadcast),
            ['confirm_test_send' => true]
        );
    }

    public function test_expired_inbound_message_blocks_sandbox_test_send(): void
    {
        $this->travelTo(now());

        $data = $this->prepareTestBroadcast();

        WhatsAppMessage::create([
            'conversation_id' => $data['conversation']->id,
            'direction' => 'inbound',
            'message_type' => 'text',
            'message_text' => 'Old customer message',
            'status' => 'received',
            'sent_at' => now()->subHours(25),
        ]);

        $kapso = \Mockery::mock(KapsoWhatsAppService::class);
        $kapso->shouldNotReceive('sendText');

        $this->instance(KapsoWhatsAppService::class, $kapso);

        $response = $this->submitTestSend(
            $data['admin'],
            $data['broadcast']
        );

        $response->assertRedirect(
            route('admin.whatsapp.broadcasts', [
                'broadcast' => $data['broadcast']->id,
            ])
        );

        $response->assertSessionHas(
            'error',
            'Test-send blocked: no valid incoming customer message within the last 24 hours.'
        );

        $this->assertDatabaseHas('whatsapp_broadcasts', [
            'id' => $data['broadcast']->id,
            'status' => 'draft',
        ]);

        $this->assertDatabaseHas('whatsapp_broadcast_recipients', [
            'id' => $data['recipient']->id,
            'status' => 'pending',
            'attempted_at' => null,
        ]);
    }

    public function test_missing_inbound_message_blocks_sandbox_test_send(): void
    {
        $this->travelTo(now());

        $data = $this->prepareTestBroadcast();

        $kapso = \Mockery::mock(KapsoWhatsAppService::class);
        $kapso->shouldNotReceive('sendText');

        $this->instance(KapsoWhatsAppService::class, $kapso);

        $response = $this->submitTestSend(
            $data['admin'],
            $data['broadcast']
        );

        $response->assertRedirect(
            route('admin.whatsapp.broadcasts', [
                'broadcast' => $data['broadcast']->id,
            ])
        );

        $response->assertSessionHas(
            'error',
            'Test-send blocked: no valid incoming customer message within the last 24 hours.'
        );

        $this->assertDatabaseHas('whatsapp_broadcasts', [
            'id' => $data['broadcast']->id,
            'status' => 'draft',
        ]);

        $this->assertDatabaseHas('whatsapp_broadcast_recipients', [
            'id' => $data['recipient']->id,
            'status' => 'pending',
            'attempted_at' => null,
        ]);
    }
}
