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
Route::view('/bookingcatalog', 'frontend-backup.public.bookingcatalog');
Route::view('/bookingpage', 'frontend-backup.public.bookingpage');
Route::view('/catalog-equipment', 'frontend-backup.public.catalog-equipment');
Route::view('/equipmentcatalog', 'frontend-backup.public.equipmentcatalog');
Route::view('/equipmentpage', 'frontend-backup.public.equipmentpage');
Route::view('/extraservicespage', 'frontend-backup.public.extraservicespage');
Route::view('/facilities', 'frontend-backup.public.facilities');
Route::view('/feedbackpage', 'frontend-backup.public.feedbackpage');
Route::view('/finalreceiptpage', 'frontend-backup.public.finalreceiptpage');
Route::view('/index', 'frontend-backup.public.index');
Route::view('/mybookingpage', 'frontend-backup.public.mybookingpage');
Route::view('/paymentpage', 'frontend-backup.public.paymentpage');
Route::view('/policies', 'frontend-backup.public.policies');

// Admin views
Route::view('/admin/add-equipment', 'frontend-backup.admin.add-equipment');
Route::view('/admin/add-facility', 'frontend-backup.admin.add-facility');
Route::view('/admin/admin-page-template', 'frontend-backup.admin.admin-page-template');
Route::view('/admin/admin-roles-page', 'frontend-backup.admin.admin-roles-page');
Route::view('/admin/adminlogin', 'frontend-backup.admin.adminlogin');
Route::view('/admin/admins', 'frontend-backup.admin.admins');
Route::view('/admin/calendar', 'frontend-backup.admin.calendar');
Route::view('/admin/dashboard', 'frontend-backup.admin.dashboard');
Route::view('/admin/equipment', 'frontend-backup.admin.equipment');
Route::view('/admin/facilities', 'frontend-backup.admin.facilities');
Route::view('/admin/manage-facility', 'frontend-backup.admin.manage-facility');
Route::view('/admin/requisitions', 'frontend-backup.admin.requisitions');

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

// add/remove selected equipment or facilities
Route::post('/remove-item', [RequisitionFormController::class, 'removeFromForm']);
// Display fees
Route::get('/calculate-fees', [RequisitionFormController::class, 'calculateFees']);
// File uploads
Route::post('/temp-upload', [RequisitionFormController::class, 'tempUpload']);
// Form submission
Route::post('/submit', [RequisitionFormController::class, 'submitRequisition']);
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


