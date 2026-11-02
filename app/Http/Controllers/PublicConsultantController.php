<?php

namespace App\Http\Controllers;

use App\Models\Consultant;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicConsultantController extends Controller
{
    /**
     * Display the public consultant directory.
     */
    public function index(Request $request): View
    {
        $countryAliases = [
            'United Kingdom' => [
                'United Kingdom',
                'UK',
                'U.K.',
                'Britain',
                'Great Britain',
            ],
            'United States' => [
                'United States',
                'USA',
                'U.S.A.',
                'US',
                'U.S.',
                'United States of America',
            ],
            'Canada' => ['Canada'],
            'Australia' => ['Australia'],
            'Sweden' => ['Sweden'],
            'Malaysia' => ['Malaysia'],
            'New Zealand' => ['New Zealand'],
            'Europe' => ['Europe'],
        ];

        $normaliseCountry = function ($country) use ($countryAliases) {
            $country = trim((string) $country);

            foreach ($countryAliases as $canonical => $aliases) {
                foreach ($aliases as $alias) {
                    if (strcasecmp($country, $alias) === 0) {
                        return $canonical;
                    }
                }
            }

            return $country;
        };

        $query = Consultant::query()
            ->where('is_active', true);

        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('company', 'like', '%' . $search . '%')
                    ->orWhere('city', 'like', '%' . $search . '%')
                    ->orWhereJsonContains('services', $search);
            });
        }

        if ($request->filled('city')) {
            $query->where('city', $request->input('city'));
        }

        if ($request->filled('country')) {
            $selectedCountry = $normaliseCountry(
                $request->input('country')
            );

            $validCountries = collect($countryAliases)
                ->keys()
                ->all();

            if (in_array($selectedCountry, $validCountries, true)) {
                $aliases = $countryAliases[$selectedCountry];

                $query->where(function ($q) use ($aliases) {
                    foreach ($aliases as $index => $alias) {
                        if ($index === 0) {
                            $q->whereJsonContains('destinations', $alias);
                        } else {
                            $q->orWhereJsonContains('destinations', $alias);
                        }
                    }
                });
            }
        }

        $consultants = $query
            ->orderByDesc('is_featured')
            ->orderBy('company')
            ->paginate(9)
            ->withQueryString();

        $cities = Consultant::query()
            ->where('is_active', true)
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        $rawDestinations = Consultant::query()
            ->where('is_active', true)
            ->get(['destinations'])
            ->flatMap(fn ($consultant) => $consultant->destinations ?? [])
            ->filter(fn ($country) => is_string($country) && trim($country) !== '');

        $countries = $rawDestinations
            ->map($normaliseCountry)
            ->unique()
            ->sort()
            ->values();

        return view('consultants.index', [
            'consultants' => $consultants,
            'cities' => $cities,
            'countries' => $countries,
        ]);
    }

    /**
     * Display a public consultant profile and record visits.
     */
    public function show(
        Request $request,
        Consultant $consultant
    ): View {
        abort_unless($consultant->is_active, 404);

        if ($consultant->user_id) {
            $user = $request->user();

            $isConsultantViewingOwnProfile =
                $user
                && (int) $user->id === (int) $consultant->user_id;

            if (!$isConsultantViewingOwnProfile) {
                Visitor::create([
                    'consultant_id' => $consultant->user_id,
                    'user_id' => $user?->id,
                    'visitor_name' => $user?->name ?? 'Guest Visitor',
                    'visitor_email' => $user?->email ?? null,
                    'source' => 'public_profile',
                    'visit_type' => 'online',
                    'country_interest' =>
                        $user?->studentProfile?->preferred_country ?? null,
                    'program_interest' =>
                        $user?->studentProfile?->preferred_subject ?? null,
                    'remarks' => null,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'visited_at' => now(),
                ]);
            }
        }

        return view('consultants.show', [
            'consultant' => $consultant,
        ]);
    }
}
