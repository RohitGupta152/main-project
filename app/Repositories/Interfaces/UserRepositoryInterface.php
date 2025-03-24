<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface UserRepositoryInterface
{
    // public function findUserById(int $userId);

    // public function findById(string $userId);

    public function getWalletBalance(int $userId): float;
    public function updateWalletBalance(string $userId, float $newBalance);
}
