<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_group_messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('whatsapp_group_id')
                ->constrained('whatsapp_groups')
                ->cascadeOnDelete();

            $table->foreignId('sender_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('sender_type')->default('admin');
            $table->string('sender_name')->nullable();

            $table->text('message_text');
            $table->string('message_type')->default('text');
            $table->string('status')->default('local');

            $table->string('provider_message_id')->nullable()->index();
            $table->json('payload')->nullable();
            $table->timestamp('sent_at')->nullable();

            $table->timestamps();

            $table->index(['whatsapp_group_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_group_messages');
    }
};