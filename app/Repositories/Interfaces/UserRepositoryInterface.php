<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface UserRepositoryInterface
{
    public function getWalletBalance(int $userId): float;
    public function updateWalletBalance(string $userId, float $newBalance);
}
