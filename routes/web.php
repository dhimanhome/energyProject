<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\LiveEmployeeController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SiteController;
use App\Http\Controllers\Admin\SubmissionController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::get('/logout', [AuthController::class, 'resetSession'])->name('logout.reset');

Route::middleware(['auth', 'role:Admin|Supervisor'])->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/live-employees', [LiveEmployeeController::class, 'index'])->name('live-employees.index');
    Route::get('/live-employees/data', [LiveEmployeeController::class, 'data'])->name('live-employees.data');
    Route::resource('sites', SiteController::class);
    Route::resource('employees', EmployeeController::class);
    Route::resource('submissions', SubmissionController::class)->only(['index', 'show']);
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/csv', [ReportController::class, 'csv'])->name('reports.csv');
    Route::get('/reports/excel', [ReportController::class, 'excel'])->name('reports.excel');
    Route::get('/reports/pdf', [ReportController::class, 'pdf'])->name('reports.pdf');
});
