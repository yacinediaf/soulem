<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Redirect the user to the Google OAuth page.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the Google OAuth callback.
     */
    public function callback(CreateNewUser $createNewUser): RedirectResponse
    {
        $socialUser = Socialite::driver('google')->user();

        $user = $createNewUser->createOrFindFromSocialite([
            'id' => $socialUser->getId(),
            'name' => $socialUser->getName(),
            'email' => $socialUser->getEmail(),
        ]);

        auth()->login($user);

        return redirect()->intended(route('dashboard'));
    }
}
