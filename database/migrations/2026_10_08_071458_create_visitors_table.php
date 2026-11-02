<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();

            // Consultant whose profile/page was visited.
            $table->foreignId('consultant_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Optional logged-in student/user who visited.
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Visitor information for guests as well.
            $table->string('visitor_name')->nullable();
            $table->string('visitor_email')->nullable();

            // Where/how the visitor came from.
            $table->string('source')->default('profile');
            $table->string('visit_type')->default('online');

            // Useful study-abroad information.
            $table->string('country_interest')->nullable();
            $table->string('program_interest')->nullable();

            // Optional consultant notes.
            $table->text('remarks')->nullable();

            // Basic visit information.
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('visited_at')->nullable();

            $table->timestamps();

            $table->index([
                'consultant_id',
                'visited_at',
            ]);

            $table->index([
                'consultant_id',
                'source',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};