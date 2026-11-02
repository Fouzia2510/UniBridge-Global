<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsultantProfileController extends Controller
{
    /**
     * Show consultant profile.
     */
    public function show(Request $request): View
    {
        return $this->edit($request);
    }

    /**
     * Show consultant profile edit page.
     */
    public function edit(Request $request): View
    {
        $profile = $request->user()->consultantProfile;

        return view('consultant.profile', compact('profile'));
    }

    /**
     * Create or update consultant profile.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'qualification' => [
                'nullable',
                'string',
                'max:255',
            ],

            'specialization' => [
                'nullable',
                'string',
                'max:255',
            ],

            'experience_years' => [
                'nullable',
                'numeric',
                'min:0',
                'max:99.9',
            ],

            'countries' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'languages' => [
                'nullable',
                'string',
                'max:255',
            ],

            'about' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $request->user()->consultantProfile()->updateOrCreate(
            [
                'user_id' => $request->user()->id,
            ],
            $validated
        );

        return redirect()
            ->route('consultant.profile.edit')
            ->with(
                'status',
                'Consultant profile updated successfully!'
            );
    }
}