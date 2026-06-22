<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

// Public pages
Route::get('/', [PageController::class, 'index']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,60');

// Auth check API (used by JS)
Route::get('/api/auth/check', [AuthController::class, 'checkSession']);

// Protected routes
Route::middleware('auth.session')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dompet', [DashboardController::class, 'dompet']);
    Route::get('/tabungan', [DashboardController::class, 'tabungan']);
    Route::get('/profile', [DashboardController::class, 'profile']);

    // Transactions API
    Route::get('/api/transactions', [TransactionController::class, 'index']);
    Route::post('/api/transactions', [TransactionController::class, 'store']);
    Route::put('/api/transactions/{id}', [TransactionController::class, 'update']);
    Route::delete('/api/transactions/{id}', [TransactionController::class, 'destroy']);
    Route::get('/api/transactions/monthly', [TransactionController::class, 'getMonthly']);

    // User API
    Route::get('/api/user/balance', [TransactionController::class, 'getBalance']);
    Route::put('/api/user/profile', [UserController::class, 'updateProfile']);
    Route::put('/api/user/password', [UserController::class, 'changePassword']);
    Route::put('/api/user/avatar', [UserController::class, 'updateAvatar']);
    Route::put('/api/user/quote', [UserController::class, 'updateQuote']);

    // Categories API
    Route::get('/api/categories', [CategoryController::class, 'index']);

    // Wallets API
    Route::get('/api/wallets', [WalletController::class, 'index']);
    Route::post('/api/wallets/transfer', [WalletController::class, 'transfer']);
    Route::get('/api/wallets/savings-target', [WalletController::class, 'savingsTarget']);
    Route::put('/api/wallets/savings-target', [WalletController::class, 'updateSavingsTarget']);

    // Dashboard API
    Route::get('/api/dashboard/stats', [DashboardController::class, 'stats']);

    // Admin page
    Route::prefix('/admin')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard']);
    });

    // Admin API
    Route::prefix('/api/admin')->group(function () {
        Route::get('/stats', [AdminController::class, 'getStats']);
        Route::get('/users', [AdminController::class, 'getUsers']);
        Route::post('/users', [AdminController::class, 'addUser']);
        Route::put('/users/{id}', [AdminController::class, 'editUser']);
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);
        Route::get('/users/{id}/transactions', [AdminController::class, 'getUserTransactions']);
    });
});
