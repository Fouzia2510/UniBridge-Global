<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use Illuminate\View\View;

class ConsultantController extends Controller
{
    public function dashboard(): View
    {
        return view('consultant.dashboard');
    }

    /**
     * Show only students whose application is assigned
     * to the currently logged-in consultant.
     */
    public function students(): View
    {
        $consultantId = auth()->id();

        $students = User::where('role', User::ROLE_STUDENT)
            ->whereHas('applications', function ($query) use ($consultantId) {
                $query->where('consultant_id', $consultantId);
            })
            ->with('studentProfile')
            ->orderBy('name')
            ->get();

        return view('consultant.students', compact('students'));
    }

    /**
     * Show a student's profile only if the student
     * has an application assigned to the current consultant.
     */
    public function showStudent(User $student): View
    {
        abort_if(
            $student->role !== User::ROLE_STUDENT,
            404
        );

        $consultantId = auth()->id();

        $isAssigned = $student->applications()
            ->where('consultant_id', $consultantId)
            ->exists();

        abort_if(!$isAssigned, 403);

        $profile = $student->studentProfile;

        $summary = $profile
            ? $profile->eligibilitySummary()
            : null;

        /*
         * Load only applications assigned to
         * the currently logged-in consultant.
         */
        $applications = $student->applications()
            ->where('consultant_id', $consultantId)
            ->with([
                'university',
                'consultant',
            ])
            ->latest()
            ->get();

        /*
         * Load only consultation bookings between
         * this student and the currently logged-in consultant.
         */
        $meetings = Booking::where(
                'student_id',
                $student->id
            )
            ->where(
                'consultant_id',
                $consultantId
            )
            ->orderByDesc('preferred_date')
            ->orderByDesc('preferred_time')
            ->get();

        return view(
            'consultant.student-detail',
            compact(
                'student',
                'profile',
                'summary',
                'applications',
                'meetings'
            )
        );
    }
}