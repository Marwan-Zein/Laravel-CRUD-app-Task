<?php

namespace App\Repositories\Eloquent;

use App\Models\Task;
use App\Repositories\Interfaces\TaskRepositoryInterface;

class EloquentTaskRepository implements TaskRepositoryInterface
{
    public function create(int $userId, array $data): Task
    {
        $data['user_id'] = $userId;

        return Task::create($data);
    }

    public function FindById(int $userId, int $id): ?Task
    {
        return Task::where('user_id', $userId)->find($id);
    }

    public function FindAll(int $userId): array
    {
        return Task::where('user_id', $userId)->get()->all();
    }

    public function UpdateById(int $userId, array $data, int $id): ?Task
    {
        $task = Task::where('user_id', $userId)->find($id);

        if (!$task) {
            return null;
        }

        $task->update($data);

        return $task->fresh();
    }

    public function DeleteById(int $userId, int $id): ?Task
    {
        $task = Task::where('user_id', $userId)->find($id);

        if (!$task) {
            return null;
        }

        $task->delete();

        return $task;
    }
}
