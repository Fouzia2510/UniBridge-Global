<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_group_members', function (Blueprint $table) {
            $table->id();

            $table->foreignId('whatsapp_group_id')
                ->constrained('whatsapp_groups')
                ->cascadeOnDelete();

            // Link to a UniBridge student/consultant when available.
            // Manual contacts can leave this empty.
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('contact_name');
            $table->string('phone_number', 30);

            // student, consultant, or manual
            $table->string('contact_type')->default('manual');

            $table->timestamps();

            // Avoid adding the same phone twice to one group.
            $table->unique(
                ['whatsapp_group_id', 'phone_number'],
                'wa_group_member_phone_unique'
            );

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_group_members');
    }
};