<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentPaymentController extends Controller
{
    /**
     * Show payment form for an application.
     */
    public function create(
        Request $request,
        Application $application
    ): View {
        abort_if(
            $application->student_id !== $request->user()->id,
            403
        );

        $application->load('university');

        $payment = $application->payments()
            ->latest()
            ->first();

        return view(
            'student.applications.payment',
            compact('application', 'payment')
        );
    }

    /**
     * Store student payment submission.
     */
    public function store(
        Request $request,
        Application $application
    ): RedirectResponse {
        abort_if(
            $application->student_id !== $request->user()->id,
            403
        );

        $validated = $request->validate([
            'payment_method' => [
                'required',
                'in:jazzcash,easypaisa,nayapay,raast,bank_transfer,western_union',
            ],

            'bank_name' => [
                'nullable',
                'required_if:payment_method,bank_transfer',
                'string',
                'max:255',
            ],

            'transaction_reference' => [
                'required',
                'string',
                'max:255',
            ],

            'payer_reference' => [
                'nullable',
                'string',
                'max:255',
            ],

            'proof' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120',
            ],

        ]);

        /*
        |--------------------------------------------------------------------------
        | Prevent duplicate pending payment
        |--------------------------------------------------------------------------
        */

        $existingPendingPayment = $application->payments()
            ->where('status', 'pending')
            ->exists();

        if ($existingPendingPayment) {
            return back()
                ->withErrors([
                    'payment_method' =>
                        'You already have a payment waiting for admin verification.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Store Payment Proof
        |--------------------------------------------------------------------------
        */

        $proofPath = $request->file('proof')
            ->store('payment-proofs', 'local');

        /*
        |--------------------------------------------------------------------------
        | Create Payment
        |--------------------------------------------------------------------------
        */

        Payment::create([
            'application_id' => $application->id,
            'student_id' => $request->user()->id,

            'payment_method' =>
                $validated['payment_method'],

            'bank_name' =>
                $validated['bank_name'] ?? null,

            'amount' =>
                $application->fee ?? 3000,

            'currency' => 'PKR',

            'transaction_reference' =>
                $validated['transaction_reference'],

            'payer_reference' =>
                $validated['payer_reference'] ?? null,

            'proof_path' =>
                $proofPath,

            'status' => 'pending',

        ]);

        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'student.applications.show',
                $application
            )
            ->with(
                'status',
                'Payment submitted successfully. Your payment is now pending admin verification.'
            );
    }
}