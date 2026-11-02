<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visa_applications', function (Blueprint $table) {
            $table->id();

            // Related university application
            $table->foreignId('application_id')
                ->constrained('applications')
                ->cascadeOnDelete();

            // Student who is applying for the visa
            $table->foreignId('student_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Visa information
            $table->string('country');
            $table->string('visa_type');

            // Visa procedure status
            $table->enum('status', [
                'not_started',
                'documents_required',
                'documents_submitted',
                'under_review',
                'ready_to_apply',
                'submitted',
                'processing',
                'approved',
                'rejected',
            ])->default('not_started');

            // Student/admin information
            $table->text('student_notes')->nullable();
            $table->text('admin_notes')->nullable();

            // Important dates
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('decision_at')->nullable();

            $table->timestamps();

            // Helpful indexes
            $table->index('status');
            $table->index('country');

            // One visa procedure per university application
            $table->unique('application_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visa_applications');
    }
};
