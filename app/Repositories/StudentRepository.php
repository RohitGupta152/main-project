<?php

namespace App\Repositories;

use App\Models\Student;
use App\Repositories\Interfaces\StudentRepositoryInterface;
use Illuminate\Http\Request;



class StudentRepository implements StudentRepositoryInterface
{
    public function getAll(array $getData): array
    {
        // dd($getData);

        $query = student::query();

        if (!empty($getData['user_id'])) {
            $query->where('id', $getData['user_id']);
        }
        // dd($query->get());

        if (!empty($getData['name'])) {
            $query->where('name', 'LIKE', "%{$getData['name']}%"); //$query->whereIn('order_id', explode(' ', string: $filters['order_id']))
        }

        if (!empty($getData['email'])) {
            $query->where('email', 'LIKE', "%{$getData['email']}%");
        }

        if (!empty($getData['age'])) {
            $query->where('age',  $getData['age']);
        }

        if (!empty($getData['course'])) {
            $query->where('course', 'LIKE', "%{$getData['course']}%");
        }

        return $query->get()->toArray();
        // dd($query->get()->toArray());


        // return Student::all();
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
}
