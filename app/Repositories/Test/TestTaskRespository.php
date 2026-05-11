<?php
namespace App\Repositories\Test;

use App\Models\Task;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Override;

class TestTaskRespository implements TaskRepositoryInterface{
    /**
     * [
     *      UserID => [
     *              TaskId => TAsk
     *         ]
     * ]
     */
    private array $tasks = [];
    private int $nextId = 1;


    #[Override]
    public function create(int $userId , array $data): Task {
        $task = new Task([
            ...$data,
            'user_id'=>$userId
        ]);

        $task->id = $this->nextId++;

        $this->tasks[$userId][$task->id] = $task;

        return $task;
    }

    #[Override]
    public function FindById(int $userID , int $id): ?Task{
        return $this->tasks[$userID][$id] ?? null;
    }

    #[Override]
    public function FindAll(int $userId , int $page_size=10): LengthAwarePaginator {
        $tasks = array_values($this->tasks[$userId] ?? []);

        return new LengthAwarePaginator(
            array_slice($tasks, 0, $page_size),
            count($tasks),
            $page_size,
            1
        );
    }

    #[Override]
    public function UpdateById(int $userID , array $taskData , int $id): ?Task {
        $task = $this->FindById($userID , $id);
        if(!$task){
            return null;
        }

        $task->fill($taskData);
        return $task;
    }

    #[Override]
    public function DeleteById(int $userId, int $id): ?Task
    {
        $task = $this->FindById($userId,$id);
        if(!$task) return null;

        unset($this->tasks[$userId][$id]);
        return $task;

    }

}
