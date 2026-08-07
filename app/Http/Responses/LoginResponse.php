<?php

namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        $user = $request->user();

        if ($user->stores()->doesntExist()) {
            return redirect()->route('stores.create');
        }

        $domain = config('app.domain', 'localhost');
        $store = $user->currentStore ?? $user->stores()->first();
        $url = "http://{$store->slug}.{$domain}";

        return redirect()->away($url);
    }
}
