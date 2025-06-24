<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use Illuminate\Support\Facades\Auth;

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

// Public login route
Route::post('/admin/login', [AdminAuthController::class, 'login'])->middleware('throttle:5,1'); // 5 attempts per minute

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

// Protected admin routes
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/admin/profile', [AdminAuthController::class, 'profile']);

    Route::get('/admin/me', function (Request $request) {
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
        Route::get('amenities', [\App\Http\Controllers\Dropdowns\AmenityController::class, 'index']);
    });
});

