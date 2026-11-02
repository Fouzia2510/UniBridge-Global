<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultant_profiles', function (Blueprint $table) {

            $table->string('budget_range')->nullable()->after('about');

            $table->string('preferred_country')->nullable()->after('budget_range');

            $table->text('preferred_destinations')->nullable()->after('preferred_country');

            $table->text('client_demands')->nullable()->after('preferred_destinations');

            $table->string('consultation_type')->nullable()->after('client_demands');

            $table->string('availability')->nullable()->after('consultation_type');

            $table->string('meeting_preference')->nullable()->after('availability');

            $table->string('approval_status')
                ->default('pending')
                ->after('meeting_preference');

            $table->text('admin_notes')->nullable()->after('approval_status');

            $table->timestamp('approved_at')->nullable()->after('admin_notes');
        });
    }

    public function down(): void
    {
        Schema::table('consultant_profiles', function (Blueprint $table) {

            $table->dropColumn([
                'budget_range',
                'preferred_country',
                'preferred_destinations',
                'client_demands',
                'consultation_type',
                'availability',
                'meeting_preference',
                'approval_status',
                'admin_notes',
                'approved_at',
            ]);
        });
    }
};