<?php

namespace App\Services;


use App\Http\Requests\CreateOrderRequest;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\RateChartRepositoryInterface;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Storage;

use Illuminate\Database\Eloquent\Casts\Json;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;



class OrderService
{
    protected $OrderRepositoryInterface;
    protected $productRepositoryInterface;
    protected $rateChartRepositoryInterface;
    protected $userRepositoryInterface;

    public function __construct(
        OrderRepositoryInterface $OrderRepositoryInterface,
        ProductRepositoryInterface $productRepositoryInterface,
        RateChartRepositoryInterface $rateChartRepositoryInterface,
        UserRepositoryInterface $userRepositoryInterface
    ) {
        $this->OrderRepositoryInterface = $OrderRepositoryInterface;
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->rateChartRepositoryInterface = $rateChartRepositoryInterface;
        $this->userRepositoryInterface = $userRepositoryInterface;
    }


    // public function createOrder(array $orderData, array $products)
    // {
    //     try {
    //         DB::beginTransaction();

    //         $userId = Auth::id();

    //         // Check wallet balance
    //         $walletBalance = $this->userRepositoryInterface->getWalletBalance($userId);
    //         if ($walletBalance <= 0) {
    //             return [
    //                 'status' => 'error',
    //                 'message' => $walletBalance == 0 ? 'Recharge your Wallet Balance.' : 'Negative Wallet Balance. Kindly Recharge your Wallet.',
    //                 'status_code' => 400
    //             ];
    //         }

    //         // Check if order already exists
    //         $existingOrder = $this->OrderRepositoryInterface->checkExistingOrder($orderData['order_no'], $userId);
    //         if ($existingOrder->isNotEmpty()) {
    //             return [
    //                 'status' => 'error',
    //                 'message' => 'You cannot use this Order ID again for the same user.',
    //                 'status_code' => 400
    //             ];
    //         }

    //         // Calculate total amount and quantity for products
    //         $totalAmount = 0;
    //         $totalQty = 0;
    //         foreach ($products as $product) {
    //             $totalAmount += $product['price'] * $product['quantity'];
    //             $totalQty += $product['quantity'];
    //         }

    //         // Calculate volumetric weight
    //         $volumetricWeight = ($orderData['length'] * $orderData['width'] * $orderData['height']) / 5000;

    //         // Get the weight to use for charging (actual or volumetric, whichever is higher)
    //         $chargingWeight = max($orderData['weight'], $volumetricWeight);


    //         // Custom rounding logic
    //         $decimalPart = $chargingWeight - floor($chargingWeight);
    //         // dd($decimalPart);

    //         if ($decimalPart > 0 && $decimalPart <= 0.5) {
    //             $chargingWeight = floor($chargingWeight) + 0.5;
    //         } elseif ($decimalPart > 0.5) {
    //             $chargingWeight = ceil($chargingWeight);
    //         }
    //         // dd($chargingWeight);


    //         $rates = $this->rateChartRepositoryInterface->getRateForWeight($chargingWeight, $userId);
    //         // dd($rates->toArray());

    //         if ($rates->isEmpty()) {
    //             return [
    //                 'status' => 'error',
    //                 'message' => 'No applicable rate found for this weight.',
    //                 'status_code' => 400
    //             ];
    //         }

    //         // Separate user-specific and default rates
    //         $userRates = $rates->where('user_id', $userId);
    //         $defaultRates = $rates->where('user_id', 0);
    //         // dd($userRates->toArray());
    //         // dd($defaultRates->toArray());

    //         // Check if user-specific rates exist
    //         if ($userRates->isNotEmpty()) {
    //             // Get the rate matching the weight
    //             $rate = $userRates->where('weight', '==', $chargingWeight)->first();
    //         }
    //         // dd($rate->toArray());
    //         // dd($rate);

    //         // If no user-specific rate is found, check default rates
    //         if (empty($rate) && $defaultRates->isNotEmpty()) {
    //             $rate = $defaultRates->where('weight', '==', $chargingWeight)->first();
    //         }
    //         // dd($rate->toArray());
    //         // dd($rate);

