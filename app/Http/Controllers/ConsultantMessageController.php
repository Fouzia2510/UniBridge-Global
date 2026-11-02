<?php

namespace App\Http\Controllers;

use App\Models\ConsultantMessage;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsultantMessageController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Student - Contact Consultant
    |--------------------------------------------------------------------------
    */

    public function create(Request $request): View
    {
        $consultants = User::query()
            ->where('role', User::ROLE_CONSULTANT)
            ->where('is_approved', true)
            ->orderBy('name')
            ->get();

        $selectedConsultantId = $request->query('consultant_id');

        return view('student.contact-consultant', [
            'consultants' => $consultants,
            'selectedConsultantId' => $selectedConsultantId,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'consultant_id' => [
                'required',
                'exists:users,id',
            ],
            'subject' => [
                'nullable',
                'string',
                'max:255',
            ],
            'message' => [
                'required',
                'string',
                'max:3000',
            ],
        ]);

        $consultant = User::query()
            ->where('id', $validated['consultant_id'])
            ->where('role', User::ROLE_CONSULTANT)
            ->where('is_approved', true)
            ->first();

        abort_if(
            ! $consultant,
            404,
            'Consultant not found or not available.'
        );

        ConsultantMessage::create([
            'student_id' => $request->user()->id,
            'consultant_id' => $consultant->id,
            'subject' => $validated['subject'] ?? null,
            'message' => $validated['message'],
            'consultant_reply' => null,
            'replied_at' => null,
            'is_read' => false,
        ]);

        return redirect()
            ->route('student.messages.index')
            ->with(
                'status',
                'Your message has been sent successfully!'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Student - Messages List
    |--------------------------------------------------------------------------
    */

    public function studentIndex(Request $request): View
    {
        $messages = ConsultantMessage::query()
            ->where('student_id', $request->user()->id)
            ->with('consultant')
            ->latest()
            ->get();

        return view('student.messages', [
            'messages' => $messages,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Student - View Conversation
    |--------------------------------------------------------------------------
    */

    public function studentShow(
        Request $request,
        ConsultantMessage $conversation
    ): View {
        abort_if(
            $conversation->student_id !== $request->user()->id,
            403
        );

        $conversation->load('consultant');

        return view('student.message-show', [
            'conversation' => $conversation,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Student - Send Message / Follow-up
    |--------------------------------------------------------------------------
    */

    public function studentStore(
        Request $request,
        ConsultantMessage $conversation
    ): RedirectResponse {
        abort_if(
            $conversation->student_id !== $request->user()->id,
            403
        );

        $validated = $request->validate([
            'message' => [
                'required',
                'string',
                'max:3000',
            ],
        ]);

        /*
         * Keep the latest student message in the main
         * message field. The consultant reply remains
         * available separately.
         */
        $conversation->update([
            'message' => $validated['message'],
            'is_read' => false,
        ]);

        return redirect()
            ->route(
                'student.messages.show',
                $conversation
            )
            ->with(
                'status',
                'Your message has been sent successfully!'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Consultant - Messages List
    |--------------------------------------------------------------------------
    */

    public function consultantIndex(Request $request): View
    {
        $messages = ConsultantMessage::query()
            ->where('consultant_id', $request->user()->id)
            ->with('student')
            ->latest()
            ->get();

        return view('consultant.messages', [
            'messages' => $messages,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Consultant - View Conversation
    |--------------------------------------------------------------------------
    */

    public function show(
        Request $request,
        ConsultantMessage $conversation
    ): View {
        abort_if(
            $conversation->consultant_id !== $request->user()->id,
            403
        );

        $conversation->load('student');

        if (! $conversation->is_read) {
            $conversation->update([
                'is_read' => true,
            ]);
        }

        return view('consultant.message-show', [
            'conversation' => $conversation,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Consultant - Reply
    |--------------------------------------------------------------------------
    */

    public function reply(
        Request $request,
        ConsultantMessage $message
    ): RedirectResponse {
        abort_if(
            $message->consultant_id !== $request->user()->id,
            403
        );

        $validated = $request->validate([
            'consultant_reply' => [
                'required',
                'string',
                'max:3000',
            ],
        ]);

        $message->update([
            'consultant_reply' => $validated['consultant_reply'],
            'replied_at' => now(),
            'is_read' => true,
        ]);

        return redirect()
            ->route('consultant.messages.index')
            ->with(
                'status',
                'Reply sent successfully!'
            );
    }
}