<?php

namespace App\Interfaces;

use App\Models\User;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?User;

    public function findByName(string $name): ?User;
}