<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function getWalletBalance(int $userId): float
    {
        return User::where('id', $userId)->value('wallet_amount');
    }

    public function updateWalletBalance(string $userId, float $newBalance)
    {
        return User::where('id', $userId)->update([
            'wallet_amount' => $newBalance
        ]);
    }
}
