<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WhatsAppContact;
use App\Models\WhatsAppContactConsent;
use App\Models\WhatsAppGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsAppGroupContactConsentTest extends TestCase
{
    use RefreshDatabase;

    private function createAdminAndGroup(): array
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_approved' => true,
        ]);

        $group = WhatsAppGroup::create([
            'name' => 'Consent Test Group',
            'category' => 'students',
            'created_by' => $admin->id,
        ]);

        return [$admin, $group];
    }

    public function test_adding_group_contact_without_opt_in_does_not_create_consent(): void
    {
        [$admin, $group] = $this->createAdminAndGroup();

        $response = $this->withSession([
            'two_factor_verified' => true,
        ])->actingAs($admin)->post(
            route('admin.whatsapp.groups.members.add', $group),
            [
                'contact_name' => 'Test Contact',
                'phone_number' => '923001234567',
                'contact_type' => 'manual',
            ]
        );

        $response->assertRedirect(
            route('admin.whatsapp.groups')
        );

        $contact = WhatsAppContact::where(
            'phone_number',
            '923001234567'
        )->first();

        $this->assertNotNull($contact);

        $this->assertDatabaseHas('whatsapp_group_members', [
            'whatsapp_group_id' => $group->id,
            'phone_number' => '923001234567',
            'whatsapp_opt_in' => false,
        ]);

        $this->assertDatabaseMissing('whatsapp_contact_consents', [
            'whatsapp_contact_id' => $contact->id,
        ]);
    }

    public function test_adding_group_contact_with_explicit_opt_in_records_consent(): void
    {
        [$admin, $group] = $this->createAdminAndGroup();

        $response = $this->withSession([
            'two_factor_verified' => true,
        ])->actingAs($admin)->post(
            route('admin.whatsapp.groups.members.add', $group),
            [
                'contact_name' => 'Consenting Contact',
                'phone_number' => '923001234568',
                'contact_type' => 'manual',
                'whatsapp_opt_in' => '1',
            ]
        );

        $response->assertRedirect(
            route('admin.whatsapp.groups')
        );

        $contact = WhatsAppContact::where(
            'phone_number',
            '923001234568'
        )->first();

        $this->assertNotNull($contact);

        $this->assertDatabaseHas('whatsapp_group_members', [
            'whatsapp_group_id' => $group->id,
            'phone_number' => '923001234568',
            'whatsapp_opt_in' => true,
        ]);

        $this->assertDatabaseHas('whatsapp_contact_consents', [
            'whatsapp_contact_id' => $contact->id,
            'status' => 'opted_in',
            'source' => 'admin_recorded',
            'recorded_by' => $admin->id,
        ]);
    }
}