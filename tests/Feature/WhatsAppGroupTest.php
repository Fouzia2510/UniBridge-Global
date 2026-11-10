<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WhatsAppBroadcast;
use App\Models\WhatsAppGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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

        $response = $this->withSession([
            'two_factor_verified' => true,
        ])->actingAs($admin)
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

        $response = $this->withSession([
            'two_factor_verified' => true,
        ])->actingAs($admin)
            ->delete(route('admin.whatsapp.groups.delete', $group));

        $response->assertRedirect(route('admin.whatsapp.groups'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('whatsapp_groups', [
            'id' => $group->id,
        ]);
    }

    public function test_group_with_internal_messages_cannot_be_deleted(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_approved' => true,
        ]);

        $group = WhatsAppGroup::create([
            'name' => 'Internal Message Protected Group',
            'category' => 'general',
            'created_by' => $admin->id,
        ]);

        DB::table('whatsapp_group_messages')->insert([
            'whatsapp_group_id' => $group->id,
            'sender_user_id' => $admin->id,
            'sender_type' => 'admin',
            'sender_name' => $admin->name,
            'message_text' => 'Saved internal history',
            'message_type' => 'text',
            'status' => 'local',
            'sent_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->withSession([
            'two_factor_verified' => true,
        ])->actingAs($admin)
            ->delete(route('admin.whatsapp.groups.delete', $group));

        $response->assertRedirect(route('admin.whatsapp.groups'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('whatsapp_groups', [
            'id' => $group->id,
        ]);

        $this->assertDatabaseHas('whatsapp_group_messages', [
            'whatsapp_group_id' => $group->id,
            'message_text' => 'Saved internal history',
        ]);
    }

    public function test_removing_member_preserves_central_contact_and_consent(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_approved' => true,
        ]);

        $group = WhatsAppGroup::create([
            'name' => 'Member Removal Test Group',
            'category' => 'general',
            'created_by' => $admin->id,
        ]);

        $now = now();

        $contactId = DB::table('whatsapp_contacts')->insertGetId([
            'phone_number' => '923001234567',
            'contact_name' => 'Removal Test Contact',
            'contact_type' => 'manual',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $consentId = DB::table('whatsapp_contact_consents')->insertGetId([
            'whatsapp_contact_id' => $contactId,
            'status' => 'opted_in',
            'source' => 'admin_recorded',
            'evidence' => 'Test consent evidence',
            'recorded_by' => $admin->id,
            'recorded_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $memberId = DB::table('whatsapp_group_members')->insertGetId([
            'whatsapp_group_id' => $group->id,
            'user_id' => null,
            'contact_name' => 'Removal Test Contact',
            'phone_number' => '923001234567',
            'contact_type' => 'manual',
            'whatsapp_opt_in' => true,
            'opted_in_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $response = $this->withSession([
            'two_factor_verified' => true,
        ])->actingAs($admin)
            ->delete(route(
                'admin.whatsapp.groups.members.remove',
                [
                    'group' => $group->id,
                    'member' => $memberId,
                ]
            ));

        $response->assertRedirect(route('admin.whatsapp.groups'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('whatsapp_group_members', [
            'id' => $memberId,
        ]);

        $this->assertDatabaseHas('whatsapp_contacts', [
            'id' => $contactId,
            'phone_number' => '923001234567',
        ]);

        $this->assertDatabaseHas('whatsapp_contact_consents', [
            'id' => $consentId,
            'whatsapp_contact_id' => $contactId,
            'status' => 'opted_in',
        ]);
    }

    public function test_member_from_another_group_cannot_be_removed(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_approved' => true,
        ]);

        $firstGroup = WhatsAppGroup::create([
            'name' => 'First Test Group',
            'category' => 'general',
            'created_by' => $admin->id,
        ]);

        $secondGroup = WhatsAppGroup::create([
            'name' => 'Second Test Group',
            'category' => 'general',
            'created_by' => $admin->id,
        ]);

        $now = now();

        $memberId = DB::table('whatsapp_group_members')->insertGetId([
            'whatsapp_group_id' => $secondGroup->id,
            'user_id' => null,
            'contact_name' => 'Other Group Member',
            'phone_number' => '923009876543',
            'contact_type' => 'manual',
            'whatsapp_opt_in' => false,
            'opted_in_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $response = $this->withSession([
            'two_factor_verified' => true,
        ])->actingAs($admin)
            ->delete(route(
                'admin.whatsapp.groups.members.remove',
                [
                    'group' => $firstGroup->id,
                    'member' => $memberId,
                ]
            ));

        $response->assertNotFound();

        $this->assertDatabaseHas('whatsapp_group_members', [
            'id' => $memberId,
            'whatsapp_group_id' => $secondGroup->id,
        ]);
    }
}
