<?php

namespace App\Http\Controllers;

use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {}

    public function findByEmail(string $email)
    {
        $result = $this->userService->findByEmail($email);

        if (!$result['found']) {
            return response()->json([
                'message' => $result['message'],
            ], 404);
        }

        return response()->json($result['data']);
    }
}