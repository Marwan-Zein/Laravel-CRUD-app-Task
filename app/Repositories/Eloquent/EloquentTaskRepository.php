<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\ApiCreateException;
use App\Exceptions\ApiDeleteException;
use App\Exceptions\ApiNotFoundException;
use App\Exceptions\ApiUpdateException;
use App\Exceptions\DeleteTaskException;
use App\Exceptions\TaskCreateException;
use App\Exceptions\TaskUpdateException;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\PaginationRequest;
use App\Http\Requests\TaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\PaginationResource;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentTaskRepository implements TaskRepositoryInterface
{
    public function create(int $userId, TaskRequest $data): Task
    {
        // $data['user_id'] = $userId;
        // $task = Task::create($data->validated());
        $user = User::find($userId);
        if(!$user){
            // throw new ModelNotFoundException("Cannot Find User with this Id");
            throw new ApiNotFoundException(User::class);
        }
        $task = $user->tasks()->create($data->validated());

        if(!$task){
            throw new ApiCreateException(Task::class);
        }
        return $task;
    }

    public function FindById(int $id): ?Task
    {
        // return Task::where('user_id', $userId)->find($id);

        $task = Task::find($id);

        if(!$task){
            // throw new ModelNotFoundException("Cannot Find Given Task");
            throw new ApiNotFoundException(Task::class);
        }
        return $task;
    }

    public function FindAll(int $userId,PaginationRequest $paginationData)
    {
        // return Task::where('user_id', $userId)->get()->all();

        $tasks = Task::where('user_id',$userId);

        if($paginationData->paginate){
            return $tasks->paginate($paginationData->per_page);
        }
        return $tasks->get();

        // return Task::where('user_id',$userId)
        //     ->paginate($perPage);
    }

    public function UpdateById(UpdateTaskRequest $data, int $id): ?Task
    {
        $task = $this->FindById($id);

        $flag = $task->update($data->validated());

        if(!$flag){
            throw new ApiUpdateException(Task::class);
        }

        return $task;
    }

    public function DeleteById(int $id): ?Task
    {
        $task = $this->FindById($id);


        $flag = $task->delete();

        if(!$flag){
            throw new ApiDeleteException(Task::class);
        }

        return $task;
    }
}
