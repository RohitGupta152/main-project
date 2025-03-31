<?php

namespace App\Services\Orders\Edit;

use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class OrderCancelService
{
    protected $OrderRepositoryInterface;

    public function __construct(OrderRepositoryInterface $OrderRepositoryInterface)
    {
        $this->OrderRepositoryInterface = $OrderRepositoryInterface;
    }

    public function cancelOrder(array $orderData)
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

        // Update order to status = 2 and is_delete = 2
        $this->OrderRepositoryInterface->updateCancellation($order);

        return [
            'status' => 'success',
            'message' => 'Order cancelled successfully',
            'status_code' => 200
        ];
    }
}
