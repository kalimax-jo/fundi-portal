<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InspectionRequestController as UserInspectionRequestController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Authenticated user routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/inspection-requests/create', [UserInspectionRequestController::class, 'create'])->name('inspection-requests.create');
    Route::post('/inspection-requests', [UserInspectionRequestController::class, 'store'])->name('inspection-requests.store');
    
    // Individual client specific routes
    Route::get('/my-requests', [UserInspectionRequestController::class, 'myRequests'])->name('my-requests');
    Route::get('/my-properties', [UserInspectionRequestController::class, 'myProperties'])->name('my-properties');
    Route::get('/profile', [UserInspectionRequestController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserInspectionRequestController::class, 'updateProfile'])->name('profile.update');
    Route::get('/inspection-requests/{inspectionRequest}', [UserInspectionRequestController::class, 'show'])->name('inspection-requests.show');
    Route::get('/inspection-requests/{inspectionRequest}/report/download', [UserInspectionRequestController::class, 'downloadReport'])->name('inspection-requests.report.download');
});

// Admin Routes (Protected by admin middleware)
Route::middleware(['auth', 'role:admin,head_technician'])->prefix('admin')->name('admin.')->group(function () {
    
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
    Route::post('business-partners/{businessPartner}/toggle-status', [App\Http\Controllers\Admin\BusinessPartnerController::class, 'toggleStatus'])->name('business-partners.toggle-status');
    Route::get('business-partners/{businessPartner}/users', [App\Http\Controllers\Admin\BusinessPartnerController::class, 'users'])->name('business-partners.users');
    Route::post('business-partners/{businessPartner}/add-user', [App\Http\Controllers\Admin\BusinessPartnerController::class, 'addUser'])->name('business-partners.add-user');
    
    // Business Partner User Management Routes
    Route::post('business-partners/{businessPartner}/users/{user}/set-primary', [App\Http\Controllers\Admin\BusinessPartnerController::class, 'setPrimaryContact'])->name('business-partners.set-primary-contact');
    Route::delete('business-partners/{businessPartner}/users/{user}/remove', [App\Http\Controllers\Admin\BusinessPartnerController::class, 'removeUser'])->name('business-partners.remove-user');
    Route::patch('business-partners/{businessPartner}/users/{user}/update-access', [App\Http\Controllers\Admin\BusinessPartnerController::class, 'updateUserAccess'])->name('business-partners.update-user-access');
    
    // Inspection Request Management
    Route::resource('inspection-requests', App\Http\Controllers\Admin\InspectionRequestController::class);
    Route::get('inspection-requests-pending', [App\Http\Controllers\Admin\InspectionRequestController::class, 'pending'])->name('inspection-requests.pending');
    Route::get('inspection-requests-assign', [App\Http\Controllers\Admin\InspectionRequestController::class, 'assign'])->name('inspection-requests.assign');
    Route::post('inspection-requests/{request}/assign-inspector', [App\Http\Controllers\Admin\InspectionRequestController::class, 'assignInspector'])->name('inspection-requests.assign-inspector');
    Route::post('inspection-requests/{request}/update-status', [App\Http\Controllers\Admin\InspectionRequestController::class, 'updateStatus'])->name('inspection-requests.update-status');
    
    // Property Management
    Route::resource('properties', App\Http\Controllers\Admin\PropertyController::class);
    Route::post('properties/{property}/verify', [App\Http\Controllers\Admin\PropertyController::class, 'verify'])->name('properties.verify');
    Route::get('properties/{property}/inspection-history', [App\Http\Controllers\Admin\PropertyController::class, 'inspectionHistory'])->name('properties.inspection-history');
    
    // Payment Management
    Route::get('payments', [App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/analytics', [App\Http\Controllers\Admin\PaymentController::class, 'analytics'])->name('payments.analytics');
    Route::get('payments/export', [App\Http\Controllers\Admin\PaymentController::class, 'export'])->name('payments.export');
    Route::get('payments/{payment}', [App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('payments.show');
    Route::post('payments/{payment}/refund', [App\Http\Controllers\Admin\PaymentController::class, 'refund'])->name('payments.refund');
    Route::post('payments/{payment}/mark-completed', [App\Http\Controllers\Admin\PaymentController::class, 'markCompleted'])->name('payments.mark-completed');
    
    // Package & Service Management
    Route::resource('packages', App\Http\Controllers\Admin\PackageController::class);
    Route::post('packages/{package}/toggle-status', [App\Http\Controllers\Admin\PackageController::class, 'toggleStatus'])->name('packages.toggle-status');
    Route::get('packages/analytics', [App\Http\Controllers\Admin\PackageController::class, 'analytics'])->name('packages.analytics');
    Route::post('packages/{package}/services', [App\Http\Controllers\Admin\PackageController::class, 'addService'])->name('packages.add-service');
    Route::delete('packages/{package}/services/{service}', [App\Http\Controllers\Admin\PackageController::class, 'removeService'])->name('packages.remove-service');
    Route::patch('packages/{package}/services/{service}', [App\Http\Controllers\Admin\PackageController::class, 'updateService'])->name('packages.update-service');
    
    Route::resource('services', App\Http\Controllers\Admin\ServiceController::class);
    Route::post('services/{service}/toggle-status', [App\Http\Controllers\Admin\ServiceController::class, 'toggleStatus'])->name('services.toggle-status');
    Route::get('services/analytics', [App\Http\Controllers\Admin\ServiceController::class, 'analytics'])->name('services.analytics');
    Route::get('services/category/{category}', [App\Http\Controllers\Admin\ServiceController::class, 'byCategory'])->name('services.by-category');
    Route::get('services/equipment/{equipment}', [App\Http\Controllers\Admin\ServiceController::class, 'byEquipment'])->name('services.by-equipment');
    Route::post('services/bulk-update', [App\Http\Controllers\Admin\ServiceController::class, 'bulkUpdate'])->name('services.bulk-update');
    
    // Reports & Analytics
    Route::get('analytics/overview', [App\Http\Controllers\Admin\AnalyticsController::class, 'overview'])->name('analytics.overview');
    Route::get('analytics/revenue', [App\Http\Controllers\Admin\AnalyticsController::class, 'revenue'])->name('analytics.revenue');
    Route::get('analytics/inspectors', [App\Http\Controllers\Admin\AnalyticsController::class, 'inspectors'])->name('analytics.inspectors');
    
    Route::get('reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/inspection-requests', [App\Http\Controllers\Admin\ReportController::class, 'inspectionRequests'])->name('reports.inspection-requests');
    Route::post('reports/generate', [App\Http\Controllers\Admin\ReportController::class, 'generate'])->name('reports.generate');
    Route::get('reports/export/{type}', [App\Http\Controllers\Admin\ReportController::class, 'export'])->name('reports.export');
});

// Head Technician Portal Routes (now outside admin group)
Route::middleware(['auth', 'role:head_technician'])->prefix('ht')->name('headtech.')->group(function () {
    Route::get('/', [\App\Http\Controllers\HeadTech\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('inspectors', \App\Http\Controllers\HeadTech\InspectorController::class);
    Route::resource('inspection-requests', \App\Http\Controllers\HeadTech\InspectionRequestController::class);

    // Package Management for Head Tech
    Route::resource('packages', \App\Http\Controllers\HeadTech\PackageController::class);
    Route::get('packages/analytics', [\App\Http\Controllers\HeadTech\PackageController::class, 'analytics'])->name('packages.analytics');
    Route::post('packages/{package}/toggle-status', [\App\Http\Controllers\HeadTech\PackageController::class, 'toggleStatus'])->name('packages.toggle-status');
    Route::post('packages/{package}/services', [\App\Http\Controllers\HeadTech\PackageController::class, 'addService'])->name('packages.add-service');
    Route::delete('packages/{package}/services/{service}', [\App\Http\Controllers\HeadTech\PackageController::class, 'removeService'])->name('packages.remove-service');
    Route::patch('packages/{package}/services/{service}', [\App\Http\Controllers\HeadTech\PackageController::class, 'updateService'])->name('packages.update-service');

    // Service Management for Head Tech
    Route::resource('services', \App\Http\Controllers\HeadTech\ServiceController::class);
    Route::get('services/analytics', [\App\Http\Controllers\HeadTech\ServiceController::class, 'analytics'])->name('services.analytics');
    Route::post('services/{service}/toggle-status', [\App\Http\Controllers\HeadTech\ServiceController::class, 'toggleStatus'])->name('services.toggle-status');

    Route::get('inspection-requests/assign', [\App\Http\Controllers\HeadTech\InspectionRequestController::class, 'assign'])->name('inspection-requests.assign-page');
    Route::post('inspection-requests/{inspectionRequest}/assign', [\App\Http\Controllers\HeadTech\InspectionRequestController::class, 'assignInspector'])->name('inspection-requests.assign');
    Route::post('inspection-requests/{inspectionRequest}/reassign', [\App\Http\Controllers\HeadTech\InspectionRequestController::class, 'reassignInspector'])->name('inspection-requests.reassign');
    // Assignments placeholder
    Route::get('assignments', [\App\Http\Controllers\HeadTech\DashboardController::class, 'assignments'])->name('assignments.index');
    // Diagnostic test route
    Route::get('test-assign', function () {
        return 'HeadTech Assign Test Route Works!';
    });

    // Profile settings
    Route::get('profile', [\App\Http\Controllers\HeadTech\DashboardController::class, 'profile'])->name('profile');
    Route::post('profile', [\App\Http\Controllers\HeadTech\DashboardController::class, 'updateProfile'])->name('profile.update');
    Route::post('password', [\App\Http\Controllers\HeadTech\DashboardController::class, 'updatePassword'])->name('password.update');

    Route::get('/inspection-requests/{inspectionRequest}/report/download', [\App\Http\Controllers\HeadTech\InspectionRequestController::class, 'downloadReport'])->name('inspection-requests.report.download');
});

// Test route for role middleware
Route::middleware(['role:head_technician'])->get('/test-role', function () {
    return 'Role middleware works!';
});

// Inspector Portal
Route::middleware(['auth', 'role:inspector'])->prefix('inspector')->name('inspector.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\InspectorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/assignments', [\App\Http\Controllers\InspectorDashboardController::class, 'assignments'])->name('assignments');
    Route::get('/pending', [\App\Http\Controllers\InspectorDashboardController::class, 'pending'])->name('pending');
    Route::get('/in-progress', [\App\Http\Controllers\InspectorDashboardController::class, 'inprogress'])->name('inprogress');
    Route::get('/complete', [\App\Http\Controllers\InspectorDashboardController::class, 'complete'])->name('complete');
    Route::get('/requests/{id}', [\App\Http\Controllers\InspectorDashboardController::class, 'show'])->name('requests.show');
    Route::post('/requests/{id}/start', [\App\Http\Controllers\InspectorDashboardController::class, 'startInspection'])->name('requests.start');
    Route::get('/requests/{id}/report', [\App\Http\Controllers\Inspector\InspectionReportController::class, 'showForm'])->name('requests.report');
    Route::post('/reports/{id}/autosave', [\App\Http\Controllers\Inspector\InspectionReportController::class, 'autoSave'])->name('reports.autosave');
    Route::post('/reports/{id}/complete', [\App\Http\Controllers\Inspector\InspectionReportController::class, 'complete'])->name('reports.complete');
    Route::put('/reports/{id}/update', [\App\Http\Controllers\Inspector\InspectionReportController::class, 'update'])->name('reports.update');
    Route::get('/reports/{id}/download', [\App\Http\Controllers\Inspector\InspectionReportController::class, 'downloadPdf'])->name('reports.download');

    // Settings
    Route::get('/settings', [\App\Http\Controllers\InspectorDashboardController::class, 'settings'])->name('settings');
    Route::post('/settings/profile', [\App\Http\Controllers\InspectorDashboardController::class, 'updateProfile'])->name('settings.profile.update');
    Route::post('/settings/password', [\App\Http\Controllers\InspectorDashboardController::class, 'updatePassword'])->name('settings.password.update');
    Route::post('/settings/availability', [\App\Http\Controllers\InspectorDashboardController::class, 'toggleAvailability'])->name('settings.availability.toggle');
});