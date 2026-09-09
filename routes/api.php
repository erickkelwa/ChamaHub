<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\MemberApiController;
use App\Http\Controllers\Api\AdminApiController;
use App\Http\Controllers\Api\PushSubscriptionController;

// Public authentication routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Protected routes – require Sanctum token
Route::middleware('auth:sanctum')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Profile management
    Route::get('/profile', [MemberApiController::class, 'profile']);
    Route::patch('/profile', [MemberApiController::class, 'updateProfile']);

    // Contributions
    Route::get('/contributions', [MemberApiController::class, 'contributions']);

    // Loans
    Route::get('/loans', [MemberApiController::class, 'listLoans']);
    Route::post('/loans', [MemberApiController::class, 'applyLoan']);

    // Meetings
    Route::get('/meetings', [MemberApiController::class, 'meetings']);
    Route::get('/meetings/{id}', [MemberApiController::class, 'meetingDetail']);

    // Decisions / Polls
    Route::get('/decisions', [MemberApiController::class, 'decisions']);
    Route::post('/decisions/{id}/vote', [MemberApiController::class, 'vote']);

    // Notifications
    Route::get('/notifications', [MemberApiController::class, 'notifications']);

    // Web Push Subscriptions
    Route::post('/push-subscriptions', [PushSubscriptionController::class, 'store']);
    Route::delete('/push-subscriptions', [PushSubscriptionController::class, 'destroy']);

    // M-Pesa STK push
    Route::post('/mpesa/push', [MemberApiController::class, 'mpesaPush']);

    // Admin routes – wrapped in admin middleware and prefixed with /admin
    Route::middleware('admin')->prefix('admin')->group(function () {
        // Users CRUD
        Route::apiResource('users', AdminApiController::class);
        // Contributions CRUD
        Route::apiResource('contributions', AdminApiController::class);
        // Loans CRUD
        Route::apiResource('loans', AdminApiController::class);
        // Meetings CRUD
        Route::apiResource('meetings', AdminApiController::class);
        // Decisions CRUD
        Route::apiResource('decisions', AdminApiController::class);
        // Fines CRUD
        Route::apiResource('fines', AdminApiController::class);
        // Dividends and reports (custom actions)
        Route::get('/dividends', [AdminApiController::class, 'dividends']);
        Route::get('/reports', [AdminApiController::class, 'reports']);
        // Bulk STK push
        Route::post('/bulk-stk', [AdminApiController::class, 'bulkStkPush']);
    });
});
