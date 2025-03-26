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

use Illuminate\Http\Response;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Storage;
use App\Models\Student;
use Illuminate\Database\Eloquent\Casts\Json;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class StudentController extends Controller
{
    protected $studentRepository;

    public function __construct(StudentRepositoryInterface $studentRepository)
    {
        $this->studentRepository = $studentRepository;
    }

    public function getStudents(Request $request)    // GET All (Using Payload)
    {
        $getData = [
            'user_id' => $request['user_id'],
            'name'    => $request['name'],
            'email'   => $request['email'],
            'age'     => $request['age'],
            'course'  => $request['course']
        ];
        // dd($getData);

        $getData = $this->getStudentData($getData);
        $formatStudent = $this->formatStudents($getData);


        return response()->json([
            'status' => 'success',
            'data'   => $formatStudent
        ], 200);

        // return $students;
    }

    protected function getStudentData(array $getData): array
    {
        $student = $this->studentRepository->getAll($getData);
        // dd($student);

        if (empty($student)) {
            throw new NotFoundHttpException('No students found.');
        }

        return $student;
    }

    protected function  formatStudents(array $getData)
    {
        $formattedStudents = [];
        foreach ($getData as $key => $values) {
            $formattedStudents[$key]['name'] = $values['name'];
            $formattedStudents[$key]['email'] = $values['email'];
            $formattedStudents[$key]['age'] = $values['age'] . " years";
            $formattedStudents[$key]['course'] = $values['course'];
            $formattedStudents[$key]['created_date'] = date('d M y  h:i A', strtotime($values['created_at']));
            $formattedStudents[$key]['updated_date'] = date('d M y  h:i A', strtotime($values['updated_at']));
        }

        return $formattedStudents;
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

        $data = $this->getStudentData($getData);
        // dd($data);
        $format = $this->formatStudents($data);


        $filePath = storage_path('app/public/filtered_students.csv');

        (new FastExcel($format))->export($filePath);

        return response()->json([
            'message'   => 'Filtered student data exported successfully!',
            'file_path' => asset('storage/filtered_students.csv')
        ]);
    }

    public function showStudentByParams($id): JsonResponse // GET (Using Params)
    {

        $student = $this->studentRepository->findByParam($id);

        $formattedStudent = [
            'id'           => $student->id,
            'name'         => $student->name,
            'email'        => $student->email,
            'age'          => $student->age . " years",
            'course'       => $student->course,
            'created_date' => date('d M y  h:i A', strtotime($student->created_at)),
            'updated_date' => date('d M y  h:i A', strtotime($student->updated_at))
        ];

        return $student
            ? response()->json([
                'status' => 'success',
                'data'    => $formattedStudent
            ], 200)

            : response()->json(['status' => 'error', 'message' => 'No student found with the provided ID.'], 404);
    }

    public function showStudentByPayload(showStudentByPayloadRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $student = $this->studentRepository->findByPayload($validated);

        return $student
            ? response()->json([
                'status' => 'success',
                'data' => [
                    'id'           => $student->id,
                    'name'         => $student->name,
                    'email'        => $student->email,
                    'age'          => $student->age . " years",
                    'course'       => $student->course,
                    'created_date' => date('d M y  h:i A', strtotime($student->created_at)),
                    'updated_date' => date('d M y  h:i A', strtotime($student->updated_at))
                ],
            ], 200)
            : response()->json([
                'status' => 'error',
                'message' => 'Student not found.'
            ], 404);
    }

    public function storeStudent(StudentRequest $request): JsonResponse
    {

        $data = $request->only(['name', 'email', 'age', 'course']);

        $student = $this->studentRepository->create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Student Created successfully.'
        ], 201);
    }

    public function updateStudentByParams(updateStudentRequest $request, $id): JsonResponse   // PUT (Using Params)
    {

        $data = $request->only(['name', 'email', 'age', 'course']);

        $student = $this->studentRepository->updateByParam($id, $data);

        return $student
            ? response()->json([
                'status' => 'success',
                'message' => 'Student updated successfully.'
            ], 200)

            : response()->json(['status' => 'error', 'message' => 'Student not found.'], 404);
    }

    public function updateStudentByPayload(UpdateStudentByPayloadRequest $request): JsonResponse
    {
        $validated = $request->validated(); // Validate input

        $student = $this->studentRepository->updateByPayload($validated);

        return $student
            ? response()->json([
                'status' => 'success',
                'message' => 'Student updated successfully.'
            ], 200)

            : response()->json(['status' => 'error', 'message' => 'Student not found.'], 404);
    }

    public function destroyStudent($id): JsonResponse // DELETE (Using Params)
    {
        $deleted = $this->studentRepository->deleteByParams($id);

        return $deleted
            ? response()->json(['status' => 'success'], 200)
            : response()->json(['status' => 'error', 'message' => 'Student not found.'], 404);
    }

    public function destroyStudentByPayload(deleteStudentByPayloadRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $student = $this->studentRepository->deleteByPayload($validated);

        return $student
            ? response()->json(['status' => 'success'], 200)
            : response()->json(['status' => 'error', 'message' => 'Student not found.'], 404);
    }

    public function exportStudents($students)
    {
        // Define the file path where the CSV file will be stored
        $filePath = storage_path('app/public/filtered_students.csv');

        // Save the file
        (new FastExcel($students))->export($filePath);

        return response()->json([
            'message'   => 'Filtered student data exported successfully!',
            'file_path' => asset('storage/filtered_students.csv')
        ]);
    }
}
