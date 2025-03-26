<?php

namespace App\Services;

use App\Repositories\Interfaces\RateChartRepositoryInterface;
use Illuminate\Support\Facades\DB;
use GrahamCampbell\ResultType\Success;

class RateChartService
{
    protected $rateChartRepository;
    public function __construct(RateChartRepositoryInterface $rateChartRepository)
    {
        $this->rateChartRepository = $rateChartRepository;
    }


    public function createRates(int $userId, array $rateDataArray): array
    {
        $existingRates = $this->rateChartRepository->getByUserId($userId);

        $existingWeights = [];
        foreach ($existingRates as $rate) {
            $existingWeights[] = (float) $rate['weight'];
        }

        $RoundWeights = [];
        foreach ($rateDataArray as $rateData) {
            $originalWeight = (float) $rateData['weight']; // Convert user input to float

            // Apply rounding logic
            $decimalPart = $originalWeight - floor($originalWeight);
            if ($decimalPart > 0 && $decimalPart <= 0.5) {
                $roundedWeight = floor($originalWeight) + 0.5;
            } elseif ($decimalPart > 0.5) {
                $roundedWeight = ceil($originalWeight);
            } else {
                $roundedWeight = $originalWeight;
            }
            // dd($roundedWeight);

            // Check if the rounded weight is already present in $RoundWeights
            if (in_array($roundedWeight, $RoundWeights, true)) {
                return [
                    'status' => "error",
                    'message' => "Duplicate rounded weight {$roundedWeight} found in the request. Each rounded weight must be unique.",
                    'status_code' => 400
                ];
            }

            $RoundWeights[] = $roundedWeight;
        }

        // // Ensure uniqueness of rounded weights in request
        // if (count($RoundWeights) !== count(array_unique($RoundWeights))) {
        //     return [
        //         'status' => false,
        //         'message' => "Duplicate rounded weights found in the request. Each rounded weight must be unique.",
        //         'status_code' => 400
        //     ];
        // }


        $duplicateWeights = array_intersect($existingWeights, $RoundWeights);
        // dd($duplicateWeights);

        if (!empty($duplicateWeights)) {
            return [
                'status' => "error",
                'message' => "Rates with weights " . implode(', ', $duplicateWeights) . " already exist for this user_id.",
                'status_code' => 400
            ];
        }

        // Create the Rate chart table
        foreach ($rateDataArray as $rateData) {
            $originalWeight = (float) $rateData['weight'];

            // Apply rounding logic again before insertion
            $decimalPart = $originalWeight - floor($originalWeight);
            if ($decimalPart > 0 && $decimalPart <= 0.5) {
                $rateData['weight'] = floor($originalWeight) + 0.5;
            } elseif ($decimalPart > 0.5) {
                $rateData['weight'] = ceil($originalWeight);
            }

            // Save each rate using the `create` method
            $this->rateChartRepository->create([
                'user_id' => $userId,
                'weight' => $rateData['weight'],
                'rate_amount' => $rateData['rate_amount'],
            ]);
        }

        return [
            'status' => "success",
            'message' => 'Rates created successfully.',
            'status_code' => 201
        ];
    }

    public function getRates(array $filters): array
    {
        $rates = $this->rateChartRepository->getRates($filters);

        if ($rates->isEmpty()) {
            return [
                'status' => 'success',
                'data' => [],
                'status_code' => 200
            ];
        }

        $response = [];

        foreach ($rates as $rate) {
            $rateData = [
                'user_id' => $rate->user_id,
                'weight' => $rate->weight,
                'rate_amount' => $rate->rate_amount,
                'created_at' => date('d-M-y  h:i A', strtotime($rate->created_at))
            ];

            $response[] = $rateData;
        }

        return [
            'status' => 'success',
            'data' => $response,
            'status_code' => 200
        ];
    }

    public function updateRate(int $rateId, array $rateData): array
    {
        // Fetch the existing rate by ID
        $existingRate = $this->rateChartRepository->findById($rateId);
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

        // Check if the user exists in the database
        $userRates = $this->rateChartRepository->getByUserId($userId);
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

    public function deleteRate(int $rateId): array
    {
        $rate = $this->rateChartRepository->findById($rateId);

        if (!$rate) {
            return [
                'status' => "error",
                'message' => 'Rate not found.',
                'status_code' => 404
            ];
        }

        $deleted = $this->rateChartRepository->deleteById($rateId);

        if (!$deleted) {
            return [
                'status' => "error",
                'message' => 'Failed to delete rate.',
                'status_code' => 500
            ];
        }

        return [
            'status' => "success",
            'message' => 'Rate deleted successfully.',
            'status_code' => 200
        ];
    }
}
