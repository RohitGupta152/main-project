<?php

namespace App\Services\Orders\Get;

use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Storage;
use App\Services\Orders\Get\OrderGetService;

class OrderExportService
{
    protected $OrderRepositoryInterface;

    public function __construct(OrderRepositoryInterface $OrderRepositoryInterface)
    {
        $this->OrderRepositoryInterface = $OrderRepositoryInterface;
    }


    public function exportOrders(array $filters): array
    {
        $OrderGetService = app(OrderGetService::class);

        $orders = $OrderGetService->getOrdersData($filters);
        // dd($orders);
        if (empty($orders)) {
            return [
                'status' => 'success',
                'message' => 'No data available to export.',
                'file_path' => null
            ];
        }
        $formatData = $this->formatExportOrders($orders);
        // dd($formatData);

        $filePath = storage_path('app/public/filtered_Orders.csv');

        $exportData = [];
        foreach ($formatData as $order) {
            $exportData[] = [
                'Order No'        => $order['order_no'],
                'Customer Name'   => $order['customer_name'],
                'Email'           => $order['email'],
                'Contact No'      => $order['contact_no'],
                'Address Line 1'  => $order['address1'],
                'Address Line 2'  => $order['address2'],
                'Pin Code'        => $order['pin_code'],
                'City'            => $order['city'],
                'State'           => $order['state'],
                'Country'         => $order['country'],
                'Charged Amount'  => $order['charged_amount'],
                'Total Amount'    => $order['total_amount'],
                'Total Quantity'  => $order['total_qty'],
                'Weight (KG)'     => $order['weight'],
                'Length (CM)'     => $order['length'],
                'Width (CM)'      => $order['width'],
                'Height (CM)'     => $order['height'],
                'Order Status'    => $order['status'],
                'Current Status'  => $order['cancelled'],
                'Order Date'      => $order['order_date'],
                'Product Name'    => $order['product_name'],
                'Price (Rs)'      => $order['price'],
                'Quantity'        => $order['quantity']
            ];
        }

        (new FastExcel(collect($exportData)))->export($filePath);

        return [
            'status'    => 'success',
            'message'   => 'Filtered Customer data exported successfully!',
            'file_path' => asset('storage/filtered_Orders.csv')
        ];
    }

    private function formatExportOrders($orders): array
    {
        $formattedOrders = [];

        foreach ($orders as $order) {
            $isFirstRow = true;

            foreach ($order->products as $product) {
                $formattedOrders[] = [
                    'order_no'        => $isFirstRow ? $order->order_no : '',
                    'customer_name'   => $isFirstRow ? $order->customer_name : '',
                    'email'           => $isFirstRow ? $order->email : '',
                    'contact_no'      => $isFirstRow ? $order->contact_no : '',
                    'address1'        => $isFirstRow ? $order->address1 : '',
                    'address2'        => $isFirstRow ? $order->address2 : '',
                    'pin_code'        => $isFirstRow ? $order->pin_code : '',
                    'city'            => $isFirstRow ? $order->city : '',
                    'state'           => $isFirstRow ? $order->state : '',
                    'country'         => $isFirstRow ? $order->country : '',
                    'charged_amount'  => $isFirstRow ? $order->charged_amount . " Rs" : '',
                    'total_amount'    => $isFirstRow ? $order->total_amount . " Rs" : '',
                    'total_qty'       => $isFirstRow ? $order->total_qty . " Qty" : '',
                    'weight'          => $isFirstRow ? $order->weight . " Kg" : '',
                    'length'          => $isFirstRow ? $order->length . " Cm" : '',
                    'width'           => $isFirstRow ? $order->width . " Cm" : '',
                    'height'          => $isFirstRow ? $order->height . " Cm" : '',
                    // Corrected status handling
                    'status'          => $isFirstRow ?
                        ($order->status == 0 ? 'Order Delivered' : ($order->status == 1 ? 'Order Processing' : 'Order Inactive')) : '',

                    // Corrected is_deleted (cancelled) handling
                    'cancelled'       => $isFirstRow ?
                        ($order->is_deleted == 0 ? 'Order Active' : ($order->is_deleted == 1 ? 'Order Completed' : 'Order Cancelled')) : '',
                        
                    'order_date'      => $isFirstRow ? date('d M y h:i A', strtotime($order->created_date)) : '',
                    'product_name'    => $product->product_name,
                    'price'           => $product->price . " Rs",
                    'quantity'        => $product->quantity . " Qty"
                ];

                $isFirstRow = false; // After the first product, set this to false so other rows are blanked out
            }
        }

        return $formattedOrders;
    }
}
