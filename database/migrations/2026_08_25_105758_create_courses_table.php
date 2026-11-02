<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category'); // e.g. IELTS Prep, SOP Writing, Scholarship Guidance, Visa Interview
            $table->text('description')->nullable();
            $table->string('mode'); // Online, Physical, Hybrid
            $table->integer('duration_weeks')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('instructor_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};