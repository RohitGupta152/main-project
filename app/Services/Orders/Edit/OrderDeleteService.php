<?php

namespace App\Services\Orders\Edit;

use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Support\Facades\Auth;


class OrderDeleteService
{
    protected $OrderRepositoryInterface;

    public function __construct(OrderRepositoryInterface $OrderRepositoryInterface)
    {
        $this->OrderRepositoryInterface = $OrderRepositoryInterface;
    }

    public function deleteOrder(array $orderData)
    {
        $userId = Auth::id();
        $order = $this->OrderRepositoryInterface->findOrder($orderData, $userId)->first();
        // dd($order->toArray());
        if (!$order) {
            return [
                'status' => 'error',
                'message' => 'Order not found',
                'status_code' => 404
            ];
        }

        $this->OrderRepositoryInterface->deleteOrder($order);

        return [
            'status' => 'success',
            'message' => 'Order deleted successfully',
            'status_code' => 200
        ];
    }
}
