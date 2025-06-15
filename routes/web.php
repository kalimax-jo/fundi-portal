<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Basic dashboard (your existing one)
Route::view('/dashboard', 'dashboard')->middleware('auth')->name('dashboard');

// Admin Routes (Protected by admin middleware)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Admin Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');
    
    // User Management
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::post('users/{user}/toggle-status', [App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('users/{user}/assign-role', [App\Http\Controllers\Admin\UserController::class, 'assignRole'])->name('users.assign-role');
    Route::delete('users/{user}/remove-role/{role}', [App\Http\Controllers\Admin\UserController::class, 'removeRole'])->name('users.remove-role');
    
    // Add other admin routes here as we build them...
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Admin Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');
    
    // User Management
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::post('users/{user}/toggle-status', [App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('users/{user}/assign-role', [App\Http\Controllers\Admin\UserController::class, 'assignRole'])->name('users.assign-role');
    Route::delete('users/{user}/remove-role/{role}', [App\Http\Controllers\Admin\UserController::class, 'removeRole'])->name('users.remove-role');
    
    // Role Management
    Route::resource('roles', App\Http\Controllers\Admin\RoleController::class);
    Route::post('roles/{role}/assign-permission', [App\Http\Controllers\Admin\RoleController::class, 'assignPermission'])->name('roles.assign-permission');
    Route::delete('roles/{role}/remove-permission/{permission}', [App\Http\Controllers\Admin\RoleController::class, 'removePermission'])->name('roles.remove-permission');
    
    // Inspector Management
    Route::resource('inspectors', App\Http\Controllers\Admin\InspectorController::class);
    Route::get('inspectors/{inspector}/assignments', [App\Http\Controllers\Admin\InspectorController::class, 'assignments'])->name('inspectors.assignments');
    Route::post('inspectors/{inspector}/toggle-availability', [App\Http\Controllers\Admin\InspectorController::class, 'toggleAvailability'])->name('inspectors.toggle-availability');
    Route::get('inspectors-assignments', [App\Http\Controllers\Admin\InspectorController::class, 'assignmentsOverview'])->name('inspectors.assignments');
    
    // Business Partner Management
    Route::resource('business-partners', App\Http\Controllers\Admin\BusinessPartnerController::class);
    Route::post('business-partners/{partner}/toggle-status', [App\Http\Controllers\Admin\BusinessPartnerController::class, 'toggleStatus'])->name('business-partners.toggle-status');
    Route::get('business-partners/{partner}/users', [App\Http\Controllers\Admin\BusinessPartnerController::class, 'users'])->name('business-partners.users');
    Route::post('business-partners/{partner}/add-user', [App\Http\Controllers\Admin\BusinessPartnerController::class, 'addUser'])->name('business-partners.add-user');
    
    // Inspection Request Management
    Route::resource('inspection-requests', App\Http\Controllers\Admin\InspectionRequestController::class);
    Route::get('inspection-requests-pending', [App\Http\Controllers\Admin\InspectionRequestController::class, 'pending'])->name('inspection-requests.pending');
    Route::get('inspection-requests-assign', [App\Http\Controllers\Admin\InspectionRequestController::class, 'assignInspectors'])->name('inspection-requests.assign');
    Route::post('inspection-requests/{request}/assign-inspector', [App\Http\Controllers\Admin\InspectionRequestController::class, 'assignInspector'])->name('inspection-requests.assign-inspector');
    Route::post('inspection-requests/{request}/update-status', [App\Http\Controllers\Admin\InspectionRequestController::class, 'updateStatus'])->name('inspection-requests.update-status');
    
    // Property Management
    Route::resource('properties', App\Http\Controllers\Admin\PropertyController::class);
    Route::post('properties/{property}/verify', [App\Http\Controllers\Admin\PropertyController::class, 'verify'])->name('properties.verify');
    Route::get('properties/{property}/inspection-history', [App\Http\Controllers\Admin\PropertyController::class, 'inspectionHistory'])->name('properties.inspection-history');
    
    // Payment Management
    Route::get('payments', [App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{payment}', [App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('payments.show');
    Route::post('payments/{payment}/refund', [App\Http\Controllers\Admin\PaymentController::class, 'refund'])->name('payments.refund');
    Route::post('payments/{payment}/mark-completed', [App\Http\Controllers\Admin\PaymentController::class, 'markCompleted'])->name('payments.mark-completed');
    
    // Package & Service Management
    Route::resource('packages', App\Http\Controllers\Admin\PackageController::class);
    Route::post('packages/{package}/toggle-status', [App\Http\Controllers\Admin\PackageController::class, 'toggleStatus'])->name('packages.toggle-status');
    Route::resource('services', App\Http\Controllers\Admin\ServiceController::class);
    Route::post('services/{service}/toggle-status', [App\Http\Controllers\Admin\ServiceController::class, 'toggleStatus'])->name('services.toggle-status');
    
    // Reports & Analytics
    Route::get('analytics/overview', [App\Http\Controllers\Admin\AnalyticsController::class, 'overview'])->name('analytics.overview');
    Route::get('analytics/revenue', [App\Http\Controllers\Admin\AnalyticsController::class, 'revenue'])->name('analytics.revenue');
    Route::get('analytics/inspectors', [App\Http\Controllers\Admin\AnalyticsController::class, 'inspectors'])->name('analytics.inspectors');
    
    Route::get('reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::post('reports/generate', [App\Http\Controllers\Admin\ReportController::class, 'generate'])->name('reports.generate');
    Route::get('reports/export/{type}', [App\Http\Controllers\Admin\ReportController::class, 'export'])->name('reports.export');
    
    // System Settings
    Route::get('settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    
    // Profile Management
    Route::get('profile', [App\Http\Controllers\Admin\ProfileController::class, 'show'])->name('profile');
    Route::put('profile', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');

      Route::prefix('assignments')->name('assignments.')->group(function () {
        // Assignment workflow interface
        Route::get('/', [App\Http\Controllers\Admin\AssignmentController::class, 'index'])->name('index');
        
        // Manual assignment
        Route::post('/assign', [App\Http\Controllers\Admin\AssignmentController::class, 'assign'])->name('assign');
        
        // Auto-assignment
        Route::post('/auto-assign', [App\Http\Controllers\Admin\AssignmentController::class, 'autoAssign'])->name('auto-assign');
        
        // Assignment management
        Route::post('/unassign', [App\Http\Controllers\Admin\AssignmentController::class, 'unassign'])->name('unassign');
        Route::post('/reassign', [App\Http\Controllers\Admin\AssignmentController::class, 'reassign'])->name('reassign');
        
        // Assignment statistics
        Route::get('/statistics', [App\Http\Controllers\Admin\AssignmentController::class, 'statistics'])->name('statistics');
    });
});
