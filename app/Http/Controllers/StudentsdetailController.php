<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Storage;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;


class StudentsdetailController extends Controller
{
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

    public function exportStudents()
    {
        /* Download excel file in user Request */
        // $students = Student::all();
        // return (new FastExcel($students))->download('students.xlsx');


        $students = Student::all();

        // Define the file path where the Excel file will be stored
        $filePath = storage_path('app/public/students.csv'); // .csv

        // Save the Excel file to the server (storage/app/public/students.xlsx)
        (new FastExcel($students))->export($filePath);

        return response()->json([
            'message' => 'Student data exported successfully!',
            'file_path' => asset('storage/students.xlsx')
        ]);
    }

}
