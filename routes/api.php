<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
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
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);

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

// Data Busana API Routes
Route::get('data-busana', [DataBusanaController::class, 'index']); //->middleware('casbin:data-busana,read');
Route::get('data-busana/{id}', [DataBusanaController::class, 'show']); //->middleware('casbin:data-busana,read');
Route::post('data-busana', [DataBusanaController::class, 'store']); //->middleware('casbin:data-busana,create');
Route::put('data-busana/{id}', [DataBusanaController::class, 'update']); //->middleware('casbin:data-busana,update');
Route::delete('data-busana/{id}', [DataBusanaController::class, 'destroy']); //->middleware('casbin:data-busana,delete');

// Data Busana Kategori API Routes
Route::get('data-busana-kategori', [DataBusanaKategoriController::class, 'index']); //->middleware('casbin:data-busana-kategori,read');
Route::get('data-busana-kategori/{id}', [DataBusanaKategoriController::class, 'show']); //->middleware('casbin:data-busana-kategori,read');
Route::post('data-busana-kategori', [DataBusanaKategoriController::class, 'store']); //->middleware('casbin:data-busana-kategori,create');
Route::put('data-busana-kategori/{id}', [DataBusanaKategoriController::class, 'update']); //->middleware('casbin:data-busana-kategori,update');
Route::delete('data-busana-kategori/{id}', [DataBusanaKategoriController::class, 'destroy']); //->middleware('casbin:data-busana-kategori,delete');

// Custom route for simple kategori busana list (dropdown usage)
Route::get('data-busana-kategori-list', [DataBusanaKategoriController::class, 'list']); //->middleware('casbin:data-busana-kategori,read');

// Data Bunga Melati API Routes
Route::get('data-bunga-melati', [DataBungaMelatiController::class, 'index']); //->middleware('casbin:data-bunga-melati,read');
Route::get('data-bunga-melati/{id}', [DataBungaMelatiController::class, 'show']); //->middleware('casbin:data-bunga-melati,read');
Route::post('data-bunga-melati', [DataBungaMelatiController::class, 'store']); //->middleware('casbin:data-bunga-melati,create');
Route::put('data-bunga-melati/{id}', [DataBungaMelatiController::class, 'update']); //->middleware('casbin:data-bunga-melati,update');
Route::delete('data-bunga-melati/{id}', [DataBungaMelatiController::class, 'destroy']); //->middleware('casbin:data-bunga-melati,delete');

// Custom routes for Data Bunga Melati
Route::get('data-bunga-melati-list', [DataBungaMelatiController::class, 'list']); //->middleware('casbin:data-bunga-melati,read');
Route::get('data-bunga-melati-jenis-list', [DataBungaMelatiController::class, 'getJenisList']); //->middleware('casbin:data-bunga-melati,read');
Route::get('data-bunga-melati-bouquet-list', [DataBungaMelatiController::class, 'getBouquetList']); //->middleware('casbin:data-bunga-melati,read');
