<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\CreateOrderRequest;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Http\Requests\GetOrderRequest;
use App\Http\Requests\GetOrdersByEmailRequest;
use Carbon\Carbon;
use Illuminate\Support\Collection;

// use Illuminate\Database\Eloquent\Collection;

class OrderRepository implements OrderRepositoryInterface
{

    // public function createOrder(CreateOrderRequest $request)
    // {
    //     // Check if the same order_id exists for the same email
    //     $existingOrder = Order::where('order_id', $request->order_id)
    //                           ->where('email', $request->email)
    //                           ->exists();

    //     if ($existingOrder) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'You cannot use this Order ID again for the same email.'
    //         ], 400);
    //     }

    //     $totalAmount = 0;
    //     $totalQty = 0;

    //     foreach ($request->products as $product) {
    //         $totalAmount += $product['price'] * $product['quantity'];
    //         $totalQty += $product['quantity'];
    //     }

    //     // Create Order
    //     $order = Order::create([
    //         'order_id' => $request->order_id,
    //         'user_name' => $request->user_name,
    //         'email' => $request->email,
    //         'total_amount' => $totalAmount,
    //         'total_qty' => $totalQty
    //     ]);

    //     // Create Product Entries
    //     foreach ($request->products as $product) {
    //         Product::create([
    //             'order_table_id' => $order->id,
    //             'order_id' => $request->order_id,
    //             'product_name' => $product['product_name'],
    //             'price' => $product['price'],
    //             'quantity' => $product['quantity']
    //         ]);
    //     }

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Order created successfully'
    //     ], 201);
    // }



    // public function getAllOrders()
    // {
    //     $orders = Order::with('products')->where('is_active', 1)
    //         ->where('is_deleted', 0)->get();

    //     if ($orders->isEmpty()) {
    //         return response()->json(['status' => 'success', 'data' => []], 200);
    //     }

    //     $response = [];

    //     foreach ($orders as $order) {
    //         $orderData = [
    //             'user_name' => $order->user_name,
    //             'email' => $order->email,
    //             'order_id' => $order->order_id,
    //             'total_amount' => $order->total_amount,
    //             'total_qty' => $order->total_qty,
    //             'order_date' => $order->created_at->format('d M y  h:i A'),
    //             'products' => []
    //         ];

    //         foreach ($order->products as $product) {
    //             $orderData['products'][] = [
    //                 'product_name' => $product->product_name,
    //                 'price' => $product->price,
    //                 'quantity' => $product->quantity,
    //             ];
    //         }

    //         $response[] = $orderData;
    //     }

    //     return response()->json(['status' => 'success', 'data' => $response]);
    // }


    // public function getOrderById(GetOrderRequest $request)
    // {
    //     $order = Order::where('id', $request->id)->with('products')->first();

    //     // $order = Order::where('order_id', $request->order_id)
    //     // ->with('products')
    //     // ->get();

    //     if (!$order) {
    //         return response()->json(['status' => 'success', 'data' => []], 200);
    //     }

    //     $response = [
    //         'user_name' => $order->user_name,
    //         'email' => $order->email,
    //         'order_id' => $order->order_id,
    //         'total_amount' => $order->total_amount,
    //         'total_qty' => $order->total_qty,
    //         'order_date' => $order->created_at->format('d M y  h:i A'),
    //         'products' => []
    //     ];

    //     foreach ($order->products as $product) {
    //         $response['products'][] = [
    //             'product_name' => $product->product_name,
    //             'price' => $product->price,
    //             'quantity' => $product->quantity,
    //         ];
    //     }

    //     return response()->json(['status' => 'success', 'data' => $response]);
    // }

    // public function getOrdersByEmail(GetOrdersByEmailRequest $request)
    // {
    //     // $orders = Order::where('email', $request->email)->with('products')->get();
    //     $orders = Order::where('email', $request->email)
    //         ->where('is_active', 1)
    //         ->where('is_deleted', 0)
    //         ->with('products')
    //         ->get();


    //     if ($orders->isEmpty()) {
    //         return response()->json(['status' => 'success', 'data' => []], 200);
    //     }

    //     $response = [
    //         'user_name' => $orders->first()->user_name,
    //         'email' => $request->email,
    //         'orders' => []
    //     ];

    //     foreach ($orders as $order) {
    //         $orderData = [
    //             'order_id' => $order->order_id,
    //             'total_amount' => $order->total_amount,
    //             'total_qty' => $order->total_qty,
    //             'order_date' => $order->created_at->format('d M y  h:i A'),
    //             'products' => []
    //         ];

    //         foreach ($order->products as $product) {
    //             $orderData['products'][] = [
    //                 'product_name' => $product->product_name,
    //                 'price' => $product->price,
    //                 'quantity' => $product->quantity,
    //             ];
    //         }

    //         $response['orders'][] = $orderData;
    //     }

    //     return response()->json(['status' => 'success', 'data' => $response]);
    // }

