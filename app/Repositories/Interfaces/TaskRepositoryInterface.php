<?php

namespace App\Repositories\Interfaces;

use App\Models\Task;
use Illuminate\Pagination\LengthAwarePaginator;

interface TaskRepositoryInterface {
    public function create(int $userId, array $data): Task;
    public function FindById(int $userId, int $id): ?Task;
    public function FindAll(int $userId,int $perPage): LengthAwarePaginator;
    public function UpdateById(int $userId, array $task, int $id): ?Task;
    public function DeleteById(int $userId, int $id): ?Task;

}
