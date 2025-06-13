<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PropertyController;

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

// Public routes (no authentication required)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/roles', [AuthController::class, 'getAvailableRoles']);
});

// Protected routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {
    
    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::put('/password', [AuthController::class, 'changePassword']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    });

    // Property routes
    Route::prefix('properties')->group(function () {
        Route::get('/', [PropertyController::class, 'index']);
        Route::post('/', [PropertyController::class, 'store']);
        Route::get('/locations', [PropertyController::class, 'getLocations']);
        Route::get('/nearby', [PropertyController::class, 'findNearby']);
        Route::get('/{id}', [PropertyController::class, 'show']);
        Route::put('/{id}', [PropertyController::class, 'update']);
        Route::delete('/{id}', [PropertyController::class, 'destroy']);
        Route::post('/{id}/photos', [PropertyController::class, 'uploadPhotos']);
        Route::delete('/{id}/photos', [PropertyController::class, 'deletePhoto']);
        Route::get('/{id}/statistics', [PropertyController::class, 'getStatistics']);
    });

    // Test route to verify API is working
    Route::get('/test', function (Request $request) {
        return response()->json([
            'success' => true,
            'message' => 'Fundi API is working!',
            'user' => $request->user()->only(['id', 'full_name', 'email']),
            'timestamp' => now()
        ]);
    });

    // Add these inside the auth:sanctum middleware group
Route::middleware('auth:sanctum')->group(function () {
    
    // Inspection Package routes
    Route::prefix('packages')->group(function () {
        Route::get('/', [InspectionPackageController::class, 'index']);
        Route::get('/comparison', [InspectionPackageController::class, 'getComparison']);
        Route::get('/{id}', [InspectionPackageController::class, 'show']);
        Route::get('/{id}/statistics', [InspectionPackageController::class, 'getStatistics']);
        Route::post('/{id}/calculate-price', [InspectionPackageController::class, 'calculatePrice']);
        
        // Admin only routes
        Route::post('/', [InspectionPackageController::class, 'store']);
        Route::put('/{id}', [InspectionPackageController::class, 'update']);
        Route::put('/{id}/price', [InspectionPackageController::class, 'updatePrice']);
        Route::post('/{id}/services', [InspectionPackageController::class, 'addService']);
        Route::delete('/{id}/services/{serviceId}', [InspectionPackageController::class, 'removeService']);
    });

    // Add these inside the auth:sanctum middleware group
Route::middleware('auth:sanctum')->group(function () {
    
    // Inspector routes
    Route::prefix('inspectors')->group(function () {
        Route::get('/', [InspectorController::class, 'index']);
        Route::post('/', [InspectorController::class, 'store']); // Admin only
        Route::get('/options', [InspectorController::class, 'getOptions']);
        Route::get('/find-best', [InspectorController::class, 'findBestInspector']);
        Route::get('/assignments', [InspectorController::class, 'getAssignments']); // Inspector only
        Route::post('/update-location', [InspectorController::class, 'updateLocation']); // Inspector only
        Route::post('/update-availability', [InspectorController::class, 'updateAvailability']); // Inspector only
        Route::get('/{id}', [InspectorController::class, 'show']);
        Route::put('/{id}', [InspectorController::class, 'update']);
        Route::get('/{id}/statistics', [InspectorController::class, 'getStatistics']);
    });
    
    // ... other existing routes
});
    
    // ... other existing routes
});


// Add these inside the auth:sanctum middleware group
Route::middleware('auth:sanctum')->group(function () {
    
    // Inspection Request routes
    Route::prefix('inspection-requests')->group(function () {
        Route::get('/', [InspectionRequestController::class, 'index']);
        Route::post('/', [InspectionRequestController::class, 'store']);
        Route::get('/dashboard-stats', [InspectionRequestController::class, 'getDashboardStats']);
        Route::get('/{id}', [InspectionRequestController::class, 'show']);
        Route::post('/{id}/assign-inspector', [InspectionRequestController::class, 'assignInspector']);
        Route::post('/{id}/start', [InspectionRequestController::class, 'startInspection']);
        Route::post('/{id}/complete', [InspectionRequestController::class, 'completeInspection']);
        Route::post('/{id}/cancel', [InspectionRequestController::class, 'cancel']);
    });
    
    // ... other existing routes
});
});

// Health check route (public)
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'Fundi API is healthy',
        'version' => '1.0.0',
        'timestamp' => now()
    ]);
});

