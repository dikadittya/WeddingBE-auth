<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Users CRUD with Casbin authorization
    Route::middleware('casbin:users,GET')->get('/users', [UserController::class, 'index']);
    Route::middleware('casbin:users,GET')->get('/users/{user}', [UserController::class, 'show']);
    Route::middleware('casbin:users,POST')->post('/users', [UserController::class, 'store']);
    Route::middleware('casbin:users,PUT')->put('/users/{user}', [UserController::class, 'update']);
    Route::middleware('casbin:users,PATCH')->patch('/users/{user}', [UserController::class, 'update']);
    Route::middleware('casbin:users,DELETE')->delete('/users/{user}', [UserController::class, 'destroy']);
});
