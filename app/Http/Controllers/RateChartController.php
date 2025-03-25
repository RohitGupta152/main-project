<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateRateRequest;
use App\Http\Requests\FindByIdRateRequest;
use App\Http\Requests\getRatesRequest;
use App\Http\Resources\RateResource;
use Illuminate\Http\Response;


use App\Services\RateChartService;
use Illuminate\Http\Request;

class RateChartController extends Controller
{
    protected $rateChartService;

    public function __construct(RateChartService $rateChartService)
    {
        $this->rateChartService = $rateChartService;
    }

    // public function createRate(CreateRateRequest $request): JsonResponse
    // {
    //     $rateData['user_id'] = $request['user_id'];
    //     $rateData['weight'] = $request->input('weight');
    //     $rateData['rate_amount'] = $request->input('rate_amount');

    //     $response = $this->rateChartService->createRate($rateData);

    //     return response()->json([
    //         'status' => $response['status'],
    //         'message' => $response['message'],
    //     ], $response['status_code']);
    // }

    // public function getAllRates(): JsonResponse
    // {
    //     $response = $this->rateChartService->getAllRates();

    //     return response()->json([
    //         'status' => $response['status'],
    //         'data' => $response['data']
    //     ], $response['status_code']);
    // }


    public function createRate(CreateRateRequest $request): JsonResponse
    {
        $userId = $request['user_id'];
        $rateDataArray = $request->input('data'); // Get the array of rates

        // dd($userId);
        // dd($rateDataArray);

        if (empty($rateDataArray)) {
            return response()->json([
                'status' => "error",
                'message' => 'No rate data provided.',
            ], 400);
        }

        $response = $this->rateChartService->createRates($userId, $rateDataArray);

        return response()->json([
            'status' => $response['status'],
            'message' => $response['message'],
        ], $response['status_code']);
    }


    public function getRates(getRatesRequest $request): JsonResponse
    {
        // $userId = (int)($request['user_id'] ?? 0);

        $filters['user_id'] = $request['user_id']; // Required user_id filter
        $filters['weight'] = $request['weight'];
        // $filters['rate_amount'] = $request['rate_amount'];
        $filters['created_date'] = $request['created_date'];

        $response = $this->rateChartService->getRates($filters);

        return response()->json([
            'status' => $response['status'],
            'data' => $response['data']
        ], $response['status_code']);

        // return response()->json([
        //     'status' => $response['status'],
        //     'data' => RateResource::collection($response['data']),
        // ], $response['status_code']);        
    }

    // public function getRateById(Request $request): JsonResponse
    // {
    //     // $userId = Auth::id(); // Get logged-in user ID
    //     $userId['user_id'] = $request->input('user_id');

    //     $response = $this->rateChartService->getRateByUserId($userId);

    //     return response()->json([
    //         'status' => $response['status'],
    //         'data' => $response['data']
    //     ], $response['status_code']);
    // }


    public function updateRate(Request $request): JsonResponse
    {
        $rateId = $request->input('id'); // Using rate_id, not user_id
        $rateData['user_id'] = $request->input('user_id');
        $rateData['weight'] = $request->input('weight');
        $rateData['rate_amount'] = $request->input('rate_amount');
        // dd(rateData);

        $response = $this->rateChartService->updateRate($rateId, $rateData);

        return response()->json([
            'status' => $response['status'],
            'message' => $response['message'],
        ], $response['status_code']);
    }

    public function deleteRate(FindByIdRateRequest $request): JsonResponse
    {
        $rateId = $request->input('id');

        $response = $this->rateChartService->deleteRate($rateId);

        return response()->json([
            'status' => $response['status'],
            'message' => $response['message'],
        ], $response['status_code']);
    }
}
