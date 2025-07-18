<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RequisitionFormController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\Dropdowns\FacilityCategoryController;
use App\Http\Controllers\Dropdowns\FacilitySubcategoryController;
use App\Http\Controllers\Dropdowns\EquipmentCategoryController;
use App\Http\Controllers\Dropdowns\DepartmentController;
use App\Http\Controllers\Dropdowns\AvailabilityStatusController;
use App\Http\Controllers\Dropdowns\ConditionController;
use App\Http\Controllers\Dropdowns\RequisitionPurposeController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;


// ----- User Routes ----- //

Route::get('/users', [UserController::class, 'index']);
// Admin Routes
Route::get('/admins', [AdminController::class, 'getAllAdmins']); // Get all admins
Route::get('/admins/{admin}', [AdminController::class, 'getAdminInfo']); // Get single admin info


// ----- Requisition Form Routes ----- //

// Add requisition prefix for all requisition-related routes
Route::prefix('requisition')->group(function () {

    // Save user information
    Route::post('/save-user-info', [RequisitionFormController::class, 'saveUserInfo']);
    // Add items to form
    Route::post('/add-item', [RequisitionFormController::class, 'addToForm']);
    // Remove items from form
    Route::post('/remove-item', [RequisitionFormController::class, 'removeFromForm']);
    // Display fees
    Route::get('/calculate-fees', [RequisitionFormController::class, 'calculateFees']);
    // Check availability
    Route::post('/check-availability', [RequisitionFormController::class, 'checkAvailability']);
    // File uploads
    Route::post('/temp-upload', [RequisitionFormController::class, 'tempUpload']);
    // Form submission
    Route::post('/submit', [RequisitionFormController::class, 'submitForm']);
    // View requisition
    Route::get('/{request_id}', [RequisitionFormController::class, 'show']);
    
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

