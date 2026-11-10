<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Payment;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentPaymentSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_cannot_write_admin_notes_when_submitting_payment(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
            'is_approved' => true,
        ]);
        $university = University::create([
            'name' => 'Test University',
            'country' => 'United Kingdom',
            'degree_level' => 'Bachelor',
            'subject' => 'Business',
        ]);
        $application = Application::create([
            'student_id' => $student->id,
            'university_id' => $university->id,
            'program' => 'Test program',
            'intake' => 'Fall',
            'fee' => 1000,
            'status' => 'submitted',
        ]);

        $this->actingAs($student)
            ->withSession(['two_factor_verified' => true])
            ->post(route('student.applications.payment.store', $application), [
                'payment_method' => 'bank_transfer',
                'bank_name' => 'Test Bank',
                'transaction_reference' => 'TX-12345',
                'proof' => UploadedFile::fake()->create('receipt.pdf', 100, 'application/pdf'),
                'admin_notes' => 'Forged administrator approval',
            ])
            ->assertRedirect(route('student.applications.show', $application));

        $payment = Payment::where('application_id', $application->id)->firstOrFail();
        $this->assertNull($payment->admin_notes);
        Storage::disk('local')->assertExists($payment->proof_path);
        Storage::disk('public')->assertMissing($payment->proof_path);
    }

    public function test_only_an_admin_can_download_a_payment_proof(): void
    {
        Storage::fake('local');
        $student = User::factory()->create(['role' => User::ROLE_STUDENT]);
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_approved' => true,
        ]);
        $university = University::create([
            'name' => 'Private Proof University',
            'country' => 'Canada',
            'degree_level' => 'Master',
            'subject' => 'Engineering',
        ]);
        $application = Application::create([
            'student_id' => $student->id,
            'university_id' => $university->id,
            'program' => 'Test program',
            'fee' => 1000,
            'status' => 'submitted',
        ]);
        $payment = Payment::create([
            'application_id' => $application->id,
            'student_id' => $student->id,
            'payment_method' => 'bank_transfer',
            'bank_name' => 'Test Bank',
            'amount' => 1000,
            'currency' => 'PKR',
            'transaction_reference' => 'TX-SECURE-1',
            'proof_path' => 'payment-proofs/private-receipt.pdf',
            'status' => 'pending',
        ]);
        Storage::disk('local')->put($payment->proof_path, 'private receipt bytes');

        $this->actingAs($student)
            ->withSession(['two_factor_verified' => true])
            ->get(route('admin.applications.payment.proof', [$application, $payment]))
            ->assertForbidden();

        $response = $this->actingAs($admin)
            ->withSession(['two_factor_verified' => true])
            ->get(route('admin.applications.payment.proof', [$application, $payment]));

        $response->assertOk();
        $response->assertStreamedContent('private receipt bytes');
    }
}
