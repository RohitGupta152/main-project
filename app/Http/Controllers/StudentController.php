<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentRequest;
use App\Http\Requests\updateStudentRequest;

use App\Http\Requests\showStudentByPayloadRequest;
use App\Http\Requests\updateStudentByPayloadRequest;
use App\Http\Requests\deleteStudentByPayloadRequest;

// use App\Repositories\StudentRepository;
use App\Repositories\Interfaces\StudentRepositoryInterface;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    protected $studentRepository;

    public function __construct(StudentRepositoryInterface $studentRepository)
    {
        $this->studentRepository = $studentRepository;
    }
    
    // public function getAllStudents(): JsonResponse  // GET All (Using Params)
    // {
    //     $students = $this->studentRepository->getAll();

    //     return $students
    //         ? response()->json(['status' => 'success', 'data' => $students], 200)
    //         : response()->json(['status' => 'error', 'message' => 'No students found.'], 404);
    // }

    
    public function getAllStudents(): JsonResponse
    {
        $students = $this->studentRepository->getAll();
        
        $formattedStudents = [];
        foreach ($students as $key => $values) {
            // Format dates
            $formattedStudents[$key]['id'] = $values->id;
            $formattedStudents[$key]['name'] = $values->name;
            $formattedStudents[$key]['email'] = $values->email;
            $formattedStudents[$key]['age'] = $values->age." years";
            $formattedStudents[$key]['course'] = $values->course;
            $formattedStudents[$key]['created_date'] = date('d M y  h:i A', strtotime($values->created_at));
            $formattedStudents[$key]['updated_date'] = date('d M y  h:i A', strtotime($values->updated_at));
            
            // $formattedStudents[] = $student;
        }

        return $students 
        ? response()->json([
            'status' => 'success',
            'data' => $formattedStudents
        ], 200)
        : response()->json(['status' => 'error', 'message' => 'No students found.'], 404);

    }  

    public function fetchAllStudents(Request $request): JsonResponse    // GET All (Using Payload)
    {
        $students = $this->studentRepository->getAll();

        $formattedStudents = [];
        foreach ($students as $key => $values) {
            // Format dates
            $formattedStudents[$key]['id'] = $values->id;
            $formattedStudents[$key]['name'] = $values->name;
            $formattedStudents[$key]['email'] = $values->email;
            $formattedStudents[$key]['age'] = $values->age." years";
            $formattedStudents[$key]['course'] = $values->course;
            $formattedStudents[$key]['created_date'] = date('d M y  h:i A', strtotime($values->created_at));
            $formattedStudents[$key]['updated_date'] = date('d M y  h:i A', strtotime($values->updated_at));
        }

        return $students
            ? response()->json([
                'status' => 'success',
                'data'   => $formattedStudents
            ], 200)
            : response()->json([
                'status'  => 'error',
                'message' => 'No students found.'
            ], 404);
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

    // public function showStudentByPayload(showStudentByPayloadRequest $request): JsonResponse // POST (Using Payload)
    // {

    //     $validated = $request->validated();
    //     $student = $this->studentRepository->findByPayload($validated);

    //     $formattedStudent = [
    //         'id'           => $student->id,
    //         'name'         => $student->name,
    //         'email'        => $student->email,
    //         'age'          => $student->age . " years",
    //         'course'       => $student->course,
    //         'created_date' => date('d M y  h:i A', strtotime($student->created_at)),
    //         'updated_date' => date('d M y  h:i A', strtotime($student->updated_at))
    //     ];

    //     return $student 
    //         ? response()->json([
    //             'status' => 'success',
    //             'data'    => $formattedStudent,
    //         ], 200) 
    //         : response()->json([
    //             'status' => 'error', 
    //             'message' => 'Student not found.'
    //         ], 404);
    // }


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

}








// namespace App\Http\Controllers;

// use App\Http\Requests\StudentRequest;
// use App\Repositories\Interfaces\StudentRepositoryInterface;
// use Illuminate\Http\JsonResponse;

// class StudentController extends Controller
// {
//     protected StudentRepositoryInterface $studentRepository;

//     public function __construct(StudentRepositoryInterface $studentRepository)
//     {
//         $this->studentRepository = $studentRepository;
//     }

//     /**
//      * Display a listing of students.
//      */
//     public function index(): JsonResponse
//     {
//         $students = $this->studentRepository->getAll();
//         return response()->json($students, 200);
//     }

//     /**
//      * Display the specified student.
//      */
//     public function show(int $id): JsonResponse
//     {
//         $student = $this->studentRepository->find($id);

//         if (!$student) {
//             return response()->json(['message' => 'Student not found'], 404);
//         }

//         return response()->json($student, 200);
//     }

//     /**
//      * Store a newly created student in storage.
//      */
//     public function store(StudentRequest $request): JsonResponse
//     {
//         $student = $this->studentRepository->create($request->validated());
//         return response()->json($student, 201);
//     }

//     /**
//      * Update the specified student in storage.
//      */
//     public function update(StudentRequest $request, int $id): JsonResponse
//     {
//         $student = $this->studentRepository->update($id, $request->validated());

//         if (!$student) {
//             return response()->json(['message' => 'Student not found'], 404);
//         }

//         return response()->json($student, 200);
//     }

//     /**
//      * Remove the specified student from storage.
//      */
//     public function destroy(int $id): JsonResponse
//     {
//         if (!$this->studentRepository->delete($id)) {
//             return response()->json(['message' => 'Student not found'], 404);
//         }

//         return response()->json(['message' => 'Student deleted successfully'], 200);
//     }
// }

