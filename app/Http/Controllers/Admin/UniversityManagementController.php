<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\University;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UniversityManagementController extends Controller
{
    public function index(): View
    {
        $universities = University::orderBy('name')->paginate(10);

        return view('admin.universities.index', compact('universities'));
    }

    public function create(): View
    {
        return view('admin.universities.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        University::create($data);

        return redirect()
            ->route('admin.universities.index')
            ->with('status', 'University added successfully.');
    }

    public function edit(University $university): View
    {
        return view('admin.universities.edit', compact('university'));
    }

    public function update(
        Request $request,
        University $university
    ): RedirectResponse {
        $data = $this->validatedData($request, $university);

        $university->update($data);

        return redirect()
            ->route('admin.universities.index')
            ->with('status', 'University updated successfully.');
    }

    private function validatedData(
        Request $request,
        ?University $university = null
    ): array {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'degree_level' => ['required', 'string', 'max:100'],
            'subject' => ['required', 'string', 'max:150'],
            'tuition_fee' => ['required', 'numeric', 'min:0'],
            'estimated_living_cost' => ['required', 'numeric', 'min:0'],
            'min_cgpa' => ['required', 'numeric', 'min:0', 'max:4'],
            'min_english_score' => ['required', 'numeric', 'min:0'],
            'english_test_type' => ['required', 'string', 'max:100'],
            'intake' => ['required', 'string', 'max:100'],
            'ranking' => ['nullable', 'integer', 'min:1'],
            'scholarship_available' => ['nullable', 'boolean'],
            'post_study_work' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string', 'max:10000'],
            'website_url' => ['nullable', 'url', 'max:2048'],
            'application_url' => ['nullable', 'url', 'max:2048'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
        ]);
    }
}
