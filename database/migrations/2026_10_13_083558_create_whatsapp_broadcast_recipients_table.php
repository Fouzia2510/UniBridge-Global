<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_broadcast_recipients', function (Blueprint $table) {
            $table->id();

            $table->foreignId('whatsapp_broadcast_id')
                ->constrained('whatsapp_broadcasts')
                ->cascadeOnDelete();

            $table->foreignId('whatsapp_group_member_id')
                ->nullable()
                ->constrained('whatsapp_group_members')
                ->nullOnDelete();

            $table->string('contact_name');
            $table->string('phone_number', 30);
            $table->string('status', 30)->default('pending');

            $table->string('provider_message_id')->nullable();
            $table->text('error_message')->nullable();
            $table->json('provider_payload')->nullable();

            $table->timestamp('attempted_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            $table->timestamps();

            $table->unique(
                ['whatsapp_broadcast_id', 'phone_number'],
                'wa_broadcast_recipient_unique'
            );

            $table->index(['whatsapp_broadcast_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_broadcast_recipients');
    }
};
