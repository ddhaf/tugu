<?php

namespace App\Services;

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class AuthService
{
    public function login(string $login, string $password): ?string
    {
        $user = User::where('email', $login)
            ->orWhere('name', $login)
            ->first();

        if (!$user || !password_verify($password, $user->password)) {
            return null;
        }

        return auth('api')->login($user);
    }

    public function register(array $data): string
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        return auth('api')->login($user);
    }

    public function getCurrentUser(): ?User
    {
        return auth('api')->user();
    }

    public function redirectToGoogle()
{
    return Socialite::driver('google')
        ->stateless()
        ->redirect();
}

   public function handleGoogleCallback(): string
{
    $googleUser = Socialite::driver('google')
        ->stateless()
        ->user();

    $user = User::where('google_id', $googleUser->getId())
        ->first();

    if (!$user) {
        $user = User::where('email', $googleUser->getEmail())
            ->first();
    }

    if ($user) {
        if (!$user->google_id) {
            $user->google_id = $googleUser->getId();
            $user->save();
        }
    } else {
        $user = User::create([
            'name' => $googleUser->getName() ?? 'Google User',
            'email' => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'password' => \Illuminate\Support\Str::random(40),
        ]);
    }

    return auth('api')->login($user);
}

public function redirectToFacebook()
{
    return Socialite::driver('facebook')
        ->stateless()
        ->scopes(['public_profile'])
        ->redirect();
}

public function handleFacebookCallback(): string
{
    $facebookUser = Socialite::driver('facebook')
        ->stateless()
        ->user();

    $facebookId = $facebookUser->getId();

    $user = User::where('facebook_id', $facebookId)
        ->first();

    if (!$user) {
        $facebookEmail = 'facebook_' . $facebookId . '@facebook.local';

        $user = User::where('email', $facebookEmail)
            ->first();

        if ($user) {
            $user->facebook_id = $facebookId;
            $user->save();
        } else {
            $user = User::create([
                'name' => $facebookUser->getName() ?? 'Facebook User',
                'email' => $facebookEmail,
                'facebook_id' => $facebookId,
                'password' => \Illuminate\Support\Str::random(40),
            ]);
        }
    }

    return auth('api')->login($user);
}
}