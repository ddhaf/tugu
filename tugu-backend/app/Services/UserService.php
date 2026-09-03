<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;

class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function findByEmail(string $email): array
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            return [
                'found' => false,
                'data' => null,
                'message' => 'User not found',
            ];
        }

        return [
            'found' => true,
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'message' => null,
        ];
    }

    public function findByName(string $name)
    {
        return $this->userRepository->findByName($name);
    }
}