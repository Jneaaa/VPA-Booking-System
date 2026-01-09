<?php

use App\Http\Controllers\EquipmentItemController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminApprovalController;
use App\Http\Controllers\RequisitionFormController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminCommentsController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\ScannerController;
use App\Http\Controllers\NotificationController;

use App\Http\Controllers\Dropdowns\FacilityCategoryController;
use App\Http\Controllers\Dropdowns\FacilitySubcategoryController;
use App\Http\Controllers\Dropdowns\EquipmentCategoryController;
use App\Http\Controllers\Dropdowns\DepartmentController;
use App\Http\Controllers\Dropdowns\AvailabilityStatusController;
use App\Http\Controllers\FormStatusController;
use App\Http\Controllers\Dropdowns\ConditionController;
use App\Http\Controllers\Dropdowns\RequisitionPurposeController;

// ==================== PUBLIC ROUTES ==================== //

// ---------------- Authentication ---------------- //
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

// ---------------- Public Data ---------------- //
Route::get('/equipment', [EquipmentController::class, 'publicIndex']);
Route::get('/facilities', [FacilityController::class, 'publicIndex']);
Route::get('/requisition-forms/calendar-events', [RequisitionFormController::class, 'getCalendarEvents']);

// ---------------- Lookup Tables ---------------- //
Route::get('/departments', [DepartmentController::class, 'index']);
Route::get('/availability-statuses', [AvailabilityStatusController::class, 'index']);
Route::get('/form-statuses', [FormStatusController::class, 'index']);
Route::get('/conditions', [ConditionController::class, 'index']);
Route::get('/equipment/{id}', [EquipmentController::class, 'show']);
Route::get('/admin/facilities/{id}', [FacilityController::class, 'show']);
Route::get('/equipment-categories', [EquipmentCategoryController::class, 'index']);
Route::get('/equipment-items', [EquipmentItemController::class, 'index']);
Route::get('/facility-categories', [FacilityCategoryController::class, 'index']);
Route::get('/facility-categories/index', [FacilityCategoryController::class, 'indexWithSubcategories']);
Route::get('/facility-subcategories/{category}', [FacilitySubcategoryController::class, 'index']);
Route::get('/requisition-purposes', [RequisitionPurposeController::class, 'index']);
Route::get('/active-schedules', [RequisitionFormController::class, 'activeSchedules']);
Route::get('/admin-role', [AdminController::class, 'adminRoles']);

// ---------------- Requisition Forms ---------------- //
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

// ---------------- Requester Routes ---------------- //
Route::prefix('requester')->group(function () {
    Route::get('/form/{accessCode}', [AdminApprovalController::class, 'getFormByAccessCode']);
    Route::post('/{requestId}/proof-of-payment', [AdminApprovalController::class, 'uploadProofOfPayment']);
    Route::get('/{requestId}/receipt', [AdminApprovalController::class, 'getOfficialReceipt']);
});

// ---------------- Public Actions ---------------- //
Route::post('/feedback', [FeedbackController::class, 'store']);
Route::post('/requester/requisition/{requestId}/cancel', [AdminApprovalController::class, 'cancelRequestPublic']);
Route::post('/requester/requisition/{requestId}/upload-receipt', [AdminApprovalController::class, 'uploadPaymentReceipt']);

// ---------------- Scanner Routes ---------------- //
Route::prefix('scanner')->group(function () {
    Route::post('/scan', [ScannerController::class, 'scan']);
    Route::post('/borrow', [ScannerController::class, 'borrow']);
    Route::post('/return', [ScannerController::class, 'return']);
    Route::put('/update-item/{itemId}', [ScannerController::class, 'updateItem']);
});

// ---------------- Barcode Generation ---------------- //
Route::post('/admin/generate-barcode', function (Request $request) {
    try {
        $equipmentId = $request->input('equipment_id');
        $itemId = $request->input('item_id');

        $barcodeValue = \App\Services\BarcodeService::generateEquipmentBarcode($equipmentId, $itemId);

        return response()->json([
            'status' => 'success',
            'barcode' => $barcodeValue
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to generate barcode: ' . $e->getMessage()
        ], 500);
    }
});

