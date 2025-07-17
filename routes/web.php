<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RequisitionFormController;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\Dropdowns\FacilityCategoryController;
use App\Http\Controllers\Dropdowns\FacilitySubcategoryController;
use App\Http\Controllers\Dropdowns\EquipmentCategoryController;
use App\Http\Controllers\Dropdowns\DepartmentController;
use App\Http\Controllers\Dropdowns\AvailabilityStatusController;
use App\Http\Controllers\Dropdowns\ConditionController;
use App\Http\Controllers\Dropdowns\RequisitionPurposeController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

// Public views
Route::view('/facility-catalog', 'public.facility-catalog');
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

// Admin views
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
Route::view('/admin/manage-facility', 'admin.manage-facility');
Route::view('/admin/manage-requests', 'admin.manage-requests');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');


Route::prefix('requisition')->group(function () {

    Route::post('/add-item', [RequisitionFormController::class, 'addToForm']);
});

// ----- User Routes ----- //

Route::get('/users', [UserController::class, 'index']);


// ----- Requisition Form Routes ----- //

// Add requisition prefix for all requisition-related routes
Route::prefix('requisition')->group(function () {

    // save user info to session
    Route::post('/save-user-info', [RequisitionFormController::class, 'saveUserInfo']);
    // add equipment or facilities to the requisition form
    Route::post('/add-item', [RequisitionFormController::class, 'addToForm']);
    // remove selected equipment or facilities
    Route::post('/remove-item', [RequisitionFormController::class, 'removeFromForm']);
    // Display fees
    Route::get('/calculate-fees', [RequisitionFormController::class, 'calculateFees']);
    // check availability
    Route::post('/check-availability', [RequisitionFormController::class, 'checkAvailability']);
    // File uploads
    Route::post('/temp-upload', [RequisitionFormController::class, 'tempUpload']);
    // Form submission
    Route::post('/submit', [RequisitionFormController::class, 'submitForm']);
});

// ------ Dropdowns and Categories ------ //

Route::get('/departments', [DepartmentController::class, 'index']);
Route::get('/availability-statuses', [AvailabilityStatusController::class, 'index']);
Route::get('/conditions', [ConditionController::class, 'index']);
Route::get('/equipment-categories', [EquipmentCategoryController::class, 'index']);
Route::get('/facility-categories', [FacilityCategoryController::class, 'index']);
Route::get('/facility-categories/index', [FacilityCategoryController::class, 'indexWithSubcategories']);
Route::get('/facility-subcategories/{category}', [FacilitySubcategoryController::class, 'index']);
Route::get('/requisition-purposes', [RequisitionPurposeController::class, 'index']);

// ---- Public Catalog Routes ---- //

Route::get('/equipment', [EquipmentController::class, 'publicIndex']);
Route::get('/facilities', [FacilityController::class, 'publicIndex']);
Route::get('/facilities/{facility}', [FacilityController::class, 'show']);

// ---- Protected Catalog Routes ---- //

// Equipment
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::get('/equipment', [EquipmentController::class, 'index']);
    Route::post('/equipment', [EquipmentController::class, 'store']);
    Route::get('/equipment/{equipment}', [EquipmentController::class, 'show']);
    Route::put('/equipment/{equipment}', [EquipmentController::class, 'update']);
    Route::delete('/equipment/{equipment}', [EquipmentController::class, 'destroy']);

    // Facilities

    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::get('/admin/facilities', [FacilityController::class, 'index']);
        Route::post('/admin/facilities', [FacilityController::class, 'store']);
        Route::put('/admin/facilities/{facility}', [FacilityController::class, 'update']);
        Route::delete('/admin/facilities/{facility}', [FacilityController::class, 'destroy']);

        // ----- Image management ----- //

        // Equipment
        Route::post('/equipment/{equipmentId}/images/upload', [EquipmentController::class, 'uploadImage']);
        Route::post('/equipment/{equipmentId}/images/bulk-upload', [EquipmentController::class, 'uploadMultipleImages']);
        Route::delete('/equipment/{equipmentId}/images/{imageId}', [EquipmentController::class, 'deleteImage']);
        Route::post('/equipment/{equipmentId}/images/reorder', [EquipmentController::class, 'reorderImages']);
    });

    // Facility
    Route::post('/admin/facilities/{facilityId}/images', [FacilityController::class, 'uploadImage']);
    Route::post('/admin/facilities/{facilityId}/images/bulk', [FacilityController::class, 'uploadMultipleImages']);
    Route::delete('/admin/facilities/{facilityId}/images/{imageId}', [FacilityController::class, 'deleteImage']);
    Route::post('/admin/facilities/{facilityId}/images/reorder', [FacilityController::class, 'reorderImages']);
});


// --------- Admin Image Uploads --------- //

// equipment image uploads
Route::post('/equipment/{id}/upload-image', [EquipmentController::class, 'uploadImage']);

Route::post('/equipment/{id}/upload-images', [EquipmentController::class, 'uploadMultipleImages']);

// ----- Admin Authentication ----- //

// Public login route
Route::post('/admin/login', [AdminAuthController::class, 'login'])
    ->middleware('throttle:5,1'); // 5 attempts per minute

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (!Auth::attempt($credentials)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $user = Auth::user();
    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'token' => $token,
        'user' => $user,
    ]);
});

// Logout route
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->middleware('auth:sanctum');


// ----- Protected admin routes ----- //

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/admin/profile', function (Request $request) {
        $user = $request->user();
        $user->load('departments'); // Ensure departments are included
        return response()->json($user);
    });

    Route::prefix('admin')->middleware(['auth:sanctum', 'check.admin.role'])->group(function () {

        // Equipment Routes
        Route::apiResource('equipment', EquipmentController::class);

        // Facility Routes
        Route::apiResource('facilities', FacilityController::class);

    });
});


