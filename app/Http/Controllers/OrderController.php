<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\GetOrderRequest;
use App\Http\Requests\GetOrdersByEmailRequest;
use App\Http\Requests\updateOrderRequest;
use App\Http\Requests\deleteOrderRequest;
use App\Services\Orders\Add\createOrder;
use App\Services\Orders\Edit\OrderCancelService;
use App\Services\OrderService;
use App\Services\Orders\Get\OrderGetService;
use App\Services\Orders\Edit\OrderUpdateService;
use App\Services\Orders\Edit\OrderDeleteService;
use App\Services\Orders\Get\getActiveOrders;
use App\Services\Orders\Get\OrderExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;


class OrderController extends Controller
{

    // protected $orderService;

    // public function __construct(OrderService $orderService)
    // {
    //     $this->orderService = $orderService;
    // }

    // public function createOrder(CreateOrderRequest $request): JsonResponse
    // {
    //     $orderData['user_id'] = Auth::id();
    //     $orderData['order_no'] = $request['order_id'];
    //     $orderData['customer_name'] = $request['customer_name'];
    //     $orderData['email'] = $request['email'];
    //     $orderData['contact_no'] = $request['contact_no'];
    //     $orderData['address1'] = $request['address1'];
    //     $orderData['address2'] = $request['address2'];
    //     $orderData['pin_code'] = $request['pin_code'];
    //     $orderData['city'] = $request['city'];
    //     $orderData['state'] = $request['state'];
    //     $orderData['country'] = $request['country'];

    //     $orderData['weight'] = $request['weight'];
    //     $orderData['length'] = $request['length'];
    //     $orderData['width'] = $request['width'];
    //     $orderData['height'] = $request['height'];

    //     $products = $request->input('products');

    //     $response = $this->orderService->createOrder($orderData, $products);

    //     return response()->json([
    //         'status' => $response['status'],
    //         'message' => $response['message']
    //     ], $response['status_code']);
    // }


    // public function getOrders(GetOrderRequest $request): JsonResponse
    // {
    //     $filters['order_no'] = $request['order_no'];
    //     $filters['customer_name'] = $request['customer_name'];
    //     $filters['created_date'] = $request['created_date'];

    //     $response = $this->orderService->getOrders($filters);

    //     return response()->json([
    //         'status' => $response['status'],
    //         'data' => $response['data']
    //     ], $response['status_code']);
    // }


    // public function getActiveOrders(): JsonResponse
    // {
    //     $response = $this->orderService->getActiveOrders();

    //     return response()->json([
    //         'status' => $response['status'],
    //         'data' => $response['data']
    //     ], $response['status_code']);
    // }


    // public function exportOrders(Request $request): JsonResponse
    // {
    //     $filters['order_no'] = $request['order_no'];
    //     $filters['customer_name'] = $request['customer_name'];
    //     $filters['created_date'] = $request['created_date'];

    //     $response = $this->orderService->exportOrders($filters);

    //     return response()->json([
    //         'status'    => $response['status'],
    //         'message'   => $response['message'],
    //         'file_path' => $response['file_path']
    //     ]);
    // }


    // public function getOrdersByEmail(GetOrdersByEmailRequest $request): JsonResponse
    // {
    //     // $email = $request->only(['email']); // ✅ Get email as a string
    //     $email = $request['email'];

    //     $response = $this->orderService->getOrdersByEmail($email);

    //     return response()->json([
    //         'status' => $response['status'],
    //         'data' => $response['data']
    //     ], $response['status_code']);
    // }

    // public function updateOrder(updateOrderRequest $request): JsonResponse
    // {
    //     $orderData['order_no'] = $request['order_no'];
    //     $orderData['customer_name'] = $request['customer_name'];
    //     $orderData['email'] = $request['email'];
    //     $orderData['contact_no'] = $request['contact_no'];
    //     $orderData['address1'] = $request['address1'];
    //     $orderData['address2'] = $request['address2'];
    //     $orderData['pin_code'] = $request['pin_code'];
    //     $orderData['city'] = $request['city'];
    //     $orderData['state'] = $request['state'];
    //     $orderData['country'] = $request['country'];

    //     $response =  $this->orderService->updateOrder($orderData);

