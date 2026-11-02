<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated');
    }

    /**
     * Upload or replace the user's profile picture.
     */
    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'profile_photo' => [
                'required',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        $user = $request->user();

        $file = $request->file('profile_photo');

        if (! $file || ! $file->isValid()) {
            return Redirect::route('profile.edit')
                ->withErrors([
                    'profile_photo' => 'The selected image could not be uploaded. Please choose another image.',
                ]);
        }

        /*
         * Store the new image first.
         *
         * This is intentional: if the new upload fails,
         * the old profile picture is not deleted.
         */
        $newPath = $file->store(
            'profile-photos',
            'public'
        );

        if (! $newPath) {
            return Redirect::route('profile.edit')
                ->withErrors([
                    'profile_photo' => 'The profile picture could not be saved. Please try again.',
                ]);
        }

        /*
         * Keep the old path temporarily so we can
         * delete the old image only after the new one
         * has been successfully stored.
         */
        $oldPath = $user->profile_photo_path;

        /*
         * Save the new path in the database.
         */
        $user->profile_photo_path = $newPath;
        $user->save();

        /*
         * Delete the previous image after the database
         * has successfully been updated.
         */
        if ($oldPath && $oldPath !== $newPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return Redirect::route('profile.edit')
            ->with('status', 'profile-photo-updated');
    }

    /**
     * Remove the user's profile picture.
     */
    public function deletePhoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        $oldPath = $user->profile_photo_path;

        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        $user->profile_photo_path = null;
        $user->save();

        return Redirect::route('profile.edit')
            ->with('status', 'profile-photo-deleted');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        /*
         * Delete profile picture before deleting account.
         */
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete(
                $user->profile_photo_path
            );
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}