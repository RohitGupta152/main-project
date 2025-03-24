<?php

namespace App\Repositories;

use App\Models\Student;
use App\Repositories\Interfaces\StudentRepositoryInterface;

use Illuminate\Http\Request;

class StudentRepository implements StudentRepositoryInterface
{
    public function getAll()
    {
        return Student::all();
    }

    public function findByParam($id)
    {
        return Student::findOrFail($id);
    }

    public function findByPayload(array $data)
    {
        return Student::find($data['id']);
    }

    public function create(array $data)
    {
        return Student::create($data);
    }

    public function updateByParam($id, array $data)   // Using Params
    {
        $student = Student::find($id);
        if ($student) {
            $student->update($data);
            return $student;
        }
        return null;
    }

    public function updateByPayload(array $data)
    {
        $student = Student::find($data['id']);
        if ($student) {
            $student->update($data);
            return $student;
        }
        return null;
    }

    public function deleteByParams($id)
    {
        $student = Student::find($id);
        if ($student) {
            $student->delete();
            return true;
        }
        return false;
    }

    public function deleteByPayload(array $data): bool
    {
        return Student::where('id', $data['id'])->delete() ? true : false;
    }



    
    // Query Parameters
    // public function getData(Request $request): array
    // {
    //     return $request->all();
    // }

}
