<?php

namespace App\Services\Orders\Get;

use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Storage;

class OrderGetService
{
    protected $OrderRepositoryInterface;

    public function __construct(OrderRepositoryInterface $OrderRepositoryInterface)
    {
        $this->OrderRepositoryInterface = $OrderRepositoryInterface;
    }

    public function getOrders(array $filters): array
    {
        $orders = $this->getOrdersData($filters);
        $formattedOrders = $this->formatOrders($orders);

        return [
            'status' => 'success',
            'data' => $formattedOrders,
            'status_code' => 200
        ];
    }

    public function getOrdersData(array $filters)
    {
        $userId = Auth::id();

        $orders = $this->OrderRepositoryInterface->getOrders($filters, $userId);
        // $orders =null;
        // dd($orders);
        // dd($orders->toArray());
        // dd(($orders)->isEmpty());

        // if (($orders)->isEmpty()) {
        //     dd('no Data Found 1');
        // }
        // if (!$orders) {
        //     dd('no Data Found 2');
        // }
        // if (empty($orders)) {
        //     dd('no Data Found 3');
        // }

        return $orders;
    }

    public function formatOrders($orders): array 
    {
        $formattedOrders = [];

        foreach ($orders as $order) {
            $formattedProducts = [];

            foreach ($order->products as $product) {
                $formattedProducts[] = [
                    'product_name' => $product->product_name,
                    'price' => $product->price . " Rs",
                    'quantity' => $product->quantity . " Qty",
                ];
            }

            $formattedOrders[] = [
                'order_no' => $order->order_no,
                'customer_name' => $order->customer_name,
                'email' => $order->email,
                'charged_amount' => $order->charged_amount . " Rs",
                'weight' => $order->weight . " Kg",
                'length' => $order->length . " Cm",
                'width' => $order->width . " Cm",
                'height' => $order->height . " Cm",
                'contact_no' => $order->contact_no,
                'address1' => $order->address1,
                'address2' => $order->address2,
                'pin_code' => $order->pin_code,
                'city' => $order->city,
                'state' => $order->state,
                'country' => $order->country,
                'total_amount' => $order->total_amount . " Rs",
                'total_qty' => $order->total_qty . " Qty",
                'order_date' => date('d M y  h:i A', strtotime($order->created_date)),
                'status' => $order->status == 0 ? 'Order Delivered' : ($order->status == 1 ? 'Order Processing' : 'Order In Active'),
                'cancelled' => $order->is_deleted == 0 ? 'Order Active' : ($order->is_deleted == 1 ? 'Order Completed' : 'Order Cancelled'),
                'products' => $formattedProducts
            ];
        }

        return $formattedOrders;
    }
}
