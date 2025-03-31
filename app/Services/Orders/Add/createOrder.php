<?php

namespace App\Services\Orders\Add;

use Illuminate\Http\JsonResponse;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\RateChartRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;




class createOrder
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


    public function createOrder(array $orderDetails, array $productList)
    {
        try {
            DB::beginTransaction();

            $currentUserId = Auth::id();

            // Check wallet balance
            $currentWalletBalance = $this->getWalletBalance($currentUserId);
            if ($currentWalletBalance <= 0) {
                return $this->walletBalanceErrorResponse($currentWalletBalance);
            }

            // Check if order already exists
            $checkOrderAlreadyExists = $this->isOrderExists($orderDetails['order_no'], $currentUserId);
            if ($checkOrderAlreadyExists) {
                return $this->orderExistsErrorResponse();
            }

            // Calculate total amount, quantity According to the order details
            list($totalOrderAmount, $totalProductQuantity) = $this->calculateTotalAmountAndQuantity($productList);
            // Calculate charging weight According to the order details
            $calculatedChargingWeight = $this->calculateChargingWeight($orderDetails);

            // Get applicable rate for the order
            $applicableChargeAmount = $this->getApplicableRate($calculatedChargingWeight, $currentUserId);
            if ($applicableChargeAmount === null) {
                return [
                    'status' => 'error',
                    'message' => 'No applicable rate found for this weight.',
                    'status_code' => 400
                ];
            }

            // Check and validate wallet balance
            if ($currentWalletBalance < $applicableChargeAmount) {
                return [
                    'status' => 'error',
                    'message' => 'Insufficient Wallet Balance. Kindly Recharge your Wallet.',
                    'status_code' => 400
                ];
            }

            // Deduct wallet balance
            $this->deductWalletBalance($currentUserId, $applicableChargeAmount, $currentWalletBalance);

            // Prepare order data
            $preparedOrderData = $this->prepareOrderData($orderDetails, $totalOrderAmount, $totalProductQuantity, $applicableChargeAmount, $calculatedChargingWeight);

            // Store order and products
            $this->storeOrder($preparedOrderData, $productList);

            DB::commit();

            return [
                'status' => 'success',
                'message' => 'Order created successfully',
                'status_code' => 200
            ];
        } catch (\Exception $exception) {
            DB::rollBack();
            return [
                'status' => 'error',
                'message' => 'Failed to create order: ' . $exception->getMessage(),
                'status_code' => 500
            ];
        }
    }

    private function getWalletBalance($userId)
    {
        return $this->userRepositoryInterface->getWalletBalance($userId);
    }

    private function walletBalanceErrorResponse($walletBalance): array
    {
        return [
            'status' => 'error',
            'message' => $walletBalance == 0
                ? 'Recharge your Wallet Balance.'
                : 'Negative Wallet Balance. Kindly Recharge your Wallet.',
            'status_code' => 400
        ];
    }

    private function isOrderExists($orderNumber, $userId): bool
    {
        $existingOrder = $this->OrderRepositoryInterface->checkExistingOrder($orderNumber, $userId);
        return $existingOrder->isNotEmpty();
    }

    private function orderExistsErrorResponse(): array
    {
        return [
            'status' => 'error',
            'message' => 'You cannot use this Order ID again for the same user.',
            'status_code' => 400
        ];
    }

    private function calculateTotalAmountAndQuantity(array $productList): array
    {
        $totalAmount = 0;
        $totalQuantity = 0;

        foreach ($productList as $product) {
            $totalAmount += $product['price'] * $product['quantity'];
            $totalQuantity += $product['quantity'];
        }

        return [$totalAmount, $totalQuantity];
    }

    private function calculateChargingWeight(array $orderDetails): float
    {
        $volumetricWeight = ($orderDetails['length'] * $orderDetails['width'] * $orderDetails['height']) / 5000;
        $chargingWeight = max($orderDetails['weight'], $volumetricWeight);

        // Custom rounding logic
        $decimalPart = $chargingWeight - floor($chargingWeight);

        if ($decimalPart > 0 && $decimalPart <= 0.5) {
            $chargingWeight = floor($chargingWeight) + 0.5;
        } elseif ($decimalPart > 0.5) {
            $chargingWeight = ceil($chargingWeight);
        }

        return $chargingWeight;
    }

    private function getApplicableRate(float $chargingWeight, int $userId): ?float
    {
        $rates = $this->rateChartRepositoryInterface->getRateForWeight($chargingWeight, $userId);

        if ($rates->isEmpty()) {
            return null;
        }

        // Separate user-specific and default rates
        $userRates = $rates->where('user_id', $userId);
        $defaultRates = $rates->where('user_id', 0);

        // Try to find the exact match for user-specific rates
        $rate = $userRates->where('weight', '==', $chargingWeight)->first();

        // If no exact match, check default rates
        if (empty($rate)) {
            $rate = $defaultRates->where('weight', '==', $chargingWeight)->first();
        }

        // If still no match, get the highest available default rate
        if (empty($rate) && $defaultRates->isNotEmpty()) {
            $rate = $defaultRates->sortByDesc('weight')->first();
        }

        return $rate ? $rate->rate_amount : null;
    }

    private function deductWalletBalance(int $userId, float $chargedAmount, float $walletBalance): void
    {
        // dd($userId, $chargedAmount, $walletBalance);
        $newBalance = $walletBalance - $chargedAmount;
        $this->userRepositoryInterface->updateWalletBalance($userId, $newBalance);
    }

    private function prepareOrderData(array $orderDetails, float $totalAmount, int $totalQuantity, float $chargedAmount, float $chargingWeight): array
    {
        $orderDetails['total_amount'] = $totalAmount;
        $orderDetails['total_qty'] = $totalQuantity;
        $orderDetails['charged_amount'] = $chargedAmount;
        $orderDetails['charged_weight'] = $chargingWeight;
        $orderDetails['created_date'] = Carbon::now();
        $orderDetails['updated_date'] = Carbon::now();

        return $orderDetails;
    }

    private function storeOrder(array $orderDetails, array $productList)
    {
        $order = $this->OrderRepositoryInterface->createOrder($orderDetails);

        foreach ($productList as $product) {
            $this->storeProduct($order->id, $orderDetails['order_no'], $product);
        }

        return $order;
    }

    private function storeProduct(int $orderId, string $orderNo, array $product): void
    {
        $productData = [
            'order_table_id' => $orderId,
            'order_no' => $orderNo,
            'product_name' => $product['product_name'],
            'price' => $product['price'],
            'quantity' => $product['quantity']
        ];

        $this->productRepositoryInterface->storeProducts($productData);
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


}
