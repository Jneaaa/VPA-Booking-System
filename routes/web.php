<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RequisitionFormController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\FacilityController;

Route::middleware('web')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });

    // ----- Public Views ----- //
    Route::view('/facility-catalog', 'public.facility-catalog');
    Route::view('/equipment-catalog', 'public.equipment-catalog');
    Route::view('/reservation-form', 'public.reservation-form');
    Route::view('/equipment-catalog', 'public.equipment-catalog');
    Route::view('/about-equipment', 'public.about-equipment');
    Route::view('/about-services', 'public.about-services');
    Route::view('/about-facilities', 'public.about-facilities');
    Route::view('/user-feedback', 'public.user-feedback');
    Route::view('/official-receipt', 'public.official-receipt');
    Route::view('/index', 'public.index');
    Route::view('/your-bookings', 'public.your-bookings');
    Route::view('/user-payment', 'public.user-payment');
    Route::view('/policies', 'public.policies');

    // ----- Admin Views ----- //
    Route::get('/admin/profile/{adminId}', function ($adminId) {
        return view('admin.admin-profile', ['adminId' => $adminId]);
    });
    Route::view('/admin/add-equipment', 'admin.add-equipment');
    Route::view('/admin/add-facility', 'admin.add-facility');
    Route::view('/admin/admin-page-template', 'admin.admin-page-template');
    Route::view('/admin/admin-roles', 'admin.admin-roles');
    Route::view('/admin/admin-login', 'admin.admin-login');
    Route::view('/admin/admins', 'admin.admins');
    Route::view('/admin/calendar', 'admin.calendar');
    Route::view('/admin/dashboard', 'admin.dashboard');
    Route::view('/admin/manage-equipment', 'admin.manage-equipment');
    Route::view('/admin/manage-facilities', 'admin.manage-facilities');
    Route::get('/admin/edit-equipment', [EquipmentController::class, 'edit'])->name('admin.edit-equipment');
    Route::get('/admin/edit-facility', [FacilityController::class, 'edit'])->name('admin.edit-facility');
    Route::view('/admin/scan-equipment', 'admin.scan-equipment');
    Route::view('/admin/manage-requests', 'admin.manage-requests');
    Route::get('/admin/requisition/{requestId}', function ($requestId) {
        return view('admin.request-view', ['requestId' => $requestId]);
    });

    // ----- Auth Routes ----- //
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    // ----- Requisition Form Submission Routes (used by form views) ----- //
    Route::prefix('requisition')->group(function () {
        // These are only needed in web.php if they’re triggered by HTML form submissions or used directly in Blade views via JS fetch/AJAX.

        Route::post('/save-user-info', [RequisitionFormController::class, 'saveUserInfo']);
        Route::post('/add-item', [RequisitionFormController::class, 'addToForm']);
        Route::post('/remove-item', [RequisitionFormController::class, 'removeFromForm']);
        Route::get('/get-items', [RequisitionFormController::class, 'getItems']);
        Route::get('/calculate-fees', [RequisitionFormController::class, 'calculateFees']);
        Route::post('/check-availability', [RequisitionFormController::class, 'checkAvailability']);
        Route::post('/temp-upload', [RequisitionFormController::class, 'tempUpload']);
        Route::post('/submit', [RequisitionFormController::class, 'submitForm']);
        Route::post('/clear-session', [RequisitionFormController::class, 'clearSession']);
    });

    Route::get('/admin-roles', [AdminController::class, 'adminRoles'])->name('admin.roles');
});