<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;


class StudentsdetailController extends Controller
{

    // Normal Ways to Get from Params
    public function getData(Request $request): JsonResponse
    {
        // Retrieve query parameters
        // $data = [
        //     'id' => $request->query('id'),
        //     'name' => $request->query('name'),
        //     'age' => $request->query('age'),
        //     'course' => $request->query('course')
        // ];

        $data = $request->all();

        // Send the data back as JSON response
        return response()->json([
            'message' => 'Data received successfully',
            'data' => $data
        ], 200);
    }




    //using Repository and Interface and Binding 

    // public function getData(Request $request): JsonResponse
    // {
    //     $data = $this->studentRepository->getData($request);

    //     return response()->json([
    //         'message' => 'Data received successfully',
    //         'data'  => $data
    //     ], 200);
    // }


    // A HEAD request is similar to a GET request, but it does not return a response body—only the headers.
    public function headData(Request $request): JsonResponse
    {
        return response()->json([], 200)
        ->header('X-API-Version', '1.0')         // API version
        ->header('X-Request-Timestamp', now())   // Timestamp of the request
        ->header('X-Server-Name', config('app.name')) // Name of the server/application
        ->header('X-Request-ID', uniqid())       // Unique request ID for tracking/debugging
        ->header('X-RateLimit-Limit', '1000')    // API rate limit
        ->header('X-RateLimit-Remaining', '999') // Remaining allowed requests
        ->header('X-Response-Time', microtime(true) - LARAVEL_START . 's'); // Server response time
    
    }


}