    // public function getOrders(GetOrderRequest $request)
    // {
    //     $query = Order::with('products');

    //     // Apply filters based on request payload
    //     if (!empty($request->created_date)) {
    //         $query->whereDate('created_at', '=', date('Y-m-d', strtotime($request->created_date)));
    //     } else {
    //         // Default: Fetch orders from the last 2 days
    //         $query->where('created_at', '>=', now()->subDays(2)->startOfDay());
    //     }

    //     if (!empty($request->order_id)) {
    //         $query->where('order_id', $request->order_id);
    //     }

    //     if (!empty($request->user_name)) {
    //         $query->where('user_name', 'LIKE', "%{$request->user_name}%");
    //     }

    //     $orders = $query->get();

    //     if ($orders->isEmpty()) {
    //         return response()->json(['status' => 'error', 'message' => 'No order found'], 404);
    //     }

    //     $response = [];

    //     foreach ($orders as $order) {
    //         $orderData = [
    //             'user_name' => $order->user_name,
    //             'email' => $order->email,
    //             'order_id' => $order->order_id,
    //             'total_amount' => $order->total_amount,
    //             'total_qty' => $order->total_qty,
    //             'order_date' => $order->created_at->format('d M y  h:i A'),
    //             'products' => []
    //         ];

    //         foreach ($order->products as $product) {
    //             $orderData['products'][] = [
    //                 'product_name' => $product->product_name,
    //                 'price' => $product->price,
    //                 'quantity' => $product->quantity,
    //             ];
    //         }

    //         $response[] = $orderData;
    //     }

    //     return response()->json($response);
    // }




    // public function getOrders(GetOrderRequest $request)
    // {
    //     $query = Order::with('products');

    //     // Check if order_id or user_name is provided
    //     $applyDateFilter = true;

    //     if (!empty($request->order_id)) {
    //         $query->where('order_id', $request->order_id);
    //         $applyDateFilter = false; // Don't apply date filter
    //     }

    //     if (!empty($request->user_name)) {
    //         $query->where('user_name', 'LIKE', "%{$request->user_name}%");
    //         $applyDateFilter = false; // Don't apply date filter
    //     }

    //     // Apply date filter ONLY IF order_id AND user_name are NOT provided
    //     if ($applyDateFilter) {
    //         if (!empty($request->created_date)) {
    //             $query->whereDate('created_at', '=', date('Y-m-d', strtotime($request->created_date)));
    //         } else {
    //             $query->where('created_at', '>=', now()->subDays(2)->startOfDay());
    //         }
    //     }

    //     $orders = $query->get();

    //     if ($orders->isEmpty()) {
    //         return response()->json(['status' => 'error', 'message' => 'No order found'], 404);
    //     }

    //     $response = [];

    //     foreach ($orders as $order) {
    //         $orderData = [
    //             'user_name' => $order->user_name,
    //             'email' => $order->email,
    //             'order_id' => $order->order_id,
    //             'total_amount' => $order->total_amount,
    //             'total_qty' => $order->total_qty,
    //             'order_date' => $order->created_at->format('d M y  h:i A'),
    //             'products' => []
    //         ];

    //         foreach ($order->products as $product) {
    //             $orderData['products'][] = [
    //                 'product_name' => $product->product_name,
    //                 'price' => $product->price,
    //                 'quantity' => $product->quantity,
    //             ];
    //         }

    //         $response[] = $orderData;
    //     }

    //     return response()->json($response);
    // }




    // public function getOrders(GetOrderRequest $request)
    // {
    //     $query = Order::with('products')
    //         ->where('is_active', 1)
    //         ->where('is_deleted', 0); // Ensure only active & non-deleted orders are fetched

    //     $hasOrderId = isset($request->order_id) && !empty($request->order_id);
    //     $hasUserName = isset($request->user_name) && !empty($request->user_name);
    //     $hasDate = isset($request->created_date) && !empty($request->created_date);

    //     // Apply order_id filter if provided
    //     if ($hasOrderId) {
    //         $query->where('order_id', $request->order_id);
    //     }

    //     // Apply user_name filter if provided
    //     if ($hasUserName) {
    //         $query->where('user_name', 'LIKE', "%{$request->user_name}%");
    //     }

    //     // Apply date filter if given
    //     if ($hasDate) {
    //         $dates = explode(' ', $request->created_date);

    //         if (count($dates) === 6) {
    //             $startDate = date('Y-m-d 00:00:00', strtotime("{$dates[2]}-{$dates[1]}-{$dates[0]}"));
    //             $endDate = date('Y-m-d 23:59:59', strtotime("{$dates[5]}-{$dates[4]}-{$dates[3]}"));

    //             $query->whereBetween('created_at', [$startDate, $endDate]);
    //         } else {
    //             return response()->json(['status' => 'error', 'message' => 'Invalid date format'], 400);
    //         }
    //     }

