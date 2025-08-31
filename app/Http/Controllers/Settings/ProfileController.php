<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use App\Utils\ImageHelper;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/Profile', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old image if it exists
            if ($user->profileImage) {
                Storage::delete($user->profileImage->path);
                $user->profileImage->delete();
            }
            $webpPath = ImageHelper::convert_to_webp($request->file('profile_image'));
            $image = $user->images()->create(['path' => $webpPath]);
            $user->profile_image_id = $image->id;
        }

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            // Delete old image if it exists
            if ($user->coverImage) {
                Storage::delete($user->coverImage->path);
                $user->coverImage->delete();
            }
            $webpPath = ImageHelper::convert_to_webp($request->file('cover_image'));
            $image = $user->images()->create(['path' => $webpPath]);
            $user->cover_image_id = $image->id;
        }

        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return to_route('profile.edit');
    }

    /**
     * Delete the user's profile.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
