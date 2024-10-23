<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/users/{id}/edit', [UserController::class, 'edit']);

Route::get('/users', [UserController::class, 'getUserList']);


Route::get('/categories', [CategoryController::class, 'list']);
// Route::get('/categories/{category}', [CategoryController::class, 'details']);
Route::get('/categories/{id}', [CategoryController::class, 'details']);
Route::post('/categories', [CategoryController::class, 'create']);
Route::put('/categories/{id}', [CategoryController::class, 'updateApi']);
Route::delete('/categories/{id}', [CategoryController::class, 'delete']);
