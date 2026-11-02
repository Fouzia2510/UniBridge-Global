<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // contact_email and contact_phone already exist
        // in the create_universities_table migration.
    }

    public function down(): void
    {
        // No columns were added by this migration.
    }
};