    //         // if (empty($rate)) {
    //         //     return [
    //         //         'status' => 'error',
    //         //         'message' => 'No applicable rate found for this weight.',
    //         //         'status_code' => 400
    //         //     ];
    //         // }
    //         // dd($rate->toArray());

    //         if (empty($rate) && $defaultRates->isNotEmpty()) {
    //             $rate = $defaultRates->sortByDesc('weight')->first();
    //         }
    //         // dd($rate->toArray());
    //         // dd($rate);

    //         $chargedAmount = $rate->rate_amount;
    //         // dd($chargedAmount);

    //         if ($walletBalance < $chargedAmount) {
    //             return [
    //                 'status' => 'error',
    //                 'message' => 'Insufficient Wallet Balance. Kindly Recharge your Wallet.',
    //                 'status_code' => 400
    //             ];
    //         }

    //         // Deduct amount from user's wallet
    //         $newBalance = $walletBalance - $chargedAmount;
    //         $this->userRepositoryInterface->updateWalletBalance($userId, $newBalance);

    //         // Add calculated fields to order data
    //         $orderData['total_amount'] = $totalAmount;
    //         $orderData['total_qty'] = $totalQty;
    //         $orderData['charged_amount'] = $chargedAmount;
    //         $orderData['charged_weight'] = $chargingWeight;
    //         $orderData['created_date'] = Carbon::now();
    //         $orderData['updated_date'] = Carbon::now();

    //         // Store order in repository
    //         $order = $this->OrderRepositoryInterface->createOrder($orderData);

    //         // Store product data
    //         foreach ($products as $product) {
    //             $productData = [
    //                 'order_table_id' => $order->id,
    //                 'order_no' => $orderData['order_no'],
    //                 'product_name' => $product['product_name'],
    //                 'price' => $product['price'],
    //                 'quantity' => $product['quantity']
    //             ];
    //             $this->productRepositoryInterface->storeProducts($productData);
    //         }

    //         DB::commit();

