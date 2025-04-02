<?php

namespace App\Services\Rates\Edit;

use App\Repositories\Interfaces\RateChartRepositoryInterface;


class RateDeleteService
{
    protected $rateChartRepository;
    public function __construct(RateChartRepositoryInterface $rateChartRepository)
    {
        $this->rateChartRepository = $rateChartRepository;
    }

    public function deleteRate(int $rateId): array
    {
        $rate = $this->rateChartRepository->findByRateId($rateId);

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
