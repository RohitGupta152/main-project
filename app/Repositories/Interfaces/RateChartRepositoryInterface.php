<?php

namespace App\Repositories\Interfaces;

use App\Models\RateChart;


interface RateChartRepositoryInterface
{
    // public function create(array $data);

    public function getByUserId(int $userId);
    
    public function create(array $data): RateChart;
    

    // public function createRates(array $rates): bool;

    // public function getAll();

    public function getRates(array $filters);

    // public function findByUserId(array $userId);

    public function findById(int $rateId);
    
    public function updateById(int $rateId, array $updateData): bool;
    
    public function deleteById(int $rateId): bool;
    

    public function getRateForWeight(float $weight, int $userId);

    // public function getDefaultRateForWeight(float $weight);

    // public function getRateForUser(float $weight);
}

// interface RateChartRepositoryInterface
// {
//     public function getRateForUser(float $weight);
// }
