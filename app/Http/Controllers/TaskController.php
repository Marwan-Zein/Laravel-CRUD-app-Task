<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request){
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string'
        ]);

        $task = $request->user()->tasks()->create([
            'title' => $request->title,
            'description' => $request->description
        ]);

        return response()->json([
            'status' => true,
            'task' => $task
        ]);
    }

    public function index(Request $request)
    {
        return response()->json([
            'status' => true,
            'tasks' => $request->user()->tasks
        ]);
    }

    public function show($id, Request $request)
    {
        $task = $request->user()->tasks()->find($id);

        if (!$task) {
            return response()->json([
                'status' => false,
                'message' => 'Task not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'task' => $task
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'sometimes|string',
            'description' => 'sometimes|string'
        ]);

        $task = $request->user()->tasks()->find($id);

        if (!$task) {
            return response()->json([
                'status' => false,
                'message' => 'Task not found'
            ], 404);
        }

        $task->update($request->only(['title', 'description']));

        return response()->json([
            'status' => true,
            'task' => $task
        ]);
    }

    public function destroy($id, Request $request)
    {
        $task = $request->user()->tasks()->find($id);

        if (!$task) {
            return response()->json([
                'status' => false,
                'message' => 'Task not found'
            ], 404);
        }

        $task->delete();

        return response()->json([
            'status' => true,
            'message' => 'Task deleted'
        ]);
    }

    public function markComplete($id,Request $request){
        $task = $request->user()->tasks()->find($id);

        if(!$task){
            return response()->json([
                'status'=>false,
                'message' => 'Task Not found'
            ],404);
        }

        $task->update([
            'status' => 'compeleted'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Task marked as completed',
            'task' => $task
        ]);

    }

}
