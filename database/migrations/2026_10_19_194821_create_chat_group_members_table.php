<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_group_members', function (Blueprint $table) {
            $table->id();

            $table->foreignId('chat_group_id')
                ->constrained('chat_groups')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('member_type', 20)->default('user');
            $table->string('contact_name')->nullable();
            $table->string('phone_number', 30)->nullable();
            $table->string('role', 20)->default('member');

            $table->foreignId('added_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(['chat_group_id', 'user_id']);
            $table->index(['chat_group_id', 'member_type']);
            $table->index('phone_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_group_members');
    }
};
