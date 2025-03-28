<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;

interface StudentRepositoryInterface
{
    public function getAll(array $getData): array;
    public function findByParam($id);
    public function findByPayload(array $validated);
    public function create(array $data);
    public function updateByParam($id, array $data);
    public function updateByPayload(array $data);
    public function deleteByParams($id);
    public function deleteByPayload(array $data);
}
