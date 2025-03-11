<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        $user = $request->user();
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            'user' => [
                'name' => $user->name ?? '',
                'email' => $user->email ?? '',
                'title' => $user->title ?? '',
                'fname' => $user->fname ?? '',
                'lname' => $user->lname ?? '',
                'birthday' => $user->birthday ?? '',
                'id_card' => $user->id_card ?? '',
                'student_code' => $user->student_code ?? '',
                'officer_code' => $user->officer_code ?? '',
                'institution' => $user->institution ?? '',
                'branch' => $user->branch ?? '',
                'faculty' => $user->faculty ?? '',
                'phone' => $user->phone ?? '',
                'image' => $user->image ?? null,
            ],
        ]);
    }

   /**
 * Update the user's profile information.
 */
public function update(ProfileUpdateRequest $request): RedirectResponse
{
    $user = $request->user();
    $data = $request->validated();
 
    $user->fill($data); 
    if ($user->isDirty('email')) {
        $user->email_verified_at = null;
    }

    $user->save();
 
    return Redirect::route('profile.edit');
}


    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
