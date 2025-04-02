<?php

namespace App\Services\Rates\Get;

use App\Repositories\Interfaces\RateChartRepositoryInterface;


class RateGetService
{
    protected $rateChartRepository;
    public function __construct(RateChartRepositoryInterface $rateChartRepository)
    {
        $this->rateChartRepository = $rateChartRepository;
    }

    public function getRates(array $filters): array
    {
        $getData = $this->getRateData($filters);
        $formattedRates = $this->formatRates($getData);

        return [
            'status' => 'success',
            'data' => $formattedRates,
            'status_code' => 200
        ];
    }

    public function getRateData(array $filters)
    {
        $rate = $this->rateChartRepository->getRates($filters);
        // dd($student);

        // if (empty($student)) {
        //     throw new NotFoundHttpException('No students found.');
        // }

        if ($rate->isEmpty()) {
            return [];
        }

        return $rate;
    }

    public function formatRates($rates): array
    {
        $formattedRates = [];

        foreach ($rates as $rate) {
            $formattedRates[] = [
                'weight'      => $rate->weight . " Kg",
                'rate_amount' => $rate->rate_amount . " Rs",
                'created_date'  => date('d-M-y  h:i A', strtotime($rate->created_date)),
                'updated_date'  => date('d-M-y  h:i A', strtotime($rate->updated_date))
            ];
        }

        return $formattedRates;
    }


}