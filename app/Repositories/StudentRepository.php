<?php

namespace App\Repositories;

use App\Models\Student;
use App\Repositories\Interfaces\StudentRepositoryInterface;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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

    public function findByPayload(array $validated)
    {
        return Student::findOrFail($validated['id']);
    }

    public function create(array $data)
    {
        $data['created_date'] = Carbon::now();
        $data['updated_date'] = Carbon::now();
        return Student::create($data);
    }

    public function updateByParam($id, array $data)   // Using Params
    {
        $student = Student::find($id);
        
        if ($student) {
            $student->update([
                'name' => $data['name'],
                'email' =>$data['email'],
                'age' => $data['age'],
                'course' => $data['course'],
                'updated_date' => now(),
                ]);

            return $student;
        }
        return null;
    }

    public function updateByPayload(array $data)
    {
        $student = Student::find($data['id']);
        
        if ($student) {
            $student->update([
            'name' => $data['name'],
            'email' =>$data['email'],
            'age' => $data['age'],
            'course' => $data['course'],
            'updated_date' => now(),
            ]);

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
