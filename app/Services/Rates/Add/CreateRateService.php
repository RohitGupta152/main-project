<?php

namespace App\Services\Rates\Add;

use App\Repositories\Interfaces\RateChartRepositoryInterface;


class CreateRateService
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
                'created_date' => now(),
                'updated_date' => now()
            ]);
        }

        return [
            'status' => "success",
            'message' => 'Rates created successfully.',
            'status_code' => 201
        ];
    }

}