    //     $orders = $query->get();

    //     if ($orders->isEmpty()) {
    //         return response()->json(['status' => 'success', 'data' => []], 200);
    //     }

    //     // $response = [];

    //     // foreach ($orders as $order) {
    //     //     $orderData = [
    //     //         'user_name' => $order->user_name,
    //     //         'email' => $order->email,
    //     //         'order_id' => $order->order_id,
    //     //         'total_amount' => $order->total_amount,
    //     //         'total_qty' => $order->total_qty,
    //     //         'order_date' => $order->created_at->format('d M y  h:i A'),
    //     //         'products' => []
    //     //     ];

    //     //     foreach ($order->products as $product) {
    //     //         $orderData['products'][] = [
    //     //             'product_name' => $product->product_name,
    //     //             'price' => $product->price,
    //     //             'quantity' => $product->quantity,
    //     //         ];
    //     //     }

    //     //     $response[] = $orderData;
    //     // }

    //     // return response()->json($response);

    //     $response = [];

    //     foreach ($orders as $key => $order) {
    //         $response[$key] = [
    //             'user_name' => $order->user_name,
    //             'email' => $order->email,
    //             'order_id' => $order->order_id,
    //             'total_amount' => $order->total_amount,
    //             'total_qty' => $order->total_qty,
    //             'order_date' => $order->created_at->format('d M y  h:i A'),
    //             'products' => []
    //         ];

    //         foreach ($order['products'] as $productKey => $product) {
    //             $response[$key]['products'][$productKey] = [
    //                 'product_name' => $product->product_name,
    //                 'price' => $product->price,
    //                 'quantity' => $product->quantity,
    //             ];
    //         }
    //     }

    //     return response()->json(['status' => 'success', 'data' => $response]);
    // }



    public function createOrder(array $orderData)
    {
        // $orderData['created_date'] = Carbon::now();  //now() 
        // $orderData['updated_date'] = Carbon::now();  //now()
        return Order::create($orderData);
    }

    public function checkExistingOrder(string $orderId, string $user_id)
    {
        return Order::where('order_no', $orderId)
            ->where('user_id', $user_id)
            ->get();
    }

    public function getOrders(array $filters, int $userId)
    {
        $query = Order::with('products')->where('user_id', $userId);

        if (!empty($filters['order_no'])) {
            $query->where('order_no', $filters['order_no']); //$query->whereIn('order_id', explode(' ', string: $filters['order_id']))
        }

        if (!empty($filters['customer_name'])) {
            $query->where('customer_name', 'LIKE', "%{$filters['customer_name']}%");
        }

        if (!empty($filters['created_date'])) {
            $dates = explode(' ', $filters['created_date']);

            if (count($dates) === 2) {
                // $startDate = date('d-m-Y 00:00:00', strtotime("{$dates[0]}-{$dates[1]}-{$dates[2]}"));
                // $endDate = date('d-m-Y 23:59:59', strtotime("{$dates[3]}-{$dates[4]}-{$dates[5]}"));

                $startDate = date('Y-m-d 00:00:00', strtotime($dates[0]));
                $endDate = date('Y-m-d 23:59:59', strtotime($dates[1]));

                $query->whereBetween('created_date', [$startDate, $endDate]);
            }
        }

        // $query->where('status', 1)->where('is_deleted', 0);

        return $query->get();
    }

    public function getActiveOrders(int $userId): collection
    {
        return Order::with('products')
            ->where([
                ['status', '=', 1],
                ['is_deleted', '=', 0],
                ['user_id', '=', $userId]
            ])
            ->get();   //->where('status', 1)->where('is_deleted', 0)
    }





    public function getOrdersByEmail(string $email, int $userId)
    {
        return Order::where('user_id', $userId)->where('email', $email)
            ->with('products')
            ->get();
    }






    public function findOrder(array $orderData, int $userId)
    {
        return Order::where('order_no', $orderData['order_no'])
            ->where('user_id', $userId)
            ->get();
    }

    public function updateOrder(Order $order, array $orderData)
    {
        $order->update([
            'customer_name' => $orderData['customer_name'],
            'email' => $orderData['email'],
            'contact_no' => $orderData['contact_no'],
            'address1' => $orderData['address1'],
            'address2' => $orderData['address2'],
            'pin_code' => $orderData['pin_code'],
            'city' => $orderData['city'],
            'state' => $orderData['state'],
            'country' => $orderData['country'],
            'updated_orders' => 1,
            'updated_date' => now()
        ]);
    }

    public function deleteOrder(Order $order)
    {
        $order['is_deleted'] = 1;
        $order['status'] = 0;
        $order['updated_date'] = now();
        $order->save(); // Ensure save() is used explicitly
    }

    public function updateCancellation(Order $order)
    {
        $order->update([
            'status' => 2,
            'is_deleted' => 2,
            'updated_date' => now()
        ]);
    }
}