// ---------------- Test Route ---------------- //
Route::get('/test-email', function () {
    try {
        \Mail::raw('Test email', function ($message) {
            $message->to('test@example.com')->subject('Test Email');
        });
        return 'Email sent successfully!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

// ==================== PROTECTED ROUTES (auth:sanctum) ==================== //

Route::middleware('auth:sanctum')->group(function () {

    // ---------------- Admin Management ---------------- //
    Route::get('/admins', [AdminController::class, 'getAllAdmins']);
    Route::get('/admins/{admin}', [AdminController::class, 'getAdminInfo']);
    Route::post('/admins', [AdminController::class, 'store']);
    Route::delete('/admins/{admin}', [AdminController::class, 'deleteAdmin']);
    Route::put('/admins/{admin}', [AdminController::class, 'update']);
    Route::post('/admin/update/{admin}', [AdminController::class, 'update']);
    Route::post('/admin/update-photo', [AdminController::class, 'updatePhoto']);
    Route::post('/admin/update-photo-records', [AdminController::class, 'updatePhotoRecords']);
    Route::post('/admin/delete-cloudinary-image', [AdminController::class, 'deleteCloudinaryImage']);

    // ---------------- Admin Profile & Notifications ---------------- //
    Route::get('/admin/profile', function (Request $request) {
        $user = $request->user();
        $user->load(['role', 'departments']);
        return response()->json($user);
    });
    Route::get('/admin/notifications', [NotificationController::class, 'getNotifications']);
    Route::post('/admin/notifications/mark-read/{notificationId?}', [NotificationController::class, 'markAsRead']);
    Route::post('/admin/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::get('/feedback', [FeedbackController::class, 'index']);
    Route::post('/admin/notifications/requisition/{requisitionId}/mark-as-read', [NotificationController::class, 'markRequisitionAsRead']);

    // ---------------- Equipment Management ---------------- //
    Route::post('admin/equipment', [EquipmentController::class, 'store']);
    Route::put('admin/equipment/{equipmentId}', [EquipmentController::class, 'update']);
    Route::delete('/admin/equipment/{equipmentId}', [EquipmentController::class, 'destroy']);

    // Equipment Images
    Route::post('/admin/upload', [EquipmentController::class, 'uploadImage']);
    Route::post('/admin/bulk-upload', [EquipmentController::class, 'uploadMultipleImages']);
    Route::delete('/admin/equipment/{equipmentId}/images/{imageId}', [EquipmentController::class, 'deleteImage']);
    Route::post('/admin/reorder', [EquipmentController::class, 'reorderImages']);
    Route::post('/admin/equipment/{equipmentId}/images/save', [EquipmentController::class, 'saveImageReference']);

    // Equipment Items
    Route::get('admin/equipment/{equipmentId}/items', [EquipmentController::class, 'getItems']);
    Route::post('admin/equipment/{equipmentId}/items', [EquipmentController::class, 'storeItem']);
    Route::put('admin/equipment/{equipmentId}/items/{itemId}', [EquipmentController::class, 'updateItem']);
    Route::delete('admin/equipment/{equipmentId}/items/{itemId}', [EquipmentController::class, 'deleteItem']);

    // ---------------- Facility Management ---------------- //
    Route::post('admin/add-facility', [FacilityController::class, 'store']);
    Route::put('admin/facilities/{facilityId}', [FacilityController::class, 'update']);
    Route::delete('/admin/facilities/{facilityId}', [FacilityController::class, 'destroy']);
    Route::get('facilities/get-categories', [FacilityController::class, 'create']);

    // Facility Images
    Route::post('/admin/upload', [FacilityController::class, 'uploadImage']);
    Route::post('/admin/bulk-upload', [FacilityController::class, 'uploadMultipleImages']);
    Route::delete('/admin/facilities/{facilityId}/images/{imageId}', [FacilityController::class, 'deleteImage']);
    Route::post('/admin/reorder', [FacilityController::class, 'reorderImages']);
    Route::post('/admin/facilities/{facilityId}/images/save', [FacilityController::class, 'saveImageReference']);

    // ---------------- Requisition Management ---------------- //
    Route::get('/admin/requisition-forms', [AdminApprovalController::class, 'pendingRequests']);
    Route::get('/admin/requisition-forms/{requestId}', [AdminApprovalController::class, 'getRequisitionFormById']);
    Route::put('admin/requisition-forms/{requestId}/calendar-info', [AdminApprovalController::class, 'updateCalendarInfo']);
    Route::post('/admin/requisition-forms', [AdminApprovalController::class, 'createReservation']);
    Route::get('/admin/simplified-forms', [AdminApprovalController::class, 'getSimplifiedForms']);
    Route::get('/admin/completed-requests', [AdminApprovalController::class, 'completedRequests']);
    Route::get('/admin/archives', [RequisitionFormController::class, 'getArchivedRequisitions']);
    Route::post('/admin/requisition/{requestId}/mark-scheduled', [AdminApprovalController::class, 'markAsScheduled']);
    Route::get('/admin/requisition/{requestId}/approval-history', [AdminApprovalController::class, 'getApprovalHistory']);
    Route::get('/admin/requisition/{requestId}/equipment-status', [AdminApprovalController::class, 'getEquipmentStatus']);

    // Form Management
    Route::prefix('admin/requisition')->group(function () {
        // Fees & Payments
        Route::post('/{requestId}/fee', [AdminApprovalController::class, 'addFee']);
        Route::post('/{requestId}/discount', [AdminApprovalController::class, 'addDiscount']);
        Route::post('/{requestId}/late-penalty', [AdminApprovalController::class, 'addLatePenalty']);
        Route::post('/{requestId}/remove-late-penalty', [AdminApprovalController::class, 'removeLatePenalty']);
        Route::delete('/{requestId}/fee/{feeId}', [AdminApprovalController::class, 'removeFee']);
        Route::get('/{requestId}/fees', [AdminApprovalController::class, 'getRequisitionFees']);
        Route::post('/{requestId}/waive', [AdminApprovalController::class, 'waiveItems']);

        // Status Management
        Route::post('/{requestId}/update-status', [AdminApprovalController::class, 'updateStatus']);
        Route::post('/{requestId}/approve', [AdminApprovalController::class, 'approveRequest']);
        Route::post('/{requestId}/reject', [AdminApprovalController::class, 'rejectRequest']);
        Route::post('{requestId}/cancel', [AdminApprovalController::class, 'cancelForm']);
        Route::post('/{requestId}/finalize', [AdminApprovalController::class, 'finalizeForm']);
        Route::post('/{requestId}/close', [AdminApprovalController::class, 'closeForm']);
        Route::post('/{requestId}/mark-returned', [AdminApprovalController::class, 'markReturned']);

        // Automatic status update routes
        Route::post('/admin/auto-mark-ongoing', [AdminApprovalController::class, 'autoMarkOngoingForms']);
        Route::post('/admin/auto-mark-late', [AdminApprovalController::class, 'autoMarkLateForms']);
        Route::post('/admin/auto-update-all', [AdminApprovalController::class, 'autoUpdateAllStatuses']);

        // Comments
        Route::post('/{requestId}/comment', [AdminCommentsController::class, 'addComment']);
        Route::get('/{requestId}/comments', [AdminCommentsController::class, 'getComments']);

        // Receipt
        Route::get('/{requestId}/receipt', [AdminApprovalController::class, 'getOfficialReceipt']);
    });

    // ---------------- Cloudinary Management ---------------- //
    
    Route::post('/admin/cloudinary/delete', function (Request $request) {
        try {
            $request->validate([
                'public_id' => 'required|string'
            ]);

            $publicId = $request->public_id;

            \Log::info('Attempting Cloudinary delete', ['public_id' => $publicId]);

            $cloudinary = new \Cloudinary\Cloudinary([
                'cloud' => [
                    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                    'api_key' => env('CLOUDINARY_API_KEY'),
                    'api_secret' => env('CLOUDINARY_API_SECRET'),
                ],
            ]);

            $result = $cloudinary->adminApi()->deleteAssets($publicId, [
                'resource_type' => 'image',
                'type' => 'upload'
            ]);

            \Log::info('Cloudinary delete successful', [
                'public_id' => $publicId,
                'result' => json_encode($result)
            ]);

            return response()->json([
                'message' => 'Image deleted from Cloudinary',
                'result' => $result
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Cloudinary delete validation error', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'message' => 'Validation failed',
                'error' => $e->getMessage()
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Cloudinary delete error', [
                'error' => $e->getMessage(),
                'public_id' => $request->public_id ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Failed to delete image',
                'error' => $e->getMessage()
            ], 500);
        }
    });

    // ---------------- Logout ---------------- //
    Route::post('/admin/logout', [AdminAuthController::class, 'logout']);
});