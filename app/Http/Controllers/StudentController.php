<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentRequest;
use App\Http\Requests\updateStudentRequest;
use App\Http\Requests\showStudentByPayloadRequest;
use App\Http\Requests\updateStudentByPayloadRequest;
use App\Http\Requests\deleteStudentByPayloadRequest;
use App\Http\Requests\GetStudentRequest;
// use App\Repositories\StudentRepository;
use App\Repositories\Interfaces\StudentRepositoryInterface;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\OrderService;


use Illuminate\Http\Response;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Services\StudentService;



class StudentController extends Controller
{
    protected $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }


    public function getStudents(GetStudentRequest $request): JsonResponse    // GET All (Using Payload)
    {
        $getData = [
            'user_id' => $request['user_id'],
            'name'    => $request['name'],
            'email'   => $request['email'],
            'age'     => $request['age'],
            'course'  => $request['course']
        ];
        // dd($getData);
        $getData = $this->studentService->getStudent($getData);

        return response()->json([
            'status' => $getData['status'],
            'data' => $getData['data']
        ], $getData['status_code']);
    }

    public function exportStudent(GetStudentRequest $request)
    {
        $getData = [
            'user_id' => $request['user_id'],
            'name'    => $request['name'],
            'email'   => $request['email'],
            'age'     => $request['age'],
            'course'  => $request['course']
        ];

        $data = $this->studentService->exportStudent($getData);

        return response()->json([
            'message'   => $data['message'],
            'file_path' => $data['file_path']
        ]);
    }

    public function getStudentsById(showStudentByPayloadRequest $request): JsonResponse
    {
        $validated = [
            'id' => $request['id']
        ];

        $students = $this->studentService->getStudentById($validated);

        return response()->json([
            'status' => $students['status'],
            // 'message' => $students['message'],
            'data' => $students['data']
        ], $students['status_code']);
    }

    public function createStudent(StudentRequest $request): JsonResponse
    {
        $data = [
            'name' => $request['name'],
            'email' => $request['email'],
            'age' => $request['age'],
            'course' => $request['course']
        ];

        $student = $this->studentService->createStudent($data);

        return response()->json([
            'status' => $student['status'],
            'message' => $student['message']
        ], $student['status_code']);
    }

    public function updateStudent(UpdateStudentByPayloadRequest $request): JsonResponse
    {
        // $validated = $request->validated();
        $validated = [
            'id' => $request['id'],
            'name' => $request['name'],
            'email' => $request['email'],
            'age' => $request['age'],
            'course' => $request['course']
        ];

        $student = $this->studentService->updateStudent($validated);

        return response()->json([
            'status' => $student['status'],
            'message' => $student['message']
        ], $student['status_code']);
    }

    public function deleteStudent(deleteStudentByPayloadRequest $request): JsonResponse
    {
        $validated = $request['id'];

        $student = $this->studentService->deleteStudent($validated);

        return response()->json([
            'status' => $student['status'],
            'message' => $student['message']
        ], $student['status_code']);
    }





    public function getStudentByParams($id): JsonResponse // GET (Using Params)
    {

        $student = $this->studentService->getStudentByParams($id);

        return response()->json([
            'status' => $student['status'],
            'data' => $student['data']
        ], $student['status_code']);
    }

    public function updateStudentByParams(updateStudentRequest $request, $id): JsonResponse   // PUT (Using Params)
    {

        $data = [
            'name' => $request['name'],
            'email' => $request['email'],
            'age' => $request['age'],
            'course' => $request['course']
        ];

        $student = $this->studentService->updateStudentByParams($id, $data);

        return response()->json([
            'status' => $student['status'],
            'message' => $student['message']
        ], $student['status_code']);
    }

    public function deleteStudentByParams($id): JsonResponse // DELETE (Using Params)
    {
        $deleted = $this->studentService->deleteStudentByParams($id);

        return response()->json([
            'status' => $deleted['status'],
            'message' => $deleted['message']
        ], $deleted['status_code']);
    }
}
