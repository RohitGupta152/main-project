<?php

namespace App\Services\Rates\Edit;

use App\Repositories\Interfaces\RateChartRepositoryInterface;


class RateUpdateService
{
    protected $rateChartRepository;
    public function __construct(RateChartRepositoryInterface $rateChartRepository)
    {
        $this->rateChartRepository = $rateChartRepository;
    }

    public function updateRate(int $rateId, array $rateData): array
    {
        // Fetch the existing rate by ID
        $existingRate = $this->rateChartRepository->findByRateId($rateId);
        // dd($existingRate);

        if (!$existingRate) {
            return [
                'status' => "error",
                'message' => 'Rate not found.',
                'status_code' => 404
            ];
        }
        // dd($existingRate->toArray());

        $userId = $rateData['user_id'];
        // dd($userId);

        // Check if the user exists in the database
        $userRates = $this->rateChartRepository->getByUserId($userId);
        // dd($userRates->toArray());
        if ($userRates->isEmpty()) {
            return [
                'status' => "error",
                'message' => "User ID {$userId} does not exist in the database.",
                'status_code' => 400
            ];
        }
        // dd($userRates->toArray());

        // Apply rounding logic
        $originalWeight = (float) $rateData['weight'];
        $decimalPart = $originalWeight - floor($originalWeight);
        if ($decimalPart > 0 && $decimalPart <= 0.5) {
            $roundedWeight = floor($originalWeight) + 0.5;
        } elseif ($decimalPart > 0.5) {
            $roundedWeight = ceil($originalWeight);
        } else {
            $roundedWeight = $originalWeight;
        }


        // Check if a rate with the same user_id, rounded weight, and rate_amount already exists
        // foreach ($userRates as $existing) {
        //     if ($existing->weight == $roundedWeight && $existing->rate_amount == $rateData['rate_amount']) {
        //         return [
        //             'status' => "error",
        //             'message' => "A rate with weight {$roundedWeight} and amount {$rateData['rate_amount']} already exists for user ID {$userId}.",
        //             'status_code' => 400
        //         ];
        //     }
        //     // elseif($existing->weight == $roundedWeight && $existing->rate_amount != $rateData['rate_amount']){
        //     //     return [
        //     //         'status' => "error",
        //     //         'message' => "A rate with weight {$roundedWeight} already exists for user ID {$userId}.",
        //     //         'status_code' => 400
        //     //     ];
        //     // }
        //     // elseif($existing->rate_amount == $rateData['rate_amount'] && $existing->weight != $roundedWeight){
        //     //     return [
        //     //         'status' => "error",
        //     //         'message' => "A rate with amount {$rateData['rate_amount']} already exists for user ID {$userId}.",
        //     //         'status_code' => 400
        //     //     ];
        //     // }
        // }

        // Prepare update data

        $updateData = [
            'weight' => $roundedWeight,
            'rate_amount' => $rateData['rate_amount']
        ];

        // Update the rate
        $updated = $this->rateChartRepository->updateById($rateId, $updateData);

        if (!$updated) {
            return [
                'status' => "error",
                'message' => 'Failed to update rate.',
                'status_code' => 500
            ];
        }

        return [
            'status' => "success",
            'message' => 'Rate updated successfully.',
            'status_code' => 200
        ];
    }
}
