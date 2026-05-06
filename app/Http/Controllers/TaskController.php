<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(
        private TaskRepositoryInterface $tasksRepository
    ) {
    }

    public function store(TaskRequest $request)
    {

        $validate = $request->validated();

        $task = $this->tasksRepository->create(
            $request->user()->id,
            [
                'title' => $request->title,
                'description' => $request->description,
            ]
        );

        return response()->json([
            'status' => true,
            'data' => new TaskResource($task)
        ],201);
    }

    public function index(Request $request)
    {
        $tasks = $this->tasksRepository->FindAll($request->user()->id);

        if(!$tasks){
            return response()->json([
                'status'=>false,
                'message'=>'tasks not found'
            ],404);
        }
        return response()->json([
            'status' => true,
            'tasks' => $tasks
        ]);
    }

    public function show($id, Request $request)
    {
        $task = $this->tasksRepository->FindById($request->user()->id, $id);

        if (!$task) {
            return response()->json([
                'status' => false,
                'message' => 'Task not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'task' => new TaskResource($task)
        ]);
    }

    public function update(UpdateTaskRequest $request, $id)
    {
        $validate = $request->validated();

        $task = $this->tasksRepository->FindById($request->user()->id, $id);

        if (!$task) {
            return response()->json([
                'status' => false,
                'message' => 'Task not found'
            ], 404);
        }

        $updatedTask = $this->tasksRepository->UpdateById(
            $request->user()->id,
            $request->only('title', 'description'),
            $id
        );

        return response()->json([
            'status' => true,
            'task' => new TaskResource($updatedTask)
        ]);
    }

    public function destroy($id, Request $request)
    {
        $task = $this->tasksRepository->FindById($request->user()->id, $id);

        if (!$task) {
            return response()->json([
                'status' => false,
                'message' => 'Task not found'
            ], 404);
        }

        $this->tasksRepository->DeleteById($request->user()->id, $id);

        return response()->json([
            'status' => true,
            'message' => 'Task deleted'
        ]);
    }

    public function markComplete($id, Request $request)
    {
        $task = $this->tasksRepository->FindById($request->user()->id, $id);

        if (!$task) {
            return response()->json([
                'status' => false,
                'message' => 'Task Not found'
            ], 404);
        }

        $updatedTask = $this->tasksRepository->UpdateById(
            $request->user()->id,
            [
                'status' => 'compeleted'
            ],
            $id
        );

        return response()->json([
            'status' => true,
            'message' => 'Task marked as completed',
            'task' => new TaskResource($updatedTask)
        ]);

    }

}
