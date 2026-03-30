<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// Public authentication routes (prefix: /api/auth)
Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

// Authenticated authentication routes (prefix: /api/auth)
Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'auth'
], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});

// Profile management routes (prefix: /api/users)
Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'users'
], function () {
    Route::get('me', [UserController::class, 'me']);
    Route::put('me', [UserController::class, 'update']);
    Route::patch('me/password', [UserController::class, 'changePassword']);
});

// Accounts management routes (prefix: /api/accounts)
Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'accounts'
], function () {
    Route::post('/', [AccountController::class, 'store']);
    Route::get('/', [AccountController::class, 'index']);
    Route::get('/{id}', [AccountController::class, 'show']);
});

// Transfers and Transactions (prefix: /api)
Route::group([
    'middleware' => 'auth:api',
], function () {
    Route::post('/transfers', [TransferController::class, 'transfer']);
    Route::get('/accounts/{id}/transactions', [TransactionController::class, 'index']);
});

// Admin routes (prefix: /api/admin)
Route::group([
    'middleware' => ['auth:api', 'admin'],
    'prefix' => 'admin'
], function () {
    Route::get('/accounts', [AdminController::class, 'index']);
    Route::patch('/accounts/{id}/block', [AdminController::class, 'block']);
    Route::patch('/accounts/{id}/unblock', [AdminController::class, 'unblock']);
    Route::patch('/accounts/{id}/close', [AdminController::class, 'close']);
});
