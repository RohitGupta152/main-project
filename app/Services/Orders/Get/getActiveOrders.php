<?php

namespace App\Services\Orders\Get;

use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Storage;
use App\Services\Orders\Get\OrderGetService;

class getActiveOrders
{
    protected $OrderRepositoryInterface;

    public function __construct(OrderRepositoryInterface $OrderRepositoryInterface)
    {
        $this->OrderRepositoryInterface = $OrderRepositoryInterface;
    }


    public function getActiveOrders()
    {
        $OrderGetService = app(OrderGetService::class);

        $userId = Auth::id();
        $orders = $this->OrderRepositoryInterface->getActiveOrders($userId);
        // dd($orders->toArray());

        if ($orders->isEmpty()) {
            return [
                'status' => 'success',
                'data' => [],
                'status_code' => 200
            ];
        }

        $formattedOrders = $OrderGetService->formatOrders($orders);
        // dd($formattedOrders);

        return [
            'status' => 'success',
            'data' => $formattedOrders,
            'status_code' => 200
        ];
    }
}
