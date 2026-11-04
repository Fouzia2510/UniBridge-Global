<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_contacts', function (Blueprint $table) {
            $table->id();

            // Store phone numbers as digits only.
            $table->string('phone_number', 30)->unique();

            $table->string('contact_name')->nullable();

            $table->string('contact_type')->default('manual');

            // Link to a UniBridge user when available.
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('contact_name');
        });

        Schema::create('whatsapp_contact_consents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('whatsapp_contact_id')
                ->constrained('whatsapp_contacts')
                ->restrictOnDelete();

            // opted_in or opted_out
            $table->string('status', 20);

            // Example: inbound_message, consent_form, admin_recorded
            $table->string('source', 50);

            // Reference or description of the consent evidence.
            $table->text('evidence')->nullable();

            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('recorded_at');

            $table->timestamps();

            $table->index(
                ['whatsapp_contact_id', 'recorded_at'],
                'wa_contact_consent_history_index'
            );

            $table->index(
                ['whatsapp_contact_id', 'status'],
                'wa_contact_consent_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_contact_consents');
        Schema::dropIfExists('whatsapp_contacts');
    }
};
