<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InspectionRequestController as UserInspectionRequestController;

// Main landing page
Route::get('/', function () {
    return view('welcome');
});

// Admin login routes
Route::get('/admin/login', [LoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [LoginController::class, 'login'])->name('admin.login.post');

// Authenticated user routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/inspection-requests/create', [UserInspectionRequestController::class, 'create'])->name('inspection-requests.create');
    Route::post('/inspection-requests', [UserInspectionRequestController::class, 'store'])->name('inspection-requests.store');
    Route::get('/my-requests', [UserInspectionRequestController::class, 'myRequests'])->name('my-requests');
    Route::get('/my-properties', [UserInspectionRequestController::class, 'myProperties'])->name('my-properties');
    Route::get('/profile', [UserInspectionRequestController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserInspectionRequestController::class, 'updateProfile'])->name('profile.update');
    Route::get('/inspection-requests/{inspectionRequest}', [UserInspectionRequestController::class, 'show'])->name('inspection-requests.show');
    Route::get('/inspection-requests/{inspectionRequest}/report/download', [UserInspectionRequestController::class, 'downloadReport'])->name('inspection-requests.report.download');
});

// Test route for main domain
Route::get('/test', function () {
    return 'Laravel is working!';
});