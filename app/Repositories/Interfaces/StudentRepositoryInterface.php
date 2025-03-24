<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;

interface StudentRepositoryInterface
{
    public function getAll();
    public function findByParam($id);
    public function findByPayload(array $data);
    public function create(array $data);
    public function updateByParam($id, array $data);
    public function updateByPayload(array $data);
    public function deleteByParams($id);
    public function deleteByPayload(array $data);


    // public function updateByPayload(int $id, array $data);

    // Query Parameters
    // public function getData(Request $request): array;
    
}
