<?php

namespace App\Repositories\Interfaces;

use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\GetOrderRequest;
use App\Http\Requests\GetOrdersByEmailRequest;
use App\Models\Order;



interface OrderRepositoryInterface
{
    public function createOrder(array $orderData);
    public function checkExistingOrder(string $orderId, string $user_id);
    public function getActiveOrders(int $userId);
    public function getOrdersByEmail(string $email, int $userid);
    public function getOrders(array $filters, int $userId);
    public function findOrder(array $orderData, int $userId);
    public function updateOrder(Order $order, array $orderData);
    public function deleteOrder(Order $order);
}
