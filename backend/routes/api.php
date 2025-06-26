<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // Restored this line
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RequisitionController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\Dropdowns\RateTypeController;
use App\Http\Controllers\Dropdowns\ImageTypeController;
use App\Http\Controllers\Dropdowns\FacilityCategoryController;
use App\Http\Controllers\Dropdowns\FacilitySubcategoryController;
use App\Http\Controllers\Dropdowns\EquipmentCategoryController;
use App\Http\Controllers\Dropdowns\DepartmentController;
use App\Http\Controllers\Dropdowns\ConditionController;
use App\Http\Controllers\Dropdowns\AvailabilityStatusController;
use App\Http\Controllers\RequisitionFormController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for our application.
| These routes are loaded by the RouteServiceProvider and all of them
| will be assigned to the "api" middleware group. 
|
*/


// User Info
Route::post('/users/store-or-fetch', [UserController::class, 'storeOrFetch']);
Route::get('/users', [UserController::class, 'index']); // list all users
Route::get('/users/{user_id}/with-requisitions', [UserController::class, 'showWithRequisitions']);
Route::get('/users/search', [UserController::class, 'search']);

// Requisition Form Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/requisition', [RequisitionFormController::class, 'store']);
    Route::get('/requisition/{id}', [RequisitionFormController::class, 'show']);
    Route::get('/requisitions', [RequisitionFormController::class, 'index']); // (Optional: show all for user or admin)
});


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

// Public routes for equipment
Route::post('/equipment/{id}/upload-image', [\App\Http\Controllers\EquipmentController::class, 'uploadImage']);
Route::post('/equipment/{id}/upload-images', [\App\Http\Controllers\EquipmentController::class, 'uploadMultipleImages']);

// Public routes for facilities and categories
Route::get('/facility-categories', [\App\Http\Controllers\Dropdowns\FacilityCategoryController::class, 'index']);
Route::get('/facility-subcategories/{category}', [\App\Http\Controllers\Dropdowns\FacilitySubcategoryController::class, 'index']);
Route::get('/facilities', [\App\Http\Controllers\FacilityController::class, 'publicIndex']);
Route::get('/equipment', [\App\Http\Controllers\EquipmentController::class, 'publicIndex']);

// Requisition Uploads 
Route::post('/requisitions/temp-upload', [RequisitionController::class, 'tempUpload']);
Route::post('/requisitions/finalize', [RequisitionController::class, 'finalizeRequisition']);

// Dropdown routes
Route::get('/dropdowns/rate-types', [RateTypeController::class, 'index']);
Route::get('/dropdowns/image-types', [ImageTypeController::class, 'index']);
Route::get('/dropdowns/facility-categories', [FacilityCategoryController::class, 'index']);
Route::get('/dropdowns/facility-subcategories/{category}', [FacilitySubcategoryController::class, 'index']);
Route::get('/dropdowns/equipment-categories', [EquipmentCategoryController::class, 'index']);
Route::get('/dropdowns/departments', [DepartmentController::class, 'index']);
Route::get('/dropdowns/conditions', [ConditionController::class, 'index']);
Route::get('/dropdowns/availability-statuses', [AvailabilityStatusController::class, 'index']);

// Protected admin routes
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/admin/profile', function (Request $request) {
        $user = $request->user();
        $user->load('departments'); // Ensure departments are included
        return response()->json($user);
    });

    Route::prefix('admin')->middleware(['auth:sanctum', 'check.admin.role'])->group(function () {
        
        // Equipment Routes
        Route::apiResource('equipment', \App\Http\Controllers\EquipmentController::class);

        // Facility Routes
        Route::apiResource('facilities', \App\Http\Controllers\FacilityController::class);

        // Additional routes for dropdowns and selections
        Route::get('equipment-categories', [\App\Http\Controllers\Dropdowns\EquipmentCategoryController::class, 'index']);
        Route::get('facility-categories', [\App\Http\Controllers\Dropdowns\FacilityCategoryController::class, 'index']);
        Route::get('facility-subcategories/{category}', [\App\Http\Controllers\Dropdowns\FacilitySubcategoryController::class, 'index']);
        Route::get('departments', [\App\Http\Controllers\Dropdowns\DepartmentController::class, 'index']);
        Route::get('rate-types', [\App\Http\Controllers\Dropdowns\RateTypeController::class, 'index']);
        Route::get('availability-statuses', [\App\Http\Controllers\Dropdowns\AvailabilityStatusController::class, 'index']);
        Route::get('conditions', [\App\Http\Controllers\Dropdowns\ConditionController::class, 'index']);
        Route::get('image-types', [\App\Http\Controllers\Dropdowns\ImageTypeController::class, 'index']);
    });

    Route::get('/admin/departments/{admin}', [\App\Http\Controllers\AdminController::class, 'getAdminDepartments']);
    Route::post('/admin/departments/{admin}/assign', [\App\Http\Controllers\AdminController::class, 'assignDepartment']);
});

