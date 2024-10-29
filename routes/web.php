<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GoogleSocialiteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Auth
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'store'])->name('store');
Route::post('/send-otp', [AuthController::class, 'sendOTP'])->name('sendOTP');
Route::get('/verify', [AuthController::class, 'verify'])->name('verify');
Route::post('/verify', [AuthController::class, 'verifyOTP'])->name('verifyOTP');

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');

Route::prefix('admin')->middleware('admin')->group(function () {
    // User
    Route::post('/user', [UserController::class, 'store'])->name('user.store');
    Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::put('/user/{id}/update', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user/{id}/destroy', [UserController::class, 'destroy'])->name('user.destroy');
    Route::post('/user/toggle-status/{id}', [UserController::class, 'toggleStatus'])->name('user.toggleStatus');


    // Category
    Route::get('/category', [CategoryController::class, 'index'])->name('category.index');
    Route::post('/category', [CategoryController::class, 'store'])->name('category.store');
    Route::get('/category/{id}/edit', [CategoryController::class, 'edit'])->name('category.edit');
    Route::put('/category/{id}/update', [CategoryController::class, 'update'])->name('category.update');
    Route::delete('/category/{id}/destroy', [CategoryController::class, 'destroy'])->name('category.destroy');

    // Book
    Route::get('/book', [BookController::class, 'index'])->name('book.index');
    Route::post('/book', [BookController::class, 'store'])->name('book.store');
    Route::get('/book/{id}/edit', [BookController::class, 'edit'])->name('book.edit');
    Route::put('/book/{id}/update', [BookController::class, 'update'])->name('book.update');
    Route::delete('/book/{id}/destroy', [BookController::class, 'destroy'])->name('book.destroy');
});
Route::group(['middleware' => 'auth'], function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // User
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
});

Route::get('/enter-email', [AuthController::class, 'enterEmail'])->name('enterEmail');
Route::post('/send-reset', [AuthController::class, 'sendResetLink'])->name('sendResetLink');
Route::get('/password/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [AuthController::class, 'reset'])->name('password.update');

Route::get('auth/google', [GoogleSocialiteController::class, 'redirectToGoogle']);
Route::get('callback/google', [GoogleSocialiteController::class, 'handleCallback']);
