<?php

namespace App\Services;

use App\Models\Student;
use App\Repositories\Interfaces\StudentRepositoryInterface;

use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Storage;

use Illuminate\Database\Eloquent\Casts\Json;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;

class StudentService
{

    protected $studentRepositoryInterface;
    public function __construct(StudentRepositoryInterface $studentRepositoryInterface)
    {
        $this->studentRepositoryInterface = $studentRepositoryInterface;
    }

    public function getStudent(array $getData)
    {
        $getData = $this->getStudentData($getData);
        $formatStudent = $this->formatStudents($getData);

        return [
            'status' => 'success',
            'data' => $formatStudent,
            'status_code' => 200
        ];
    }

    protected function getStudentData(array $getData)
    {
        $student = $this->studentRepositoryInterface->getAll($getData);
        // dd($student);

        if (empty($student)) {
            throw new NotFoundHttpException('No students found.');
        }

        return $student;
    }

    protected function formatStudents(array $getData)
    {
        $formattedStudents = [];
        foreach ($getData as $key => $values) {
            $formattedStudents[$key]['name'] = $values['name'];
            $formattedStudents[$key]['email'] = $values['email'];
            $formattedStudents[$key]['age'] = $values['age'] . " years";
            $formattedStudents[$key]['course'] = $values['course'];
            $formattedStudents[$key]['created_date'] = date('d M y  h:i A', strtotime($values['created_date']));
            $formattedStudents[$key]['updated_date'] = date('d M y  h:i A', strtotime($values['updated_date']));
        }

        return $formattedStudents;
    }


    public function exportStudent(array $getData)
    {
        $data = $this->getStudentData($getData);
        // dd($data);
        $formatData = $this->formatStudents($data);

        $filePath = storage_path('app/public/filtered_students.csv');

        // (new FastExcel($format))->export($filePath);

        // (new FastExcel(collect($formatData)))->export($filePath, function ($student) {
        //     return [
        //         'Student Name'    => $student['name'],      // Renamed from 'name'
        //         'Student Email'   => $student['email'],     // Renamed from 'email'
        //         'Age (Years)'     => $student['age'],       // Renamed from 'age'
        //         'Enrolled Course' => $student['course'],    // Renamed from 'course'
        //         'Created On'      => $student['created_date'], // Renamed from 'created_date'
        //         'Updated On'      => $student['updated_date']  // Renamed from 'updated_date'
        //     ];
        // });


        $exportData = [];
        foreach ($formatData as $student) {
            $exportData[] = [
                'Student Name'    => $student['name'],
                'Student Email'   => $student['email'],
                'Age (Years)'     => $student['age'],
                'Enrolled Course' => $student['course'],
                'Created On'      => $student['created_date'],
                'Updated On'      => $student['updated_date']
            ];
        }

        (new FastExcel(collect($exportData)))->export($filePath);

        return [
            'message'   => 'Filtered student data exported successfully!',
            'file_path' => asset('storage/filtered_students.csv')
        ];
    }


    public function createStudent(array $data)
    {
        // $data['created_date'] = Carbon::now();
        // $data['updated_date'] = Carbon::now();

        $student = $this->studentRepositoryInterface->create($data);
        // dd($student);

        if ($student == null) {
            return [
                'status' => 'error',
                'message' => 'Student not created.'
            ];
        }

        return [
            'status' => 'success',
            'message' => 'Student Created successfully.',
            'status_code' => 201
        ];
    }


    public function updateStudent(array $validated)
    {
        $student = $this->studentRepositoryInterface->updateByPayload($validated);

        if ($student == null) {
            return [
                'status' => 'error',
                'message' => 'Student not found.',
                'status_code' => 404
            ];
        }

        return [
            'status' => 'success',
            'message' => 'Student updated successfully.',
            'status_code' => 200
        ];
    }


    public function deleteStudent(array $validated)
    {
        $student = $this->studentRepositoryInterface->deleteByPayload($validated);

        if ($student == false) {
            return [
                'status' => 'error',
                'message' => 'Student not found.',
                'status_code' => 404
            ];
        }

        return [
            'status' => 'success',
            'message' => 'Student Deleted successfully.',
            'status_code' => 200
        ];
    }


    public function getStudentById(array $validated)
    {
        try {
            $student = $this->studentRepositoryInterface->findByPayload($validated);

            $formateData = [
                'name' => $student->name,
                'email' => $student->email,
                'age' => $student->age,
                'course' => $student->course,
                'created_date' => date('d M y  h:i A', strtotime($student->created_date)),
                'updated_date' => date('d M y  h:i A', strtotime($student->updated_date))

            ];

            return [
                'status' => 'success',
                // 'message' => 'Student Found.',
                'data' => $formateData,
                'status_code' => 200
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'status' => 'error',
                // 'message' => 'Student not found.',
                'data' => [],
                'status_code' => 404
            ];
        }
    }



    public function getStudentByParams(int $id)
    {
        try {

            $student = $this->studentRepositoryInterface->findByParam($id);

            $formattedStudent = [
                'name'         => $student->name,
                'email'        => $student->email,
                'age'          => $student->age . " years",
                'course'       => $student->course,
                'created_date' => date('d M y  h:i A', strtotime($student->created_date)),
                'updated_date' => date('d M y  h:i A', strtotime($student->updated_date))
            ];

            return [
                'status' => 'success',
                // 'message' => 'Student Found.',
                'data' => $formattedStudent,
                'status_code' => 200
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'status' => 'error',
                // 'message' => 'Student not found.',
                'data' => [],
                'status_code' => 404
            ];
        }
    }


    public function updateStudentByParams(int $id, array $data)
    {

        $student = $this->studentRepositoryInterface->updateByParam($id, $data);

        if ($student == null) {
            return [
                'status' => 'error',
                'message' => 'Student not found.',
                'status_code' => 404
            ];
        }

        return [
            'status' => 'success',
            'message' => 'Student updated successfully.',
            'status_code' => 200
        ];
    }

    public function deleteStudentByParams(int $id)
    {

        $student = $this->studentRepositoryInterface->deleteByParams($id);

        if ($student == false) {
            return [
                'status' => 'error',
                'message' => 'Student not found.',
                'status_code' => 404
            ];
        }

        return [
            'status' => 'success',
            'message' => 'Student Deleted successfully.',
            'status_code' => 200
        ];
    }
}