    //         return [
    //             'status' => 'success',
    //             'message' => 'Order created successfully',
    //             'status_code' => 200
    //         ];
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return [
    //             'status' => 'error',
    //             'message' => 'Failed to create order: ' . $e->getMessage(),
    //             'status_code' => 500
    //         ];
    //     }
    // }





    // public function getOrders(array $filters): array
    // {
    //     $orders = $this->getOrdersData($filters);
    //     $formattedOrders = $this->formatOrders($orders);

    //     return [
    //         'status' => 'success',
    //         'data' => $formattedOrders,
    //         'status_code' => 200
    //     ];
    // }

    // public function getOrdersData(array $filters)
    // {
    //     $userId = Auth::id();

    //     $orders = $this->OrderRepositoryInterface->getOrders($filters, $userId);
    //     // dd(empty($orders));
    //     // dd($orders->toArray());
    //     // dd(($orders)->isEmpty());

    //     if (($orders)->isEmpty()) {
    //         return [];
    //     }

    //     return $orders;
    // }

    // public function getActiveOrders(): array
    // {
    //     $userId = Auth::id();

    //     $orders = $this->OrderRepositoryInterface->getActiveOrders($userId);

    //     if ($orders->isEmpty()) {
    //         return [
    //             'status' => 'success',
    //             'data' => [],
    //             'status_code' => 200
    //         ];
    //     }

    //     $formattedOrders = $this->formatOrders($orders);

    //     // $response = [];

    //     // foreach ($orders as $order) {
    //     //     $orderData = [
    //     //         'order_id' => $order->order_no,
    //     //         'customer_name' => $order->customer_name,
    //     //         'email' => $order->email,
    //     //         'charged_amount' => $order->charged_amount . " Rs",
    //     //         'weight' => $order->weight . " Kg",
    //     //         'length' => $order->length . " cm",
    //     //         'width' => $order->width . " cm",
    //     //         'height' => $order->height . " cm",
    //     //         'contact_no' => $order->contact_no,
    //     //         'address1' => $order->address1,
    //     //         'address2' => $order->address2,
    //     //         'pin_code' => $order->pin_code,
    //     //         'city' => $order->city,
    //     //         'state' => $order->state,
    //     //         'country' => $order->country,
    //     //         'total_amount' => $order->total_amount,
    //     //         'total_qty' => $order->total_qty,
    //     //         'order_date' => date('d M y  h:i A', strtotime($order->created_date)),
    //     //         'products' => []
    //     //     ];

    //     //     foreach ($order['products'] as $product) {
    //     //         $orderData['products'][] = [
    //     //             'product_name' => $product->product_name,
    //     //             'price' => $product->price,
    //     //             'quantity' => $product->quantity,
    //     //             // 'weight' => $product->weight . " Kg",
    //     //             // 'length' => $product->length . " cm",
    //     //             // 'width' => $product->width . " cm",
    //     //             // 'height' => $product->height . " cm",
    //     //         ];
    //     //     }

    //     //     $response[] = $orderData;
    //     // }

    //     return [
    //         'status' => 'success',
    //         'data' => $formattedOrders,
    //         'status_code' => 200
    //     ];
    // }

    // private function formatOrders($orders): array
    // {
    //     $formattedOrders = [];

    //     foreach ($orders as $order) {
    //         $formattedOrders[] = $this->formatOrderData($order);
    //     }

    //     return $formattedOrders;
    // }

    // private function formatOrderData($order): array
    // {
    //     return [
    //         'order_no' => $order->order_no,
    //         'customer_name' => $order->customer_name,
    //         'email' => $order->email,
    //         'charged_amount' => $order->charged_amount . " Rs",
    //         'weight' => $order->weight . " Kg",
    //         'length' => $order->length . " Cm",
    //         'width' => $order->width . " Cm",
    //         'height' => $order->height . " Cm",
    //         'contact_no' => $order->contact_no,
    //         'address1' => $order->address1,
    //         'address2' => $order->address2,
    //         'pin_code' => $order->pin_code,
    //         'city' => $order->city,
    //         'state' => $order->state,
    //         'country' => $order->country,
    //         'total_amount' => $order->total_amount . " Rs",
    //         'total_qty' => $order->total_qty . " Qty",
    //         'order_date' => date('d M y  h:i A', strtotime($order->created_date)),
    //         'products' => $this->formatProducts($order->products)
    //     ];
    // }

    // private function formatProducts($products): array
    // {
    //     $formattedProducts = [];

    //     foreach ($products as $product) {
    //         $formattedProducts[] = [
    //             'product_name' => $product->product_name,
    //             'price' => $product->price . " Rs",
    //             'quantity' => $product->quantity . " Qty",
    //         ];
    //     }

    //     return $formattedProducts;
    // }


    // public function exportOrders(array $filters): array
    // {
    //     $orders = $this->getOrdersData($filters);
    //     $formatData = $this->formatExportOrders($orders);

    //     if(empty($formatData)) {
    //         return [
    //             'status' => 'success',
    //             'message' => 'No data available to export.',
    //             'file_path' => null
    //         ];
    //     }
    //     // dd($formatData);

    //     $filePath = storage_path('app/public/filtered_Orders.csv');

    //     $exportData = [];
    //     foreach ($formatData as $order) {
    //         $exportData[] = [
    //             'Order No'        => $order['order_no'],
    //             'Customer Name'   => $order['customer_name'],
    //             'Email'           => $order['email'],
    //             'Contact No'      => $order['contact_no'],
    //             'Address Line 1'  => $order['address1'],
    //             'Address Line 2'  => $order['address2'],
    //             'Pin Code'        => $order['pin_code'],
    //             'City'            => $order['city'],
    //             'State'           => $order['state'],
    //             'Country'         => $order['country'],
    //             'Charged Amount'  => $order['charged_amount'],
    //             'Total Amount'    => $order['total_amount'],
    //             'Total Quantity'  => $order['total_qty'],
    //             'Weight (KG)'     => $order['weight'],
    //             'Length (CM)'     => $order['length'],
    //             'Width (CM)'      => $order['width'],
    //             'Height (CM)'     => $order['height'],
    //             'Order Date'      => $order['order_date'],
    //             'Product Name'    => $order['product_name'],
    //             'Price (Rs)'      => $order['price'],
    //             'Quantity'        => $order['quantity']
    //         ];
    //     }

    //     (new FastExcel(collect($exportData)))->export($filePath);

    //     return [
    //         'status'    => 'success',
    //         'message'   => 'Filtered Customer data exported successfully!',
    //         'file_path' => asset('storage/filtered_Orders.csv')
    //     ];
    // }

    // // private function formatExportOrders($orders): array
    // // {
    // //     $formattedOrders = [];

    // //     foreach ($orders as $order) {
    // //         $firstRow = [
    // //             'order_no'        => $order->order_no,
    // //             'customer_name'   => $order->customer_name,
    // //             'email'           => $order->email,
    // //             'contact_no'      => $order->contact_no,
    // //             'address1'        => $order->address1,
    // //             'address2'        => $order->address2,
    // //             'pin_code'        => $order->pin_code,
    // //             'city'            => $order->city,
    // //             'state'           => $order->state,
    // //             'country'         => $order->country,
    // //             'charged_amount'  => $order->charged_amount . " Rs",
    // //             'total_amount'    => $order->total_amount . " Rs",
    // //             'total_qty'       => $order->total_qty . " Qty",
    // //             'weight'          => $order->weight . " Kg",
    // //             'length'          => $order->length . " Cm",
    // //             'width'           => $order->width . " Cm",
    // //             'height'          => $order->height . " Cm",
    // //             'order_date'      => date('d M y h:i A', strtotime($order->created_date)),
    // //             'product_name'    => '',
    // //             'price'           => '',
    // //             'quantity'        => '',
    // //         ];

    // //         $formattedOrders[] = $firstRow;

    // //         foreach ($order['products'] as $product) {
    // //             $formattedOrders[] = [
    // //                 'order_no'        => '',
    // //                 'customer_name'   => '',
    // //                 'email'           => '',
    // //                 'contact_no'      => '',
    // //                 'address1'        => '',
    // //                 'address2'        => '',
    // //                 'pin_code'        => '',
    // //                 'city'            => '',
    // //                 'state'           => '',
    // //                 'country'         => '',
    // //                 'charged_amount'  => '',
    // //                 'total_amount'    => '',
    // //                 'total_qty'       => '',
    // //                 'weight'          => '',
    // //                 'length'          => '',
    // //                 'width'           => '',
    // //                 'height'          => '',
    // //                 'order_date'      => '',
    // //                 'product_name'    => $product->product_name,
    // //                 'price'           => $product->price . " Rs",
    // //                 'quantity'        => $product->quantity . " Qty"
    // //             ];
    // //         }
    // //     }

    // //     return $formattedOrders;
    // // }

    // private function formatExportOrders($orders): array
    // {
    //     $formattedOrders = [];

    //     foreach ($orders as $order) {
    //         $isFirstRow = true;

    //         foreach ($order->products as $product) {
    //             $formattedOrders[] = [
    //                 'order_no'        => $isFirstRow ? $order->order_no : '',
    //                 'customer_name'   => $isFirstRow ? $order->customer_name : '',
    //                 'email'           => $isFirstRow ? $order->email : '',
    //                 'contact_no'      => $isFirstRow ? $order->contact_no : '',
    //                 'address1'        => $isFirstRow ? $order->address1 : '',
    //                 'address2'        => $isFirstRow ? $order->address2 : '',
    //                 'pin_code'        => $isFirstRow ? $order->pin_code : '',
    //                 'city'            => $isFirstRow ? $order->city : '',
    //                 'state'           => $isFirstRow ? $order->state : '',
    //                 'country'         => $isFirstRow ? $order->country : '',
    //                 'charged_amount'  => $isFirstRow ? $order->charged_amount . " Rs" : '',
    //                 'total_amount'    => $isFirstRow ? $order->total_amount . " Rs" : '',
    //                 'total_qty'       => $isFirstRow ? $order->total_qty . " Qty" : '',
    //                 'weight'          => $isFirstRow ? $order->weight . " Kg" : '',
    //                 'length'          => $isFirstRow ? $order->length . " Cm" : '',
    //                 'width'           => $isFirstRow ? $order->width . " Cm" : '',
    //                 'height'          => $isFirstRow ? $order->height . " Cm" : '',
    //                 'order_date'      => $isFirstRow ? date('d M y h:i A', strtotime($order->created_date)) : '',
    //                 'product_name'    => $product->product_name,
    //                 'price'           => $product->price . " Rs",
    //                 'quantity'        => $product->quantity . " Qty"
    //             ];

    //             $isFirstRow = false; // After the first product, set this to false so other rows are blanked out
    //         }
    //     }

    //     return $formattedOrders;
    // }


    // public function getOrdersByEmail(string $email): array
    // {
    //     $userId = Auth::id();

    //     $orders = $this->OrderRepositoryInterface->getOrdersByEmail($email, $userId);

    //     if ($orders->isEmpty()) {
    //         return [
    //             'status' => 'success',
    //             'data' => [],
    //             'status_code' => 200
    //         ];
    //     }

    //     $response = [
    //         'customer_name' => $orders[0]->customer_name,
    //         'email' => $email,
    //         'charged_amount' => $orders[0]->charged_amount . " Rs",
    //         'weight' => $orders[0]->weight . " Kg",

    //         'length' => $orders[0]->length . " cm",
    //         'width' => $orders[0]->width . " cm",
    //         'height' => $orders[0]->height . " cm",

    //         'contact_no' => $orders[0]->contact_no,
    //         'address1' => $orders[0]->address1,
    //         'address2' => $orders[0]->address2,
    //         'pin_code' => $orders[0]->pin_code,
    //         'city' => $orders[0]->city,
    //         'state' => $orders[0]->state,
    //         'country' => $orders[0]->country,
    //         'orders' => []
    //     ];

    //     foreach ($orders as $order) {
    //         $orderData = [
    //             'order_no' => $order->order_no,
    //             'total_amount' => $order->total_amount,
    //             'total_qty' => $order->total_qty,
    //             'order_date' => date('d M y  h:i A', strtotime($order->created_at)),
    //             'products' => []
    //         ];

    //         foreach ($order->products as $product) {
    //             $orderData['products'][] = [
    //                 'product_name' => $product->product_name,
    //                 'price' => $product->price,
    //                 'quantity' => $product->quantity,
    //                 // 'weight' => $product->weight . " Kg",
    //                 // 'length' => $product->length . " cm",
    //                 // 'width' => $product->width . " cm",
    //                 // 'height' => $product->height . " cm",
    //             ];
    //         }

    //         $response['orders'][] = $orderData;
    //     }

    //     return [
    //         'status' => 'success',
    //         'data' => $response,
    //         'status_code' => 200
    //     ];
    // }



    // public function updateOrder(array $orderData): array
    // {
    //     $userId = Auth::id();

    //     $order = $this->OrderRepositoryInterface->findOrder($orderData, $userId);

    //     if ($order->isEmpty()) {
    //         return [
    //             'status' => 'error',
    //             'message' => 'Order not found',
    //             'status_code' => 404
    //         ];
    //     }

    //     $this->OrderRepositoryInterface->updateOrder($order, $orderData);

    //     return [
    //         'status' => 'success',
    //         'message' => 'Order updated successfully',
    //         'status_code' => 200
    //     ];
    // }

    // public function deleteOrder(array $orderData): array
    // {
    //     $userId = Auth::id();

    //     $order = $this->OrderRepositoryInterface->findOrder($orderData, $userId);

    //     if ($order->isEmpty()) {
    //         return [
    //             'status' => 'error',
    //             'message' => 'Order not found',
    //             'status_code' => 404
    //         ];
    //     }

    //     $this->OrderRepositoryInterface->deleteOrder($order);

    //     return [
    //         'status' => 'success',
    //         'message' => 'Order deleted successfully',
    //         'status_code' => 200
    //     ];
    // }
}
