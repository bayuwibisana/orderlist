<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;


Route::get('/', function () {
    return view('welcome');
});

Route::post('/register', [AuthController::class, 'register']); 

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('me', [AuthController::class, 'me']);

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'user']);

Route::middleware('auth:sanctum')->post('/order', [OrderController::class, 'store']);

Route::middleware('auth:sanctum')->get('/orders', [OrderController::class, 'index']);

Route::middleware('auth:sanctum')->put('/orders_update_position', [OrderController::class, 'updatePosition']);

Route::middleware('auth:sanctum')->put('/order_update', [OrderController::class, 'update']);

Route::middleware('auth:sanctum')->delete('/delete_order/{id}', [OrderController::class, 'destroy']);