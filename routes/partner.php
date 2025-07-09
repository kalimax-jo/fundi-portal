<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Partner\LoginController;
use App\Http\Controllers\Partner\DashboardController;
use App\Http\Controllers\Partner\UserController;
use App\Http\Controllers\Partner\BillingController;

// Test route to verify partner routes are loaded
Route::domain('{partner}.electronova.rw')->group(function () {
    // All partner routes should have the detect_partner_subdomain middleware
    Route::middleware(['detect_partner_subdomain'])->group(function () {
        // Login routes - these should be accessible without authentication
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('partner.login');
        Route::post('/login', [LoginController::class, 'login'])->name('partner.login.post');
        Route::post('/logout', [LoginController::class, 'logout'])->name('partner.logout');

        // Home route
        Route::get('/', function () {
            if (auth()->check()) {
                return redirect()->route('partner.dashboard');
            }
            return redirect()->route('partner.login');
        })->name('partner.home');

        // Protected routes that require authentication
        Route::middleware(['auth'])->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('partner.dashboard');
            Route::get('/users', [DashboardController::class, 'users'])->name('partner.users.index');
            Route::get('/users/create', [DashboardController::class, 'createUser'])->name('partner.users.create');
            Route::post('/users', [DashboardController::class, 'storeUser'])->name('partner.users.store');
            Route::get('/users/{user}/edit', [DashboardController::class, 'editUser'])->name('partner.users.edit');
            Route::put('/users/{user}', [DashboardController::class, 'updateUser'])->name('partner.users.update');
            Route::delete('/users/{user}', [DashboardController::class, 'removeUser'])->name('partner.users.remove');
            Route::post('/users/{user}/set-primary', [DashboardController::class, 'setPrimaryContact'])->name('partner.users.set-primary');
            Route::get('/users/{user}', [DashboardController::class, 'showUser'])->name('partner.users.show');
            
            Route::get('/test-dashboard', function () {
                return view('business-partner.dashboard.test');
            })->name('partner.test-dashboard');
            
            // Inspection requests (CRUD)
            Route::get('inspection-requests', [DashboardController::class, 'inspectionRequests'])->name('business-partner.inspection-requests.index');
            Route::get('inspection-requests/create', [DashboardController::class, 'createInspectionRequest'])->name('business-partner.inspection-requests.create');
            Route::post('inspection-requests', [DashboardController::class, 'storeInspectionRequest'])->name('business-partner.inspection-requests.store');
            Route::get('inspection-requests/{request}', [DashboardController::class, 'showInspectionRequest'])->name('business-partner.inspection-requests.show');
            Route::get('inspection-requests/{request}/edit', [DashboardController::class, 'editInspectionRequest'])->name('business-partner.inspection-requests.edit');
            Route::put('inspection-requests/{request}', [DashboardController::class, 'updateInspectionRequest'])->name('business-partner.inspection-requests.update');
            
            // Properties (CRUD)
            Route::get('properties', [DashboardController::class, 'properties'])->name('business-partner.properties.index');
            Route::get('properties/create', [DashboardController::class, 'createProperty'])->name('business-partner.properties.create');
            Route::post('properties', [DashboardController::class, 'storeProperty'])->name('business-partner.properties.store');
            Route::get('properties/{property}', [DashboardController::class, 'showProperty'])->name('business-partner.properties.show');
            Route::get('properties/{property}/edit', [DashboardController::class, 'editProperty'])->name('business-partner.properties.edit');
            Route::put('properties/{property}', [DashboardController::class, 'updateProperty'])->name('business-partner.properties.update');
            Route::delete('properties/{property}', [DashboardController::class, 'destroyProperty'])->name('business-partner.properties.destroy');
            Route::put('properties/{property}/status', [DashboardController::class, 'updatePropertyStatus'])->name('business-partner.properties.status');
            
            // Billing (requires auth)
            Route::get('billing', [\App\Http\Controllers\Partner\BillingController::class, 'index'])->name('partner.billing');
            Route::post('billing/select-tier/{tier}', [\App\Http\Controllers\Partner\BillingController::class, 'selectTier'])->name('partner.billing.select-tier');
            
            // Reports
            Route::get('reports', [DashboardController::class, 'reports'])->name('business-partner.reports.index');
            Route::get('reports/{report}', [DashboardController::class, 'showReport'])->name('business-partner.reports.show');
            Route::get('reports/{report}/download', [DashboardController::class, 'downloadReport'])->name('business-partner.reports.download');
        });

        // Flutterwave callback (no auth required, but needs subdomain context)
        Route::get('billing/flutterwave/callback', [\App\Http\Controllers\Partner\BillingController::class, 'flutterwaveCallback'])->name('partner.billing.flutterwave.callback');

        // Test routes for debugging
        Route::get('/partner-subdomain-check-test', function () {
            return 'New partner subdomain check middleware works!';
        });
    });
    // Test route to verify partner routes are loaded
    Route::get('/test', function () {
        return 'Partner routes are working! Subdomain: ' . request()->getHost();
    });
}); 