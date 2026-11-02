<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('application_id')
                ->constrained('applications')
                ->cascadeOnDelete();

            $table->foreignId('student_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /*
             * Payment method selected by the student.
             */
            $table->enum('payment_method', [
                'jazzcash',
                'easypaisa',
                'nayapay',
                'raast',
                'bank_transfer',
                'western_union',
            ]);

            /*
             * Used when bank_transfer is selected.
             * Examples:
             * Bank of Punjab
             * Soneri Bank
             * Other Bank
             */
            $table->string('bank_name')->nullable();

            /*
             * Amount requested/paid.
             */
            $table->decimal('amount', 12, 2);

            /*
             * Currency, e.g. PKR.
             */
            $table->string('currency', 10)->default('PKR');

            /*
             * Student's transaction/reference number.
             */
            $table->string('transaction_reference')->nullable();

            /*
             * Optional account/phone/reference information.
             * Do not store passwords, PINs or OTPs.
             */
            $table->string('payer_reference')->nullable();

            /*
             * Payment proof file path.
             */
            $table->string('proof_path')->nullable();

            /*
             * Admin verification status.
             */
            $table->enum('status', [
                'pending',
                'verified',
                'rejected',
            ])->default('pending');

            /*
             * Admin who verified/rejected the payment.
             */
            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('verified_at')->nullable();

            /*
             * Admin's reason/notes.
             */
            $table->text('admin_notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};