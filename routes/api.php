<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CasbinRuleController;
use App\Http\Controllers\Api\DataBungaMelatiController;
use App\Http\Controllers\Api\DataBusanaController;
use App\Http\Controllers\Api\DataBusanaKategoriController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\MenuController;
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
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    Route::get('/menus/sidebar', [MenuController::class, 'sidebar']); // Sidebar menu for authenticated user

    // Users CRUD with Casbin authorization
    Route::middleware('casbin:users,GET')->get('/users', [UserController::class, 'index']);
    Route::middleware('casbin:users,GET')->get('/users/{user}', [UserController::class, 'show']);
    Route::middleware('casbin:users,POST')->post('/users', [UserController::class, 'store']);
    Route::middleware('casbin:users,PUT')->put('/users/{user}', [UserController::class, 'update']);
    Route::middleware('casbin:users,PATCH')->patch('/users/{user}', [UserController::class, 'update']);
    Route::middleware('casbin:users,DELETE')->delete('/users/{user}', [UserController::class, 'destroy']);

    // Menus CRUD with Casbin authorization
    Route::middleware('casbin:menus,GET')->get('/menus', [MenuController::class, 'index']);
    Route::middleware('casbin:menus,GET')->get('/menus/tree', [MenuController::class, 'tree']);
    Route::middleware('casbin:menus,GET')->get('/menus/{menu}', [MenuController::class, 'show']);
    Route::middleware('casbin:menus,POST')->post('/menus', [MenuController::class, 'store']);
    Route::middleware('casbin:menus,PUT')->put('/menus/{menu}', [MenuController::class, 'update']);
    Route::middleware('casbin:menus,PATCH')->patch('/menus/{menu}', [MenuController::class, 'update']);
    Route::middleware('casbin:menus,DELETE')->delete('/menus/{menu}', [MenuController::class, 'destroy']);
});

Route::get('/members', [MemberController::class, 'index']);
Route::get('/members/{id}', [MemberController::class, 'show']);

// Casbin Rules CRUD (for super_admin only)
Route::get('/casbin-rules', [CasbinRuleController::class, 'index']);
Route::get('/casbin-rules/{id}', [CasbinRuleController::class, 'show']);
Route::post('/casbin-rules', [CasbinRuleController::class, 'store']);
Route::put('/casbin-rules/{id}', [CasbinRuleController::class, 'update']);
Route::patch('/casbin-rules/{id}', [CasbinRuleController::class, 'update']);
Route::delete('/casbin-rules/{id}', [CasbinRuleController::class, 'destroy']);

// Additional Casbin helper routes
Route::get('/casbin-rules/subject/{subject}/policies', [CasbinRuleController::class, 'getPoliciesForSubject']);
Route::get('/casbin-rules/user/{user}/roles', [CasbinRuleController::class, 'getRolesForUser']);
