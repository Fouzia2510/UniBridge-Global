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
        if (Schema::hasTable('course_modules')) {
            return;
        }

        Schema::create('course_modules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')
                ->constrained('courses')
                ->cascadeOnDelete();

            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index([
                'course_id',
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