    //     return response()->json([
    //         'status' => $response['status'],
    //         'message' => $response['message'],
    //     ], $response['status_code']);
    // }

    // public function deleteOrder(DeleteOrderRequest $request): JsonResponse
    // {
    //     $orderData['order_no'] = $request['order_no'];

    //     $response =  $this->orderService->deleteOrder($orderData);

    //     return response()->json([
    //         'status' => $response['status'],
    //         'message' => $response['message'],
    //     ], $response['status_code']);
    // }




    public function createOrder(CreateOrderRequest $request): JsonResponse
    {
        $createOrder = app(createOrder::class);

        $orderData['user_id'] = Auth::id();
        $orderData['order_no'] = $request['order_id'];
        $orderData['customer_name'] = $request['customer_name'];
        $orderData['email'] = $request['email'];
        $orderData['contact_no'] = $request['contact_no'];
        $orderData['address1'] = $request['address1'];
        $orderData['address2'] = $request['address2'];
        $orderData['pin_code'] = $request['pin_code'];
        $orderData['city'] = $request['city'];
        $orderData['state'] = $request['state'];
        $orderData['country'] = $request['country'];

        $orderData['weight'] = $request['weight'];
        $orderData['length'] = $request['length'];
        $orderData['width'] = $request['width'];
        $orderData['height'] = $request['height'];
        $products = $request->input('products');

        $response = $createOrder->createOrder($orderData, $products);
        return response()->json([
            'status' => $response['status'],
            'message' => $response['message']
        ], $response['status_code']);
    }

    public function getOrders(GetOrderRequest $request): JsonResponse
    {
        $OrderGetService = app(OrderGetService::class);

        $filters['order_no'] = $request['order_no'];
        $filters['customer_name'] = $request['customer_name'];
        $filters['created_date'] = $request['created_date'];

        $response = $OrderGetService->getOrders($filters);

        return response()->json([
            'status' => $response['status'],
            'data' => $response['data']
        ], $response['status_code']);
    }

    public function getActiveOrders(): JsonResponse
    {
        $getActiveOrders = app(getActiveOrders::class);

        $response = $getActiveOrders->getActiveOrders();

        return response()->json([
            'status' => $response['status'],
            'data' => $response['data']
        ], $response['status_code']);
    }

    public function exportOrders(Request $request): JsonResponse
    {
        $OrderExportService = app(OrderExportService::class);

        $filters['order_no'] = $request['order_no'];
        $filters['customer_name'] = $request['customer_name'];
        $filters['created_date'] = $request['created_date'];

        $response = $OrderExportService->exportOrders($filters);

        return response()->json([
            'status'    => $response['status'],
            'message'   => $response['message'],
            'file_path' => $response['file_path']
        ]);
    }

    public function updateOrder(updateOrderRequest $request): JsonResponse
    {

        $OrderUpdateService = app(OrderUpdateService::class);

        $orderData['order_no'] = $request['order_no'];
        $orderData['customer_name'] = $request['customer_name'];
        $orderData['email'] = $request['email'];
        $orderData['contact_no'] = $request['contact_no'];
        $orderData['address1'] = $request['address1'];
        $orderData['address2'] = $request['address2'];
        $orderData['pin_code'] = $request['pin_code'];
        $orderData['city'] = $request['city'];
        $orderData['state'] = $request['state'];
        $orderData['country'] = $request['country'];

        $response = $OrderUpdateService->updateOrder($orderData);

        return response()->json([
            'status' => $response['status'],
            'message' => $response['message'],
        ], $response['status_code']);
    }

    public function deleteOrder(DeleteOrderRequest $request): JsonResponse
    {
        $OrderDeleteService = app(OrderDeleteService::class);
        $orderData['order_no'] = $request['order_no'];

        $response = $OrderDeleteService->deleteOrder($orderData);

        return response()->json([
            'status' => $response['status'],
            'message' => $response['message'],
        ], $response['status_code']);
    }

    public function cancelOrder(Request $request): JsonResponse
    {
        $OrderCancelService = app(OrderCancelService::class);

        $orderData['order_no'] = $request['order_no'];

        $response = $OrderCancelService->cancelOrder($orderData);

        return response()->json([
            'status' => $response['status'],
            'message' => $response['message'],
        ], $response['status_code']);
    }
}
