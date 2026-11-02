<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Consultant;
use App\Models\Payment;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        return view('admin.dashboard');
    }

    public function users(): View
    {
        $users = User::orderBy('created_at', 'desc')->get();

        return view('admin.users', [
            'users' => $users,
            'pageTitle' => 'Manage Users',
            'pageDescription' =>
                'View all registered UniBridge accounts. Edit user details, activate accounts and manage individual user records.',
            'userType' => 'all',
        ]);
    }

    public function students(): View
    {
        $users = User::where('role', 'student')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.users', [
            'users' => $users,
            'pageTitle' => 'Manage Students',
            'pageDescription' =>
                'View and manage all registered student accounts.',
            'userType' => 'student',
        ]);
    }

    public function consultants(): View
    {
        $users = User::where('role', 'consultant')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.users', [
            'users' => $users,
            'pageTitle' => 'Manage Consultants',
            'pageDescription' =>
                'View and manage all consultant accounts.',
            'userType' => 'consultant',
        ]);
    }

    public function administrators(): View
    {
        $users = User::where('role', 'admin')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.users', [
            'users' => $users,
            'pageTitle' => 'Manage Administrators',
            'pageDescription' =>
                'View and manage all administrator accounts with platform access.',
            'userType' => 'admin',
        ]);
    }

    public function roleManagement(): View
    {
        $users = User::orderBy('role')
            ->orderBy('name')
            ->get();

        return view('admin.role-management', [
            'users' => $users,
        ]);
    }

    public function updateRole(
        Request $request,
        User $user
    ): RedirectResponse {
        $request->validate([
            'role' => [
                'required',
                'in:student,consultant,admin',
            ],
        ]);

        if ($user->id === $request->user()->id) {
            return redirect()->back()->with(
                'status',
                'You cannot change your own role.'
            );
        }

        $user->update([
            'role' => $request->role,
        ]);

        return redirect()->back()->with(
            'status',
            $user->name .
                "'s role has been updated successfully!"
        );
    }

    public function approveUser(User $user): RedirectResponse
    {
        $user->update([
            'is_approved' => true,
        ]);

        return redirect()->back()->with(
            'status',
            $user->name . ' has been approved!'
        );
    }

    public function editUser(User $user): View
    {
        $user->load([
            'documents' => function ($query) {
                $query->latest();
            },
            'studentProfile',
        ]);

        return view('admin.edit-user', [
            'user' => $user,
            'documents' => $user->documents,
        ]);
    }

    public function updateUser(
        Request $request,
        User $user
    ): RedirectResponse {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $user->id,
            ],
            'password' => [
                'nullable',
                'confirmed',
                'min:8',
            ],
        ];

        $studentFields = [
            'degree',
            'field_of_study',
            'cgpa',
            'graduation_year',
            'english_test',
            'english_score',
            'preferred_country',
            'preferred_degree_level',
            'preferred_subject',
            'budget',
            'preferred_intake',
            'work_experience_years',
        ];

        if ($user->role === 'student') {
            $rules = array_merge($rules, [
                'degree' => ['nullable', 'string', 'max:255'],
                'field_of_study' => ['nullable', 'string', 'max:255'],
                'cgpa' => ['nullable', 'numeric', 'min:0', 'max:4'],
                'graduation_year' => ['nullable', 'digits:4', 'integer', 'min:1900', 'max:2100'],
                'english_test' => ['nullable', Rule::in(['IELTS', 'PTE', 'Other', 'None'])],
                'english_score' => ['nullable', 'numeric', 'min:0', 'max:9'],
                'preferred_country' => ['nullable', 'string', 'max:255'],
                'preferred_degree_level' => ['nullable', 'string', 'max:255'],
                'preferred_subject' => ['nullable', 'string', 'max:255'],
                'budget' => ['nullable', 'numeric', 'min:0'],
                'preferred_intake' => ['nullable', 'string', 'max:255'],
                'work_experience_years' => ['nullable', 'numeric', 'min:0'],
            ]);
        }

        $validated = $request->validate($rules);

        DB::transaction(function () use ($user, $validated, $studentFields) {
            $user->name = $validated['name'];
            $user->email = $validated['email'];

            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();

            if ($user->role === 'student') {
                $studentData = array_intersect_key(
                    $validated,
                    array_flip($studentFields)
                );

                $profile = StudentProfile::firstOrNew([
                    'user_id' => $user->id,
                ]);

                $profile->fill($studentData);
                $profile->readiness_score = $profile->calculateReadinessScore();
                $profile->save();
            }
        });

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('status', 'User details and student profile saved successfully!');
    }
    public function destroyUser(
        Request $request,
        User $user
    ): RedirectResponse {
        abort_if(
            $user->id === $request->user()->id,
            403,
            'You cannot delete your own account.'
        );

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with(
                'status',
                'User deleted successfully!'
            );
    }

    public function applications(): View
    {
        $applications = Application::with([
            'student',
            'university',
            'consultant',
        ])
            ->orderBy('created_at', 'desc')
            ->get();

        return view(
            'admin.applications',
            compact('applications')
        );
    }

    public function showApplication(
        Application $application
    ): View {
        $application->load([
            'student',
            'university',
            'payments',
            'consultant',
            'assignedBy',
        ]);

        $consultants = User::where(
            'role',
            User::ROLE_CONSULTANT
        )
            ->where(
                'is_approved',
                true
            )
            ->orderBy('name')
            ->get();

        return view(
            'admin.application-show',
            compact(
                'application',
                'consultants'
            )
        );
    }

    public function assignConsultant(
        Request $request,
        Application $application
    ): RedirectResponse {
        $validated = $request->validate([
            'consultant_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where(
                        'role',
                        User::ROLE_CONSULTANT
                    )->where(
                        'is_approved',
                        true
                    );
                }),
            ],
        ]);

        $application->update([
            'consultant_id' => $validated['consultant_id'],
            'assigned_by' => $request->user()->id,
            'assigned_at' => now(),
        ]);

        return redirect()
            ->route(
                'admin.applications.show',
                $application
            )
            ->with(
                'status',
                'Consultant assigned successfully.'
            );
    }

    public function updateApplicationStatus(
        Request $request,
        Application $application
    ): RedirectResponse {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:submitted,under_review,accepted,rejected,withdrawn',
            ],
        ]);

        $application->update([
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route(
                'admin.applications.show',
                $application
            )
            ->with(
                'status',
                'Application status updated successfully!'
            );
    }

    public function showPayment(
        Application $application
    ): View {
        $application->load([
            'student',
            'university',
            'payments' => function ($query) {
                $query->latest();
            },
        ]);

        $payment = $application->payments->first();

        return view(
            'admin.application-payment',
            compact(
                'application',
                'payment'
            )
        );
    }

    public function verifyPayment(
        Request $request,
        Application $application
    ): RedirectResponse {
        $payment = $application->payments()
            ->latest()
            ->first();

        if (!$payment) {
            return redirect()
                ->back()
                ->withErrors([
                    'payment' =>
                        'No payment submission was found for this application.',
                ]);
        }

        if ($payment->status !== 'pending') {
            return redirect()
                ->back()
                ->withErrors([
                    'payment' =>
                        'Only pending payments can be verified.',
                ]);
        }

        $payment->update([
            'status' => 'verified',
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        return redirect()
            ->route(
                'admin.applications.show',
                $application
            )
            ->with(
                'status',
                'Payment verified successfully. The application can now proceed.'
            );
    }

    public function rejectPayment(
        Request $request,
        Application $application
    ): RedirectResponse {
        $validated = $request->validate([
            'admin_notes' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        $payment = $application->payments()
            ->latest()
            ->first();

        if (!$payment) {
            return redirect()
                ->back()
                ->withErrors([
                    'payment' =>
                        'No payment submission was found for this application.',
                ]);
        }

        if ($payment->status !== 'pending') {
            return redirect()
                ->back()
                ->withErrors([
                    'payment' =>
                        'Only pending payments can be rejected.',
                ]);
        }

        $payment->update([
            'status' => 'rejected',
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
            'admin_notes' => $validated['admin_notes'],
        ]);

        return redirect()
            ->route(
                'admin.applications.show',
                $application
            )
            ->with(
                'status',
                'Payment rejected. The student can submit a new payment.'
            );
    }

    public function consultantProfiles(): View
    {
        $consultants = Consultant::with('user')
            ->orderBy('company')
            ->get();

        $consultantUsers = User::where(
            'role',
            User::ROLE_CONSULTANT
        )
            ->orderBy('name')
            ->get();

        return view(
            'admin.consultant-profiles',
            [
                'consultants' => $consultants,
                'consultantUsers' => $consultantUsers,
            ]
        );
    }

    public function updateConsultantProfile(
        Request $request,
        Consultant $consultant
    ): RedirectResponse {
        $validated = $request->validate([
            'user_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where(
                        'role',
                        User::ROLE_CONSULTANT
                    );
                }),
                Rule::unique('consultants', 'user_id')->ignore(
                    $consultant->id
                ),
            ],
        ]);

        $consultant->update([
            'user_id' => $validated['user_id'] ?? null,
        ]);

        if (!empty($validated['user_id'])) {
            $user = User::find($validated['user_id']);

            return redirect()
                ->route('admin.consultant-profiles')
                ->with(
                    'status',
                    $consultant->company .
                        ' is now linked with ' .
                        $user->name .
                        '.'
                );
        }

        return redirect()
            ->route('admin.consultant-profiles')
            ->with(
                'status',
                $consultant->company .
                    ' has been unlinked.'
            );
    }
}