<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    // public function checkWalletAmount(int $userId)
    // {
    //     $user = User::find($userId);
    //     return $user ? $user->wallet_amount : 0;
    // }

    // public function deductWalletAmount(int $userId, float $amount): void
    // {
    //     $user = User::find($userId);
    //     if ($user) {
    //         $user->wallet_amount -= $amount;
    //         $user->save();
    //     }
    // }



    // public function findById(string $userId)
    // {
    //     return User::find($userId);
    // }


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
