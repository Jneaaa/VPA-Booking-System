<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminApprovalController;
use App\Http\Controllers\RequisitionFormController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\EquipmentItem;
use App\Http\Controllers\AdminController;

use App\Http\Controllers\Dropdowns\FacilityCategoryController;
use App\Http\Controllers\Dropdowns\FacilitySubcategoryController;
use App\Http\Controllers\Dropdowns\EquipmentCategoryController;
use App\Http\Controllers\Dropdowns\DepartmentController;
use App\Http\Controllers\Dropdowns\AvailabilityStatusController;
use App\Http\Controllers\FormStatusController;
use App\Http\Controllers\Dropdowns\ConditionController;
use App\Http\Controllers\Dropdowns\RequisitionPurposeController;


// ---------------- Admin Routes ---------------- //

Route::get('/admins', [AdminController::class, 'getAllAdmins']);
Route::get('/admins/{admin}', [AdminController::class, 'getAdminInfo']);

// ---------------- Admin Management ---------------- //

Route::post('/admins', [AdminController::class, 'store']);
Route::delete('/admins/{admin}', [AdminController::class, 'deleteAdmin']);
Route::get('/admins/{admin}', [AdminController::class, 'getAdminInfo']);
Route::put('/admins/{admin}', [AdminController::class, 'update']);

// ---------------- Lookup Tables ---------------- //

Route::get('/departments', [DepartmentController::class, 'index']);
Route::get('/availability-statuses', [AvailabilityStatusController::class, 'index']);
Route::get('/form-statuses', [FormStatusController::class, 'index']);
Route::get('/conditions', [ConditionController::class, 'index']);
Route::get('/equipment-categories', [EquipmentCategoryController::class, 'index']);
Route::get('/equipment-items', [EquipmentItem::class, 'index']);
Route::get('/facility-categories', [FacilityCategoryController::class, 'index']);
Route::get('/facility-categories/index', [FacilityCategoryController::class, 'indexWithSubcategories']);
Route::get('/facility-subcategories/{category}', [FacilitySubcategoryController::class, 'index']);
Route::get('/requisition-purposes', [RequisitionPurposeController::class, 'index']);
Route::get('/active-schedules', [RequisitionFormController::class, 'activeSchedules']);
Route::get('/admin-role', [AdminController::class, 'adminRoles']);

// ---------------- Public Catalog Routes ---------------- //

Route::get('/equipment', [EquipmentController::class, 'publicIndex']);
Route::get('/facilities', [FacilityController::class, 'publicIndex']);
Route::get('/facilities/{facility}', [FacilityController::class, 'show']);

// ---------------- Requisition Form Routes ---------------- //

Route::prefix('requisition')->middleware('web')->group(function () {
    Route::post('/save-request-info', [RequisitionFormController::class, 'saveRequestInfo']);
    Route::post('/add-item', [RequisitionFormController::class, 'addToForm']);
    Route::post('/remove-item', [RequisitionFormController::class, 'removeFromForm']);
    Route::get('/get-items', [RequisitionFormController::class, 'getItems']);
    Route::get('/calculate-fees', [RequisitionFormController::class, 'calculateFees']);
    Route::post('/check-availability', [RequisitionFormController::class, 'checkAvailability']);
    Route::post('/temp-upload', [RequisitionFormController::class, 'tempUpload']);
    Route::post('/submit', [RequisitionFormController::class, 'submitForm']);
    Route::post('/clear-session', [RequisitionFormController::class, 'clearSession']);
});

// ---------------- Admin Authentication ---------------- //

Route::post('/admin/login', [AdminAuthController::class, 'login'])->middleware('throttle:5,1');

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

Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->middleware('auth:sanctum');

// ---------------- Protected Admin Routes ---------------- //

Route::middleware('auth:sanctum')->group(function () {

    // ---- Admin Approval Routes ---- //
    Route::get('/admin/requisition-forms', [AdminApprovalController::class, 'pendingRequests']);
    Route::get('/admin/simplified-forms', [AdminApprovalController::class, 'getSimplifiedForms']);
    Route::get('/admin/completed-requests', [AdminApprovalController::class, 'completedRequests']);
    Route::post('/admin/approve-request', [AdminApprovalController::class, 'approveRequest']);
    Route::post('/admin/reject-request', [AdminApprovalController::class, 'rejectRequest']);
    Route::put('/admin/manage-waivers/{requestId}', [AdminApprovalController::class, 'manageWaivers'])
    ->middleware('auth:sanctum');




    Route::get('/admin/profile', function (Request $request) {
        $user = $request->user();
        $user->load(['role', 'departments']);
        return response()->json($user);
    });

    Route::post('/admin/update-photo', [AdminController::class, 'updatePhoto']);

    Route::prefix('admin')->middleware(['check.admin.role'])->group(function () {


        // Equipment
        Route::apiResource('equipment', EquipmentController::class);

        // Facilities
        Route::apiResource('facilities', FacilityController::class);

        // Equipment Image Management
        Route::prefix('equipment/{equipmentId}/images')->group(function () {
            Route::post('/upload', [EquipmentController::class, 'uploadImage']);
            Route::post('/bulk-upload', [EquipmentController::class, 'uploadMultipleImages']);
            Route::delete('/{imageId}', [EquipmentController::class, 'deleteImage']);
            Route::post('/reorder', [EquipmentController::class, 'reorderImages']);
        });

        // Facility Image Management
        Route::prefix('facilities/{facilityId}/images')->group(function () {
            Route::post('/', [FacilityController::class, 'uploadImage']);
            Route::post('/bulk', [FacilityController::class, 'uploadMultipleImages']);
            Route::delete('/{imageId}', [FacilityController::class, 'deleteImage']);
            Route::post('/reorder', [FacilityController::class, 'reorderImages']);
        });
    });

});

// All RequisitionFormController API routes are already present.
