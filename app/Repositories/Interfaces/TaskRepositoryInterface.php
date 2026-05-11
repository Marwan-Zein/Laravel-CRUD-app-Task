<?php

namespace App\Repositories\Interfaces;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\PaginationRequest;
use App\Http\Requests\TaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\PaginationResource;
use App\Models\Task;
use Illuminate\Pagination\LengthAwarePaginator;

interface TaskRepositoryInterface {
    public function create(int $userId, TaskRequest $data): Task;
    public function FindById(int $id): ?Task;
    public function FindAll(int $userId,PaginationRequest $paginationData);
    public function UpdateById(UpdateTaskRequest $task, int $id): ?Task;
    public function DeleteById(int $id): ?Task;

}
