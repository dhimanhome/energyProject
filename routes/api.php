<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmployeeHistoryController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\SiteController;
use App\Http\Controllers\Api\SubmissionController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

Route::middleware(['auth:sanctum', 'throttle:api'])->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/submission/store', [SubmissionController::class, 'store']);
    Route::post('/location/update', [LocationController::class, 'update']);
    Route::get('/sites', [SiteController::class, 'index']);
    Route::get('/employee/history', [EmployeeHistoryController::class, 'index']);
});
