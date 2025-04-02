<?php

namespace App\Services\Rates\Get;

use App\Repositories\Interfaces\RateChartRepositoryInterface;
use Rap2hpoutre\FastExcel\FastExcel;


class ExportGetService
{
    protected $rateChartRepository;
    public function __construct(RateChartRepositoryInterface $rateChartRepository)
    {
        $this->rateChartRepository = $rateChartRepository;
    }

    public function exportRates(array $filters)
    {
        $rateGetService = app(RateGetService::class);

        $data = $rateGetService->getRateData($filters);
        // dd($data->toArray());
        $formatData = $this->formatExportRate($data);
        // dd($formatData);

        $filePath = storage_path('app/public/filtered_Customers_Rates.csv');

        $exportData = [];
        foreach ($formatData as $Customer) {
            $exportData[] = [
                'Customer Name' => $Customer['name'],
                'Customer Email'   => $Customer['email'],
                'Weight (KG)'     => $Customer['weight'],
                'Rate Amount' => $Customer['rate_amount'],
                'Created On'      => $Customer['created_date'],
                'Updated On'      => $Customer['updated_date']
            ];
        }

        (new FastExcel(collect($exportData)))->export($filePath);

        return [
            'message'   => 'Filtered Customer data exported successfully!',
            'file_path' => asset('storage/filtered_Customers.csv')
        ];
    }

    private function formatExportRate($data): array
    {
        $formattedRates = [];

        foreach ($data as $rate) {
            $formattedRates[] = [
                'name'        => $rate->user['name'] ?? 'Default Rate',
                'email'       => $rate->user['email'] ?? ' ',
                'weight'      => $rate->weight . " Kg",
                'rate_amount' => $rate->rate_amount . " Rs",
                'created_date'  => date('d-M-y  h:i A', strtotime($rate->created_date)),
                'updated_date'  => date('d-M-y  h:i A', strtotime($rate->updated_date))
            ];
        }

        return $formattedRates;
    }
    

}