
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('conversation_id')
                ->constrained('whatsapp_conversations')
                ->cascadeOnDelete();

            // Unique provider ID prevents saving the same message twice.
            $table->string('provider_message_id')->nullable()->unique();

            // inbound = received from a user; outbound = sent by UniBridge.
            $table->string('direction', 10)->default('inbound');

            $table->string('message_type', 30)->default('text');
            $table->text('message_text')->nullable();

            // Delivery state for outgoing messages, when available.
            $table->string('status', 30)->nullable();

            // Preserve the original provider payload for troubleshooting.
            $table->json('payload')->nullable();

            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
            $table->index(['direction', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};
