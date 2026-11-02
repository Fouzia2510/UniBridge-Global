<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WhatsAppBroadcast;
use App\Models\WhatsAppGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsAppGroupTest extends TestCase
{
    use RefreshDatabase;

    public function test_group_with_broadcast_history_cannot_be_deleted(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_approved' => true,
        ]);

        $group = WhatsAppGroup::create([
            'name' => 'History Protected Group',
            'category' => 'students',
            'created_by' => $admin->id,
        ]);

        WhatsAppBroadcast::create([
            'whatsapp_group_id' => $group->id,
            'created_by' => $admin->id,
            'name' => 'Saved Broadcast',
            'message_text' => 'Previously saved draft',
            'status' => 'draft',
        ]);

        $response = $this->withSession(['two_factor_verified' => true])
            ->actingAs($admin)
            ->delete(route('admin.whatsapp.groups.delete', $group));

        $response->assertRedirect(route('admin.whatsapp.groups'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('whatsapp_groups', [
            'id' => $group->id,
        ]);

        $this->assertDatabaseHas('whatsapp_broadcasts', [
            'whatsapp_group_id' => $group->id,
        ]);
    }

    public function test_group_without_broadcast_history_can_be_deleted(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_approved' => true,
        ]);

        $group = WhatsAppGroup::create([
            'name' => 'Safe To Delete Group',
            'category' => 'general',
            'created_by' => $admin->id,
        ]);

        $response = $this->withSession(['two_factor_verified' => true])
            ->actingAs($admin)
            ->delete(route('admin.whatsapp.groups.delete', $group));

        $response->assertRedirect(route('admin.whatsapp.groups'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('whatsapp_groups', [
            'id' => $group->id,
        ]);
    }
}
