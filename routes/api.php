<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;


Route::get('/test',function(Request $request)
{
    return response()->json(['message' => 'test'],Response::HTTP_OK);
});

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);

});


Route::apiResource('tasks',TaskController::class);

Route::patch('tasks/{task}/complete', [TaskController::class, 'markAsCompleted']);

Route::apiResource('users',UserController::class);










