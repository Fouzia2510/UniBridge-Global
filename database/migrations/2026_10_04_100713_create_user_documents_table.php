<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('document_type');
            $table->string('title');
            $table->string('original_name');

            // Private/local storage path
            $table->string('storage_path')->nullable();

            // Google Drive information
            $table->string('google_drive_file_id')->nullable();
            $table->text('google_drive_url')->nullable();

            // Admin verification
            $table->enum('verification_status', [
                'pending',
                'approved',
                'rejected',
            ])->default('pending');

            $table->text('admin_notes')->nullable();

            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('verified_at')->nullable();

            $table->timestamps();

            $table->index([
                'user_id',
                'document_type'
            ]);

            $table->index('verification_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_documents');
    }
};