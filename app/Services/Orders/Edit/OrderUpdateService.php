<?php

namespace App\Services\Orders\Edit;

use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Support\Facades\Auth;


class OrderUpdateService
{
    protected $OrderRepositoryInterface;

    public function __construct(OrderRepositoryInterface $OrderRepositoryInterface)
    {
        $this->OrderRepositoryInterface = $OrderRepositoryInterface;
    }

    public function updateOrder(array $orderData)
    {
        $userId = Auth::id();
        $order = $this->OrderRepositoryInterface->findOrder($orderData, $userId)->first();

        if (!$order) {
            return [
                'status' => 'error',
                'message' => 'Order not found',
                'status_code' => 404
            ];
        }

        $this->OrderRepositoryInterface->updateOrder($order, $orderData);

        return [
            'status' => 'success',
            'message' => 'Order updated successfully',
            'status_code' => 200
        ];
    }
}
