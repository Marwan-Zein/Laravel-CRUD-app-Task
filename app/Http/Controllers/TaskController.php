<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiCreateException;
use App\Exceptions\ApiDeleteException;
use App\Exceptions\ApiNotFoundException;
use App\Exceptions\ApiUpdateException;
use App\Exceptions\DeleteTaskException;
use App\Exceptions\TaskCreateException;
use App\Exceptions\TaskUpdateException;
use App\Http\Requests\PaginationRequest;
use App\Http\Requests\TaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\PaginationResource;
use App\Http\Resources\TaskResource;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(
        private TaskRepositoryInterface $tasksRepository
    ) {
    }

    public function store(TaskRequest $request)
    {
        try{
            $validate = $request->validated();

            $task = $this->tasksRepository->create(
                $request->user()->id,
                $request
            );

            return response()->json([
                'status' => 'accepted',
                'data' => new TaskResource($task)
            ],201);
        }
        catch(ApiCreateException $e){
            return response()->json([
                'status'=>'failed',
                'message'=>$e->getMessage()
            ]);
        }
        catch(ModelNotFoundException $e){
            return response()->json([
                'status'=>'failed',
                'message'=>$e->getMessage()
            ]);
        }
    }

   public function index(PaginationRequest $paginationData)
    {
        try {
            $tasks = $this->tasksRepository->FindAll(
                auth()->id(),
                $paginationData
            );

            return response()->json([
                'status' => 'success',
                'tasks'  => $tasks
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status'  => 'failed',
                'message' => $e->getMessage()
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id, Request $request)
    {

        try{
            $task = $this->tasksRepository->FindById($id);

            return response()->json([
                'status' => 'Successed',
                'task' => new TaskResource($task)
            ]);
        }
        catch(ModelNotFoundException $e){
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
            ], 404);
        }


    }

    public function update(UpdateTaskRequest $request, $id)
    {
        try {
            $updatedTask = $this->tasksRepository->UpdateById(
                $request,
                (int) $id
            );

            return response()->json([
                'status' => 'success',
                'task'   => new TaskResource($updatedTask)
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status'  => 'failed',
                'message' => $e->getMessage()
            ], 404);
        } catch (ApiUpdateException $e) {
            return response()->json([
                'status'  => 'failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function destroy($id, Request $request)
    {

        try{
            $this->tasksRepository->DeleteById($id);

            return response()->json([
                'status' => 'successed',
                'message' => 'Task deleted'
            ]);

        }
        catch(ApiNotFoundException $e){
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
            ], 404);
        }
        catch(ApiDeleteException $e){
            return response()->json([
                'status'=>'failed',
                'message'=> $e->getMessage()
            ],500);
        }
    }

    public function markComplete($id, UpdateTaskRequest $request)
    {
        try{
            $task = $this->tasksRepository->FindById($id);

            $updatedTask = $this->tasksRepository->UpdateById(
                $request,
                $id
            );

            return response()->json([
                'status' => true,
                'message' => 'Task marked as completed',
                'task' => new TaskResource($updatedTask)
            ]);


        }
        catch(ModelNotFoundException $e){
            return response()->json([
                'status' => false,
                'message' => 'Task Not found'
            ], 404);

        }
        catch(ApiUpdateException $e){
            return response()->json([
                'status'=>'failed',
                'message'=>$e->getMessage()
            ],500);
        }
    }

}
