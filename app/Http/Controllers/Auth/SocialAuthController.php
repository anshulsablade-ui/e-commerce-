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

        // Facebook needs explicit email scope
        if ($provider === 'facebook') {
            return Socialite::driver('facebook')
                ->stateless()
                ->scopes(['email'])
                ->redirect();
        }

        // Google works without explicit scopes
        return Socialite::driver('google')
            ->stateless()
            ->redirect();
    }

    public function callback(Request $request, $provider)
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            abort(404);
        }

        $socialUser = Socialite::driver($provider)
            ->stateless()
            ->user();

        $user = User::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if (!$user) {
            $user = User::create([
                'name' => $socialUser->getName() ?? 'User',
                'email' => $socialUser->getEmail(), // may be null
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'password' => Hash::make(\Str::random(16)),
            ]);
        }

        Auth::login($user, true);

        return redirect()->route('dashboard');
    }
}
