<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect($provider)
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            abort(404);
        }
        return Socialite::driver($provider)->redirect();
    }

public function callback(Request $request, $provider)
{
    $socialUser = Socialite::driver($provider)->user();

    $user = User::where('provider', $provider)->where('provider_id', $socialUser->getId())->first();

    if ($user) {
        Auth::login($user, true);
        session()->flash('success', 'Login successful!');
        return redirect()->route('dashboard');
    }

    $user = User::where('email', $socialUser->getEmail())->first();

    if ($user) {
        $user->update([
            'name' => $socialUser->getName() ?? $user->name,
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
        ]);
    } else {
        $user = User::create([
            'name' => $socialUser->getName() ?? 'User',
            'email' => $socialUser->getEmail(),
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'password' => Hash::make(\Str::random(16)),
        ]);
    }

    Auth::login($user, true);
    session()->flash('success', 'Login successful!');
    return redirect()->route('dashboard');
}

}
