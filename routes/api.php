<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::post("/login",[AuthController::class,"Login"]);
Route::post("/register",[AuthController::class,'Register']);

Route::middleware('auth:sanctum')->group(function(){
    Route::post("/logout",[AuthController::class,'Logout']);
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::get('/tasks/{id}', [TaskController::class, 'show']);
    Route::put('/tasks/{id}', [TaskController::class, 'update']);
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);
    Route::middleware("role:admin")->group(function(){
        Route::patch('/tasks/{id}/complete', [TaskController::class, 'markComplete']);
    });
});
