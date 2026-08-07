<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
        ]);
    }

    /**
     * Find an existing user or create a new one from socialite data.
     *
     * @param  array{id: string, name: string, email: string}  $socialUser
     */
    public function createOrFindFromSocialite(array $socialUser): User
    {
        $user = User::firstWhere('email', $socialUser['email']);

        if ($user) {
            $user->update(['google_id' => $socialUser['id']]);

            return $user;
        }

        return User::create([
            'name' => $socialUser['name'],
            'email' => $socialUser['email'],
            'google_id' => $socialUser['id'],
        ]);
    }
}
