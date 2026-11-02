<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Visitor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class ConsultantVisitorController extends Controller
{
    /**
     * Show grouped visitors for the currently logged-in consultant.
     *
     * IMPORTANT:
     * Every individual visit remains stored in the visitors table.
     * The dashboard only groups those visits for display.
     */
    public function index(Request $request): View
    {
        $consultantId = $request->user()->id;

        /*
         * -------------------------------------------------------------
         * Base visitor query
         * -------------------------------------------------------------
         */
        $query = Visitor::query()
            ->where('consultant_id', $consultantId)
            ->with([
                'user',
                'user.studentProfile',
            ])
            ->latest('visited_at')
            ->latest('id');

        /*
         * -------------------------------------------------------------
         * Search
         * -------------------------------------------------------------
         */
        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where(
                    'visitor_name',
                    'like',
                    '%' . $search . '%'
                )
                ->orWhere(
                    'visitor_email',
                    'like',
                    '%' . $search . '%'
                )
                ->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery
                        ->where(
                            'name',
                            'like',
                            '%' . $search . '%'
                        )
                        ->orWhere(
                            'email',
                            'like',
                            '%' . $search . '%'
                        );
                });
            });
        }

        /*
         * -------------------------------------------------------------
         * Lead status filter
         *
         * IMPORTANT:
         * Lead status belongs to individual visitor records.
         *
         * The dashboard groups visits afterwards.
         * Therefore filtering happens BEFORE grouping.
         * -------------------------------------------------------------
         */
        if ($request->filled('lead_status')) {
            $leadStatus = $request->input('lead_status');

            $allowedLeadStatuses = [
                'new',
                'contacted',
                'interested',
                'converted',
                'not_interested',
            ];

            if (in_array($leadStatus, $allowedLeadStatuses, true)) {
                $query->where(
                    'lead_status',
                    $leadStatus
                );
            }
        }

        /*
         * -------------------------------------------------------------
         * Visit type filter
         * -------------------------------------------------------------
         */
        if ($request->filled('visit_type')) {
            $query->where(
                'visit_type',
                $request->input('visit_type')
            );
        }

        /*
         * -------------------------------------------------------------
         * Source filter
         * -------------------------------------------------------------
         */
        if ($request->filled('source')) {
            $query->where(
                'source',
                $request->input('source')
            );
        }

        /*
         * -------------------------------------------------------------
         * Country filter
         * -------------------------------------------------------------
         */
        if ($request->filled('country_interest')) {
            $country = trim(
                $request->input('country_interest')
            );

            $query->where(function ($q) use ($country) {
                $q->where(
                    'country_interest',
                    'like',
                    '%' . $country . '%'
                )
                ->orWhereHas(
                    'user.studentProfile',
                    function ($profileQuery) use ($country) {
                        $profileQuery->where(
                            'preferred_country',
                            'like',
                            '%' . $country . '%'
                        );
                    }
                );
            });
        }

        /*
         * -------------------------------------------------------------
         * Get matching individual visit records.
         * -------------------------------------------------------------
         */
        $allVisitors = $query->get();

        /*
         * -------------------------------------------------------------
         * Group visitors.
         *
         * Registered users:
         *     user_id
         *
         * Guests:
         *     IP address
         * -------------------------------------------------------------
         */
        $groupedVisitors = $allVisitors
            ->groupBy(function (Visitor $visitor) {
                if ($visitor->user_id) {
                    return 'user:' . $visitor->user_id;
                }

                return 'guest:' .
                    ($visitor->ip_address ?: 'unknown');
            })
            ->map(function ($group) {

                /*
                 * -----------------------------------------------------
                 * Latest visit
                 * -----------------------------------------------------
                 */
                $latestVisit = $group
                    ->sortByDesc(function ($visitor) {
                        return $visitor->visited_at?->timestamp ?? 0;
                    })
                    ->sortByDesc('id')
                    ->first();

                /*
                 * -----------------------------------------------------
                 * First visit
                 * -----------------------------------------------------
                 */
                $firstVisit = $group
                    ->sortBy(function ($visitor) {
                        return $visitor->visited_at?->timestamp
                            ?? PHP_INT_MAX;
                    })
                    ->sortBy('id')
                    ->first();

                /*
                 * -----------------------------------------------------
                 * Registered user
                 * -----------------------------------------------------
                 */
                $user = $latestVisit?->user;

                /*
                 * -----------------------------------------------------
                 * Visitor role
                 * -----------------------------------------------------
                 */
                $visitorRole = $user?->role;

                /*
                 * -----------------------------------------------------
                 * Student profile ONLY for students.
                 * -----------------------------------------------------
                 */
                $profile = null;

                if ($visitorRole === 'student') {
                    $profile = $user?->studentProfile;
                }

                /*
                 * -----------------------------------------------------
                 * Profile completion
                 * -----------------------------------------------------
                 */
                $profileCompletion = 0;

                if ($profile) {
                    $profileFields = [
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

                    $completed = 0;

                    foreach ($profileFields as $field) {
                        $value = $profile->{$field};

                        if (
                            !is_null($value)
                            && $value !== ''
                        ) {
                            $completed++;
                        }
                    }

                    $totalFields = count($profileFields);

                    if ($totalFields > 0) {
                        $profileCompletion = (int) round(
                            ($completed / $totalFields) * 100
                        );
                    }
                }

                /*
                 * -----------------------------------------------------
                 * Applications
                 * -----------------------------------------------------
                 */
                $applications = collect();

                if (
                    $user
                    && $visitorRole === 'student'
                ) {
                    $applications = Application::query()
                        ->where(
                            'student_id',
                            $user->id
                        )
                        ->with([
                            'university',
                            'consultant',
                        ])
                        ->latest('created_at')
                        ->get();
                }

                $latestApplication =
                    $applications->first();

                /*
                 * -----------------------------------------------------
                 * Country
                 * -----------------------------------------------------
                 */
                $country =
                    $latestVisit?->country_interest
                    ?: $profile?->preferred_country;

                /*
                 * -----------------------------------------------------
                 * Program / Subject
                 * -----------------------------------------------------
                 */
                $program =
                    $latestVisit?->program_interest
                    ?: $profile?->preferred_subject;

                /*
                 * -----------------------------------------------------
                 * Application status
                 * -----------------------------------------------------
                 */
                $applicationStatus =
                    $latestApplication?->status;

                /*
                 * -----------------------------------------------------
                 * Lead status
                 * -----------------------------------------------------
                 */
                $leadStatus =
                    $latestVisit?->lead_status
                    ?: 'new';

                /*
                 * -----------------------------------------------------
                 * Consultant remarks
                 * -----------------------------------------------------
                 */
                $consultantRemarks =
                    $latestVisit?->consultant_remarks;

                /*
                 * -----------------------------------------------------
                 * Return grouped visitor data.
                 * -----------------------------------------------------
                 */
                return (object) [
                    'visitor' =>
                        $latestVisit,

                    'user' =>
                        $user,

                    'visitor_role' =>
                        $visitorRole,

                    'profile' =>
                        $profile,

                    'visitor_name' =>
                        $user?->name
                        ?? $latestVisit?->visitor_name
                        ?? 'Guest Visitor',

                    'visitor_email' =>
                        $user?->email
                        ?? $latestVisit?->visitor_email
                        ?? 'No email',

                    'visit_count' =>
                        $group->count(),

                    'first_visit' =>
                        $firstVisit?->visited_at,

                    'last_visit' =>
                        $latestVisit?->visited_at,

                    'visit_type' =>
                        $latestVisit?->visit_type
                        ?? 'online',

                    'source' =>
                        $latestVisit?->source
                        ?? 'profile',

                    'country' =>
                        $country,

                    'program' =>
                        $program,

                    'profile_completion' =>
                        $profileCompletion,

                    'readiness_score' =>
                        $profile?->readiness_score,

                    'applications_count' =>
                        $applications->count(),

                    'latest_application' =>
                        $latestApplication,

                    'application_status' =>
                        $applicationStatus,

                    'lead_status' =>
                        $leadStatus,

                    'consultant_remarks' =>
                        $consultantRemarks,

                    'is_registered' =>
                        !is_null($latestVisit?->user_id),
                ];
            })
            ->values();

        /*
         * -------------------------------------------------------------
         * Grouped pagination
         * -------------------------------------------------------------
         */
        $perPage = 15;

        $currentPage =
            LengthAwarePaginator::resolveCurrentPage();

        $currentItems = $groupedVisitors
            ->slice(
                ($currentPage - 1) * $perPage,
                $perPage
            )
            ->values();

        $visitors = new LengthAwarePaginator(
            $currentItems,
            $groupedVisitors->count(),
            $perPage,
            $currentPage,
            [
                'path' =>
                    LengthAwarePaginator::resolveCurrentPath(),

                'query' =>
                    $request->query(),
            ]
        );

        /*
         * -------------------------------------------------------------
         * Statistics
         * -------------------------------------------------------------
         */
        $baseQuery = Visitor::query()
            ->where(
                'consultant_id',
                $consultantId
            );

        /*
         * Total individual visits.
         */
        $totalVisitors = (clone $baseQuery)
            ->count();

        /*
         * Today's individual visits.
         */
        $todayVisitors = (clone $baseQuery)
            ->whereDate(
                'visited_at',
                today()
            )
            ->count();

        /*
         * Online individual visits.
         */
        $onlineVisitors = (clone $baseQuery)
            ->where(
                'visit_type',
                'online'
            )
            ->count();

        /*
         * Manual/offline individual visits.
         */
        $manualVisitors = (clone $baseQuery)
            ->where(
                'visit_type',
                '!=',
                'online'
            )
            ->count();

        /*
         * -------------------------------------------------------------
         * Unique registered STUDENTS only.
         * -------------------------------------------------------------
         */
        $registeredVisitors = (clone $baseQuery)
            ->whereNotNull('user_id')
            ->whereHas('user', function ($userQuery) {
                $userQuery->where(
                    'role',
                    'student'
                );
            })
            ->distinct('user_id')
            ->count('user_id');

        /*
         * -------------------------------------------------------------
         * Unique STUDENTS with applications.
         * -------------------------------------------------------------
         */
        $applicationVisitors = (clone $baseQuery)
            ->whereNotNull('user_id')
            ->whereHas('user', function ($userQuery) {
                $userQuery->where(
                    'role',
                    'student'
                );
            })
            ->whereHas('user.applications')
            ->distinct('user_id')
            ->count('user_id');

        /*
         * -------------------------------------------------------------
         * Send data to dashboard.
         * -------------------------------------------------------------
         */
        return view(
            'consultant.visitors',
            compact(
                'visitors',
                'totalVisitors',
                'todayVisitors',
                'onlineVisitors',
                'manualVisitors',
                'registeredVisitors',
                'applicationVisitors'
            )
        );
    }

    /**
     * Show one visitor with complete intelligence.
     */
    public function show(
        Request $request,
        Visitor $visitor
    ): View {
        abort_if(
            (int) $visitor->consultant_id !==
            (int) $request->user()->id,
            403
        );

        $visitor->load([
            'user',
            'consultant',
            'user.studentProfile',
        ]);

        $userId =
            $visitor->user_id;

        $visitorRole =
            $visitor->user?->role;

        $profile = null;

        $profileCompletion = 0;

        $completedProfileItems = [];

        $missingProfileItems = [];

        $applications = collect();

        /*
         * -------------------------------------------------------------
         * Find ALL visits from this same visitor.
         * -------------------------------------------------------------
         */
        $visitorHistoryQuery = Visitor::query()
            ->where(
                'consultant_id',
                $request->user()->id
            );

        if ($userId) {
            $visitorHistoryQuery->where(
                'user_id',
                $userId
            );
        } else {
            $visitorHistoryQuery
                ->whereNull('user_id')
                ->where(
                    'ip_address',
                    $visitor->ip_address
                );
        }

        $visitCount =
            (clone $visitorHistoryQuery)
                ->count();

        $firstVisit =
            (clone $visitorHistoryQuery)
                ->oldest('visited_at')
                ->first();

        $lastVisit =
            (clone $visitorHistoryQuery)
                ->latest('visited_at')
                ->latest('id')
                ->first();

        /*
         * -------------------------------------------------------------
         * Student intelligence ONLY for students.
         * -------------------------------------------------------------
         */
        if (
            $visitor->user
            && $visitor->user->role === 'student'
        ) {
            $profile =
                $visitor->user->studentProfile;

            if ($profile) {
                $profileFields = [
                    'degree' =>
                        'Degree',

                    'field_of_study' =>
                        'Field of Study',

                    'cgpa' =>
                        'CGPA',

                    'graduation_year' =>
                        'Graduation Year',

                    'english_test' =>
                        'English Test',

                    'english_score' =>
                        'English Score',

                    'preferred_country' =>
                        'Preferred Country',

                    'preferred_degree_level' =>
                        'Degree Level',

                    'preferred_subject' =>
                        'Preferred Subject',

                    'budget' =>
                        'Budget',

                    'preferred_intake' =>
                        'Preferred Intake',

                    'work_experience_years' =>
                        'Work Experience',
                ];

                foreach (
                    $profileFields as $field => $label
                ) {
                    $value =
                        $profile->{$field};

                    $filled =
                        !is_null($value)
                        && $value !== '';

                    if ($filled) {
                        $completedProfileItems[] =
                            $label;
                    } else {
                        $missingProfileItems[] =
                            $label;
                    }
                }

                $totalProfileFields =
                    count($profileFields);

                $completedCount =
                    count($completedProfileItems);

                $profileCompletion =
                    $totalProfileFields > 0
                    ? (int) round(
                        (
                            $completedCount /
                            $totalProfileFields
                        ) * 100
                    )
                    : 0;
            }

            $applications = Application::query()
                ->where(
                    'student_id',
                    $visitor->user->id
                )
                ->with([
                    'university',
                    'consultant',
                ])
                ->latest('created_at')
                ->get();
        }

        $latestApplication =
            $applications->first();

        $lastActivity =
            $lastVisit?->visited_at;

        return view(
            'consultant.visitor-detail',
            compact(
                'visitor',
                'visitorRole',
                'profile',
                'profileCompletion',
                'completedProfileItems',
                'missingProfileItems',
                'applications',
                'latestApplication',
                'visitCount',
                'firstVisit',
                'lastVisit',
                'lastActivity'
            )
        );
    }

    /**
     * Update consultant remarks for a visitor.
     */
    public function updateRemarks(
        Request $request,
        Visitor $visitor
    ): RedirectResponse {
        abort_if(
            (int) $visitor->consultant_id !==
            (int) $request->user()->id,
            403
        );

        $validated = $request->validate([
            'remarks' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        $visitor->update([
            'remarks' =>
                $validated['remarks'] ?? null,
        ]);

        return redirect()
            ->route(
                'consultant.visitors.show',
                $visitor
            )
            ->with(
                'status',
                'Visitor remarks updated successfully.'
            );
    }

    /**
     * Update lead status and consultant remarks.
     *
     * Lead statuses:
     *
     * new
     * contacted
     * interested
     * converted
     * not_interested
     */
    public function updateLead(
        Request $request,
        Visitor $visitor
    ): RedirectResponse {
        abort_if(
            (int) $visitor->consultant_id !==
            (int) $request->user()->id,
            403
        );

        $validated = $request->validate([
            'lead_status' => [
                'required',
                'string',
                'in:new,contacted,interested,converted,not_interested',
            ],

            'consultant_remarks' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        $newStatus =
            $validated['lead_status'];

        $contactedAt =
            $visitor->contacted_at;

        $convertedAt =
            $visitor->converted_at;

        if (
            in_array(
                $newStatus,
                [
                    'contacted',
                    'interested',
                    'converted',
                ],
                true
            )
            && !$contactedAt
        ) {
            $contactedAt = now();
        }

        if ($newStatus === 'converted') {
            if (!$convertedAt) {
                $convertedAt = now();
            }
        } else {
            $convertedAt = null;
        }

        $visitor->update([
            'lead_status' =>
                $newStatus,

            'consultant_remarks' =>
                $validated['consultant_remarks'] ?? null,

            'contacted_at' =>
                $contactedAt,

            'converted_at' =>
                $convertedAt,
        ]);

        return redirect()
            ->route(
                'consultant.visitors.show',
                $visitor
            )
            ->with(
                'status',
                'Lead information updated successfully.'
            );
    }
}