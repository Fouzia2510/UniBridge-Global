<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * This table already exists in the UniBridge database.
         * Do not recreate or modify it.
         */
        if (Schema::hasTable('course_lessons')) {
            return;
        }

        Schema::create('course_lessons', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_module_id')
                ->constrained('course_modules')
                ->cascadeOnDelete();

            $table->string('title');
            $table->string('slug')->nullable();
            $table->string('lesson_type')->nullable();
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->string('video_url')->nullable();
            $table->string('resource_url')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_preview')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index([
                'course_module_id',
                'sort_order',
            ]);
        });
    }

    public function down(): void
    {
        /*
         * The table existed before this migration,
         * so this migration must not delete it.
         */
    }
};