<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $token = $this->authService->login(
            $credentials['login'],
            $credentials['password']
        );

        if (!$token) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $token = $this->authService->register($validated);

        return response()->json([
            'message' => 'Registration successful',
            'token' => $token,
        ], 201);
    }

    public function me()
    {
        return response()->json([
            'user' => $this->authService->getCurrentUser(),
        ]);
    }

    public function googleRedirect()
{
    return $this->authService->redirectToGoogle();
}

    public function googleCallback()
    {
        $token = $this->authService->handleGoogleCallback();
        $frontendUrl = rtrim(config('services.frontend.url', 'http://localhost:5173'), '/');

        return redirect(
            $frontendUrl . '/oauth/google/callback#token=' . $token
        );
    }

    public function facebookRedirect()
    {
        return $this->authService->redirectToFacebook();
    }

    public function facebookCallback()
    {
        $token = $this->authService->handleFacebookCallback();
        $frontendUrl = rtrim(config('services.frontend.url', 'http://localhost:5173'), '/');

        return redirect(
            $frontendUrl . '/oauth/facebook/callback#token=' . $token
        );
    }
}