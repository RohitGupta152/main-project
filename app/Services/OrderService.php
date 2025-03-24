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


    // public function createOrder(CreateOrderRequest $request): array //JsonResponse
    // {

    //     // ✅ Check if order already exists
    //     if ($this->OrderRepositoryInterface->checkExistingOrder($request->order_id, $request->email)) {

    //         // return response()->json([
    //         //     'status' => 'error',
    //         //     'message' => 'You cannot use this Order ID again for the same email.'
    //         // ], 400);

    //         return [
    //             'status' => 'error',
    //             'message' => 'You cannot use this Order ID again for the same email.',
    //             'status_code' => 400
    //         ];

    //     }

    //     // ✅ Calculate total amount and quantity
    //     $totalAmount = 0;
    //     $totalQty = 0;

    //     foreach ($request['products'] as $product) {
    //         $totalAmount += $product['price'] * $product['quantity'];
    //         $totalQty += $product['quantity'];
    //     }

    //     // ✅ Store order in repository and get order object
    //     $order = $this->OrderRepositoryInterface->createOrder([
    //         'order_id' => $request->order_id,
    //         'user_name' => $request->user_name,
    //         'email' => $request->email,
    //         'total_amount' => $totalAmount,
    //         'total_qty' => $totalQty
    //     ]);

    //     // ✅ Process and store product data
    //     $products = [];
    //     foreach ($request['products'] as $product) {
    //         $products[] = [
    //             'order_table_id' => $order->id,
    //             'order_id' => $order->order_id,
    //             'product_name' => $product['product_name'],
    //             'price' => $product['price'],
    //             'quantity' => $product['quantity']
    //         ];
    //     }

    //     // ✅ Save all products at once using bulk insert
    //     $this->OrderRepositoryInterface->storeProducts($products);

    //     // return response()->json([
    //     //     'status' => 'success',
    //     //     'message' => 'Order created successfully'
    //     // ], 201);

    //     return [
    //         'status' => 'success',
    //         'message' => 'Order created successfully',
    //         'status_code' => 201
    //     ];

    // }



    // public function createOrder(array $orderData, array $products)
    // {
    // // Check if order already exists
    // $existingOrder = $this->OrderRepositoryInterface->checkExistingOrder($orderData['order_no'], $orderData['user_id']);

    // if ($existingOrder) 
    // {
    //     return [
    //         'status' => 'error',
    //         'message' => 'You cannot use this Order ID again for the same email or Users.',
    //         'status_code' => 400
    //     ];
    // }

    // // Calculate total amount and quantity
    // $totalAmount = 0;
    // $totalQty = 0;

    // foreach ($products as $product) 
    // {
    //     $totalAmount += $product['price'] * $product['quantity'];
    //     $totalQty += $product['quantity'];
    // }

    // // Add calculated fields to order data
    // $orderData['total_amount'] = $totalAmount;
    // $orderData['total_qty'] = $totalQty;

    // // Store order in repository
    // $order = $this->OrderRepositoryInterface->createOrder($orderData);

    // // Prepare product data for bulk insert
    // $productData = [];
    // foreach ($products as $product) 
    // {
    //     $productData = [
    //         'order_table_id' => $order->id,
    //         // 'order_table_id' => 1,
    //         'order_no' => $orderData['order_no'],
    //         'product_name' => $product['product_name'],
    //         'price' => $product['price'],
    //         'quantity' => $product['quantity']
    //     ];

    //     $this->productRepositoryInterface->storeProducts($productData);
    // }

    // // dd($productData);

    // // Save all products using bulk insert
    // // $this->OrderRepositoryInterface->storeProducts($productData);

    // return [
    //     'status' => 'success',
    //     'message' => 'Order created successfully',
    //     'status_code' => 200
    // ];




    //     $userId = $orderData['user_id'];

    //     // Check wallet balance
    //     $walletAmount = $this->UserRepositoryInterface->checkWalletAmount($userId);
    //     if ($walletAmount <= 0) {
    //         return [
    //             'status' => 'error',
    //             'message' => 'Recharge your wallet of ITL Account.',
    //             'status_code' => 400
    //         ];
    //     }

    //     $existingOrder = $this->OrderRepositoryInterface->checkExistingOrder($orderData['order_no'], $orderData['user_id']);

    //     if ($existingOrder) 
    //     {
    //         return [
    //             'status' => 'error',
    //             'message' => 'You cannot use this Order ID again for the same email or Users.',
    //             'status_code' => 400
    //         ];
    //     }

    //     // Calculate total amount & quantity
    //     $totalAmount = 0;
    //     $totalQty = 0;

    //     foreach ($products as $product) {
    //         $totalAmount += $product['price'] * $product['quantity'];
    //         $totalQty += $product['quantity'];
    //     }

    //     // Check if wallet has enough balance
    //     if ($totalAmount > $walletAmount) {
    //         return [
    //             'status' => 'error',
    //             'message' => 'Insufficient Wallet balance please Add your Amount',
    //             'status_code' => 400
    //         ];
    //     }

    //     // Deduct wallet amount
    //     $this->UserRepositoryInterface->deductWalletAmount($userId, $totalAmount);

    //     // Store order
    //     $orderData['total_amount'] = $totalAmount;
    //     $orderData['total_qty'] = $totalQty;
    //     $orderData['created_date'] = Carbon::now();
    //     $orderData['updated_date'] = Carbon::now();

    //     $order = $this->OrderRepositoryInterface->createOrder($orderData);

    //     // Store products
    //     foreach ($products as $product) {

    //         $this->productRepositoryInterface->storeProducts([
    //             'order_table_id' => $order->id,
    //             'order_no' => $orderData['order_no'],
    //             'product_name' => $product['product_name'],
    //             'price' => $product['price'],
    //             'quantity' => $product['quantity']
    //         ]);
    //     }

    //     return [
    //         'status' => 'success',
    //         'message' => 'Order created successfully',
    //         'status_code' => 200
    //     ];


    // }









    // public function createOrder(array $orderData, array $products)
    // {
    //     try {
    //         DB::beginTransaction();

    //         $userId = Auth::id();
    //         // $orderData['user_id'] = $userId;

    //         $walletAmount = $this->userRepositoryInterface->getWalletBalance($userId);
    //         if ($walletAmount == 0) {
    //             return [
    //                 'status' => 'error',
    //                 'message' => 'Recharge your Wallet Balance.',
    //                 'status_code' => 400
    //             ];
    //         } elseif ($walletAmount < 0) {
    //             return [
    //                 'status' => 'error',
    //                 'message' => 'Negative Wallet Balance. Kindly Recharge your Wallet.',
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

    //         // // Get user details & ensure wallet amount is retrieved properly
    //         // $user = $this->userRepositoryInterface->findById($userId);
    //         // if (!$user) {
    //         //     return [
    //         //         'status' => 'error',
    //         //         'message' => 'User not found.',
    //         //         'status_code' => 404
    //         //     ];
    //         // }

    //         // Calculate total amount and quantity for products
    //         $totalAmount = 0;

    //         $totalQty = 0;
    //         foreach ($products as $product) {
    //             $totalAmount += $product['price'] * $product['quantity'];
    //             $totalQty += $product['quantity'];
    //         }

    //         // Ensure default values for missing dimensions
    //         $orderData['length'] = $orderData['length'] ?? 0;
    //         $orderData['width'] = $orderData['width'] ?? 0;
    //         $orderData['height'] = $orderData['height'] ?? 0;
    //         $orderData['weight'] = $orderData['weight'] ?? 0;

    //         // Calculate volumetric weight
    //         $volumetricWeight = ($orderData['length'] * $orderData['width'] * $orderData['height']) / 5000;

    //         // Get the weight to use for charging (actual or volumetric, whichever is higher)
    //         $chargingWeight = max($orderData['weight'], $volumetricWeight);

    //         // Get rate from RateChart based on the weight

    //         /* $rate = $this->rateChartRepositoryInterface->getUserRateForWeight($chargingWeight, $userId);
    //         if (!$rate) {
    //             $rate = $this->rateChartRepositoryInterface->getDefaultRateForWeight($chargingWeight);
    //         } */



    //         // $rate = $this->rateChartRepositoryInterface->getRateForWeight($chargingWeight, $userId);

    //         // if (!$rate) {
    //         //     return [
    //         //         'status' => 'error',
    //         //         'message' => 'No applicable rate found for this weight.',
    //         //         'status_code' => 400
    //         //     ];
    //         // }

    //         // // Get the first rate from the collection
    //         // // $rate = $rate->first();
    //         // $chargedAmount = $rate->rate_amount;








    //         $rates = $this->rateChartRepositoryInterface->getRateForWeight($chargingWeight, $userId);

    //         dd($rates->toArray());

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

    //         // Find the best matching rate for the user
    //         if ($userRates->isNotEmpty()) {
    //             $selectedRates = $userRates;
    //         } else {
    //             $selectedRates = $defaultRates; // Fall back to default rates if no user rate exists
    //         }

    //         // Find the rate that is closest to the user's weight requirement
    //         $rate = $selectedRates->sortBy('weight')->firstWhere('weight', '>=', $chargingWeight);

    //         if (!$rate) {
    //             return [
    //                 'status' => 'error',
    //                 'message' => 'No applicable rate found for this weight.',
    //                 'status_code' => 400
    //             ];
    //         }

    //         // Charge based on selected rate
    //         $chargedAmount = $rate->rate_amount;












    //         // // Calculate charged amount
    //         // $chargedAmount = $rate->rate_amount;

    //         // Fetch the latest wallet balance
    //         $walletBalance = $this->userRepositoryInterface->getWalletBalance($userId);

    //         // Check if user has enough balance
    //         if ($walletBalance < $chargedAmount) {
    //             return [
    //                 'status' => 'error',
    //                 'message' => 'Insufficient Wallet Balance. Kindly Recharge your Wallet.',
    //                 'status_code' => 400
    //             ];
    //         }

    //         // Deduct amount from user's wallet BEFORE committing the order
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


    public function createOrder(array $orderData, array $products)
    {
        try {
            DB::beginTransaction();

            $userId = Auth::id();

            // Check wallet balance
            $walletBalance = $this->userRepositoryInterface->getWalletBalance($userId);
            if ($walletBalance <= 0) {
                return [
                    'status' => 'error',
                    'message' => $walletBalance == 0 ? 'Recharge your Wallet Balance.' : 'Negative Wallet Balance. Kindly Recharge your Wallet.',
                    'status_code' => 400
                ];
            }

            // Check if order already exists
            $existingOrder = $this->OrderRepositoryInterface->checkExistingOrder($orderData['order_no'], $userId);
            if ($existingOrder->isNotEmpty()) {
                return [
                    'status' => 'error',
                    'message' => 'You cannot use this Order ID again for the same user.',
                    'status_code' => 400
                ];
            }

            // Calculate total amount and quantity for products
            $totalAmount = 0;
            $totalQty = 0;
            foreach ($products as $product) {
                $totalAmount += $product['price'] * $product['quantity'];
                $totalQty += $product['quantity'];
            }

            // Calculate volumetric weight
            $volumetricWeight = ($orderData['length'] * $orderData['width'] * $orderData['height']) / 5000;

            // Get the weight to use for charging (actual or volumetric, whichever is higher)
            $chargingWeight = max($orderData['weight'], $volumetricWeight);


            // Custom rounding logic
            $decimalPart = $chargingWeight - floor($chargingWeight);
            // dd($decimalPart);

            if ($decimalPart > 0 && $decimalPart <= 0.5) {
                $chargingWeight = floor($chargingWeight) + 0.5;
            } elseif ($decimalPart > 0.5) {
                $chargingWeight = ceil($chargingWeight);
            }
            // dd($chargingWeight);


            $rates = $this->rateChartRepositoryInterface->getRateForWeight($chargingWeight, $userId);
            // dd($rates->toArray());

            if ($rates->isEmpty()) {
                return [
                    'status' => 'error',
                    'message' => 'No applicable rate found for this weight.',
                    'status_code' => 400
                ];
            }

            // Separate user-specific and default rates
            $userRates = $rates->where('user_id', $userId);
            $defaultRates = $rates->where('user_id', 0);
            // dd($userRates->toArray());
            // dd($defaultRates->toArray());

            // Check if user-specific rates exist
            if ($userRates->isNotEmpty()) {
                // Get the rate matching the weight
                $rate = $userRates->where('weight', '==', $chargingWeight)->first();
            }
            // dd($rate->toArray());
            // dd($rate);

            // If no user-specific rate is found, check default rates
            if (empty($rate) && $defaultRates->isNotEmpty()) {
                $rate = $defaultRates->where('weight', '==', $chargingWeight)->first();
            }
            // dd($rate->toArray());
            // dd($rate);

            // if (empty($rate)) {
            //     return [
            //         'status' => 'error',
            //         'message' => 'No applicable rate found for this weight.',
            //         'status_code' => 400
            //     ];
            // }
            // dd($rate->toArray());

            if (empty($rate) && $defaultRates->isNotEmpty()){
                $rate = $defaultRates->sortByDesc('weight')->first();
            }
            // dd($rate->toArray());
            // dd($rate);

            $chargedAmount = $rate->rate_amount;
            // dd($chargedAmount);

            if ($walletBalance < $chargedAmount) {
                return [
                    'status' => 'error',
                    'message' => 'Insufficient Wallet Balance. Kindly Recharge your Wallet.',
                    'status_code' => 400
                ];
            }

            // Deduct amount from user's wallet
            $newBalance = $walletBalance - $chargedAmount;
            $this->userRepositoryInterface->updateWalletBalance($userId, $newBalance);

            // Add calculated fields to order data
            $orderData['total_amount'] = $totalAmount;
            $orderData['total_qty'] = $totalQty;
            $orderData['charged_amount'] = $chargedAmount;
            $orderData['charged_weight'] = $chargingWeight;
            $orderData['created_date'] = Carbon::now();
            $orderData['updated_date'] = Carbon::now();

            // Store order in repository
            $order = $this->OrderRepositoryInterface->createOrder($orderData);

            // Store product data
            foreach ($products as $product) {
                $productData = [
                    'order_table_id' => $order->id,
                    'order_no' => $orderData['order_no'],
                    'product_name' => $product['product_name'],
                    'price' => $product['price'],
                    'quantity' => $product['quantity']
                ];
                $this->productRepositoryInterface->storeProducts($productData);
            }

            DB::commit();

            return [
                'status' => 'success',
                'message' => 'Order created successfully',
                'status_code' => 200
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => 'error',
                'message' => 'Failed to create order: ' . $e->getMessage(),
                'status_code' => 500
            ];
        }
    }


    // public function createOrder(array $orderData, array $products)
    // {
    //     try {
    //         DB::beginTransaction();

    //         $userId = Auth::id();
    //         $orderData['user_id'] = $userId;

    //         $walletAmount = $this->userRepositoryInterface->getWalletBalance($userId);
    //         if ($walletAmount <= 0) {
    //             return [
    //                 'status' => 'error',
    //                 'message' => 'Recharge your Wallet Balance.',
    //                 'status_code' => 400
    //             ];
    //         }

    //         // Check if order already exists
    //         $existingOrder = $this->OrderRepositoryInterface->checkExistingOrder($orderData['order_no'], $userId);
    //         if ($existingOrder) {
    //             return [
    //                 'status' => 'error',
    //                 'message' => 'You cannot use this Order ID again for the same user.',
    //                 'status_code' => 400
    //             ];
    //         }

    //         // Get user details
    //         // $user = $this->userRepositoryInterface->findById($userId);
    //         // if (!$user) {
    //         //     return [
    //         //         'status' => 'error',
    //         //         'message' => 'User not found.',
    //         //         'status_code' => 404
    //         //     ];
    //         // }

    //         // Calculate total amount, quantity, and total weight
    //         $totalAmount = 0;
    //         $totalQty = 0;
    //         $totalWeight = 0;
    //         $volumetricWeight = 0;

    //         foreach ($products as $product) {
    //             $totalAmount += $product['price'] * $product['quantity'];
    //             $totalQty += $product['quantity'];
    //             $totalWeight += $product['weight'] * $product['quantity'];

    //             // Calculate volumetric weight per product

    //             $productVolumetricWeight = ($product['length'] * $product['width'] * $product['height']) / 5000;

    //             // $productVolumetricWeight = ($product['length'] * $product['height']) / 5000;
    //             $volumetricWeight += max($productVolumetricWeight, $product['weight']) * $product['quantity'];
    //         }

    //         // Get the final weight to use for charging (max of total actual or total volumetric weight)
    //         $chargingWeight = max($totalWeight, $volumetricWeight);

    //         // Get rate from rate chart
    //         $rate = $this->rateChartRepositoryInterface->getRateForUser($chargingWeight);
    //         if (!$rate) {
    //             return [
    //                 'status' => 'error',
    //                 'message' => 'No applicable rate found for this weight.',
    //                 'status_code' => 400
    //             ];
    //         }

    //         // Calculate charged amount
    //         $chargedAmount = $chargingWeight * $rate->rate_amount; //$rate['rate_per_kg']

    //         // $chargedAmount = $rate->rate_amount;

    //         // $walletBalance = $this->userRepositoryInterface->getWalletBalance($userId);
    //         // if ($walletBalance < $chargedAmount)

    //         // Check if user has enough balance
    //         if ($walletAmount < $chargedAmount) {
    //             return [
    //                 'status' => 'error',
    //                 'message' => 'Insufficient wallet balance. Kindly recharge your wallet.',
    //                 'status_code' => 400
    //             ];
    //         }

    //         // Add calculated fields to order data
    //         $orderData['total_amount'] = $totalAmount;
    //         $orderData['total_qty'] = $totalQty;
    //         $orderData['total_weight'] = $totalWeight;
    //         $orderData['charged_amount'] = $chargedAmount;
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
    //                 'quantity' => $product['quantity'],
    //                 'weight' => $product['weight'],
    //                 'length' => $product['length'],
    //                 'width' => $product['width'],
    //                 'height' => $product['height']
    //             ];
    //             $this->productRepositoryInterface->storeProducts($productData);
    //         }

    //         // Deduct amount from user's wallet
    //         $newBalance = $walletAmount - $chargedAmount;
    //         // $newBalance = $user->wallet_amount - $chargedAmount;

    //         $this->userRepositoryInterface->updateWalletBalance($userId, $newBalance);

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

    // private function calculateVolumetricWeight(float $length, float $height): float
    // {
    //     return ($length * $height) / 5000;
    // }


    public function getAllOrders(): array
    {
        $userId = Auth::id();

        $orders = $this->OrderRepositoryInterface->getActiveOrders($userId);

        if ($orders->isEmpty()) {
            return [
                'status' => 'success',
                'data' => [],
                'status_code' => 200
            ];
        }

        $response = [];

        foreach ($orders as $order) {
            $orderData = [
                'order_id' => $order->order_no,
                'customer_name' => $order->customer_name,
                'email' => $order->email,
                'charged_amount' => $order->charged_amount . " Rs",
                'weight' => $order->weight . " Kg",
                'length' => $order->length . " cm",
                'width' => $order->width . " cm",
                'height' => $order->height . " cm",
                'contact_no' => $order->contact_no,
                'address1' => $order->address1,
                'address2' => $order->address2,
                'pin_code' => $order->pin_code,
                'city' => $order->city,
                'state' => $order->state,
                'country' => $order->country,
                'total_amount' => $order->total_amount,
                'total_qty' => $order->total_qty,
                'order_date' => date('d M y  h:i A', strtotime($order->created_date)),
                'products' => []
            ];

            foreach ($order['products'] as $product) {
                $orderData['products'][] = [
                    'product_name' => $product->product_name,
                    'price' => $product->price,
                    'quantity' => $product->quantity,
                    // 'weight' => $product->weight . " Kg",
                    // 'length' => $product->length . " cm",
                    // 'width' => $product->width . " cm",
                    // 'height' => $product->height . " cm",
                ];
            }

            $response[] = $orderData;
        }

        return [
            'status' => 'success',
            'data' => $response,
            'status_code' => 200
        ];
    }

    public function getOrders(array $filters): array
    {
        $userId = Auth::id();

        $orders = $this->OrderRepositoryInterface->getOrders($filters, $userId);

        if ($orders->isEmpty()) {
            return [
                'status' => 'success',
                'data' => [],
                'status_code' => 200
            ];
        }

        $response = [];

        foreach ($orders as $order) {
            $orderData = [
                'order_no' => $order->order_no,
                'customer_name' => $order->customer_name,
                'email' => $order->email,
                'charged_amount' => $order->charged_amount . " Rs",
                'weight' => $order->weight . " Kg",
                'length' => $order->length . " cm",
                'width' => $order->width . " cm",
                'height' => $order->height . " cm",
                'contact_no' => $order->contact_no,
                'address1' => $order->address1,
                'address2' => $order->address2,
                'pin_code' => $order->pin_code,
                'city' => $order->city,
                'state' => $order->state,
                'country' => $order->country,
                'total_amount' => $order->total_amount,
                'total_qty' => $order->total_qty,
                // 'order_date' => $order->created_at->format('d M y  h:i A'),
                'order_date' => date('d M y  h:i A', strtotime($order->created_date)),
                'products' => []
            ];

            foreach ($order['products'] as $product) {
                $orderData['products'][] = [
                    'product_name' => $product->product_name,
                    'price' => $product->price,
                    'quantity' => $product->quantity,
                    // 'weight' => $product->weight . " Kg",
                    // 'length' => $product->length . " cm",
                    // 'width' => $product->width . " cm",
                    // 'height' => $product->height . " cm",
                ];
            }

            $response[] = $orderData;
        }

        return [
            'status' => 'success',
            'data' => $response,
            'status_code' => 200
        ];
    }


    // public function getOrdersByEmail(array $email): array
    // {
    //     $orders = $this->OrderRepositoryInterface->getOrdersByEmail($email);

    //     if (empty($orders)) {
    //         return [
    //             'status' => 'success',
    //             'data' => [],
    //             'status_code' => 200
    //         ];
    //     }

    //     $response = [
    //         'user_name' => $orders[0]['user_name'],
    //         'email' => $email,
    //         'orders' => []
    //     ];

    //     foreach ($orders as $order) {
    //         $orderData = [
    //             'order_id' => $order['order_id'],
    //             'total_amount' => $order['total_amount'],
    //             'total_qty' => $order['total_qty'],
    //             'order_date' => date('d M y  h:i A', strtotime($order['created_at'])),
    //             'products' => []
    //         ];

    //         foreach ($order['products'] as $product) {
    //             $orderData['products'][] = [
    //                 'product_name' => $product['product_name'],
    //                 'price' => $product['price'],
    //                 'quantity' => $product['quantity']
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



    public function getOrdersByEmail(string $email): array
    {
        $userId = Auth::id();

        $orders = $this->OrderRepositoryInterface->getOrdersByEmail($email, $userId);

        if ($orders->isEmpty()) {
            return [
                'status' => 'success',
                'data' => [],
                'status_code' => 200
            ];
        }

        $response = [
            'customer_name' => $orders[0]->customer_name,
            'email' => $email,
            'charged_amount' => $orders[0]->charged_amount . " Rs",
            'weight' => $orders[0]->weight . " Kg",

            'length' => $orders[0]->length . " cm",
            'width' => $orders[0]->width . " cm",
            'height' => $orders[0]->height . " cm",

            'contact_no' => $orders[0]->contact_no,
            'address1' => $orders[0]->address1,
            'address2' => $orders[0]->address2,
            'pin_code' => $orders[0]->pin_code,
            'city' => $orders[0]->city,
            'state' => $orders[0]->state,
            'country' => $orders[0]->country,
            'orders' => []
        ];

        foreach ($orders as $order) {
            $orderData = [
                'order_no' => $order->order_no,
                'total_amount' => $order->total_amount,
                'total_qty' => $order->total_qty,
                'order_date' => date('d M y  h:i A', strtotime($order->created_at)),
                'products' => []
            ];

            foreach ($order->products as $product) {
                $orderData['products'][] = [
                    'product_name' => $product->product_name,
                    'price' => $product->price,
                    'quantity' => $product->quantity,
                    // 'weight' => $product->weight . " Kg",
                    // 'length' => $product->length . " cm",
                    // 'width' => $product->width . " cm",
                    // 'height' => $product->height . " cm",

                ];
            }

            $response['orders'][] = $orderData;
        }

        return [
            'status' => 'success',
            'data' => $response,
            'status_code' => 200
        ];
    }














    public function updateOrder(array $orderData): array
    {
        $userId = Auth::id();

        $order = $this->OrderRepositoryInterface->findOrder($orderData, $userId);

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


    public function deleteOrder(array $orderData): array
    {
        $userId = Auth::id();

        $order = $this->OrderRepositoryInterface->findOrder($orderData, $userId);

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
