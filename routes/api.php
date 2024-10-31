<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BookController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\OrderController;
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


// Auth
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/send-otp', [AuthController::class, 'sendOTP']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOTP']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

// User
Route::prefix('user')->middleware('auth:api')->group(function () {
    Route::get('/{id}', [UserController::class, 'getUserDetails']);
    Route::get('/', [UserController::class, 'getListOfUsers']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::put('/{id}', [UserController::class, 'updateUser']);
    Route::put('/{id}/change-password', [UserController::class, 'changePassword']);
    Route::delete('/{id}', [UserController::class, 'deleteUser']);
    Route::get('/{id}/books', [UserController::class, 'getAllUserBooks']);
});

// Category
Route::prefix('category')->group(function () {
    Route::get('/', [CategoryController::class, 'getListOfCategories']);
    Route::get('/{id}', [CategoryController::class, 'getCategoryDetails']);
    Route::post('/', [CategoryController::class, 'createCategory']);
    Route::put('/{id}', [CategoryController::class, 'updateCategory']);
    Route::delete('/{id}', [CategoryController::class, 'deleteCategory']);
    Route::get('/{id}/books', [CategoryController::class, 'getAllCategoryBooks']);
});

// Book
Route::prefix('book')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::post('/', [BookController::class, 'createBook']);
    });
    Route::get('/', [BookController::class, 'getListOfBooks']);
    Route::get('/{id}', [BookController::class, 'getBookDetails']);
    Route::put('/{id}', [BookController::class, 'updateBook']);
    Route::delete('/{id}', [BookController::class, 'deleteBook']);
});

// Order
Route::prefix('order')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::post('/', [OrderController::class, 'createOrder']);
    });
    Route::get('/filtered', [OrderController::class, 'getFilterOrders']);
    Route::get('/', [OrderController::class, 'getListOfOrders']);
    Route::get('/{id}', [OrderController::class, 'getOrderDetails']);
});

Route::prefix('cache')->group(function () {
    Route::post('/', [OrderController::class, 'storeOrderInCache']);
    Route::get('/{token}', [OrderController::class, 'getOrderFromCacheByToken']);
    Route::delete('/{token}', [OrderController::class, 'deleteOrderFromCacheByToken']);
});
