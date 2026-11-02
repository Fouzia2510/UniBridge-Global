<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('university_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->string('program');
            $table->string('intake')->nullable();

            $table->text('personal_statement')->nullable();
            $table->text('notes')->nullable();

            $table->enum('status', [
                'draft',
                'submitted',
                'under_review',
                'accepted',
                'rejected',
                'withdrawn',
            ])->default('draft');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};