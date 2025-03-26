<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Validation\Rule;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\GetOrderRequest;
use App\Http\Requests\GetOrdersByEmailRequest;
use App\Http\Requests\updateOrderRequest;
use App\Http\Requests\deleteOrderRequest;

use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;


class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }


    public function createOrder(CreateOrderRequest $request): JsonResponse
    {
        // return $this->orderService->createOrder($request);
        // $orderData = $request->only(['order_id', 'user_name', 'email']);

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

        $response = $this->orderService->createOrder($orderData, $products);

        return response()->json([
            'status' => $response['status'],
            'message' => $response['message']
        ], $response['status_code']);
    }

    public function getAllOrders(): JsonResponse
    {
        $response = $this->orderService->getAllOrders();

        return response()->json([
            'status' => $response['status'],
            'data' => $response['data']
        ], $response['status_code']);
    }

    public function getOrders(GetOrderRequest $request): JsonResponse
    {
        // $filters = $request->only(['order_id', 'user_name', 'created_date']);

        $filters['order_no'] = $request['order_id'];
        $filters['customer_name'] = $request['customer_name'];
        $filters['created_date'] = $request['created_date'];


        $response = $this->orderService->getOrders($filters);

        return response()->json([
            'status' => $response['status'],
            'data' => $response['data']
        ], $response['status_code']);
    }

    public function getOrdersByEmail(GetOrdersByEmailRequest $request): JsonResponse
    {
        // $email = $request->only(['email']); // ✅ Get email as a string
        $email = $request['email'];

        $response = $this->orderService->getOrdersByEmail($email);

        return response()->json([
            'status' => $response['status'],
            'data' => $response['data']
        ], $response['status_code']);
    }

    public function updateOrder(updateOrderRequest $request): JsonResponse
    {
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


        $response = $this->orderService->updateOrder($orderData);

        return response()->json([
            'status' => $response['status'],
            'message' => $response['message'],
        ], $response['status_code']);
    }

    public function deleteOrder(DeleteOrderRequest $request): JsonResponse
    {
        $orderData['order_no'] = $request['order_no'];

        $response = $this->orderService->deleteOrder($orderData);

        return response()->json([
            'status' => $response['status'],
            'message' => $response['message'],
        ], $response['status_code']);
    }
}
