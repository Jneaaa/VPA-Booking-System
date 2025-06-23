<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;

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
Route::post('/admin/login', [AdminAuthController::class, 'login'])
    ->middleware('throttle:5,1'); // 5 attempts per minute


Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->middleware('auth:sanctum');

Route::post('/equipment/{id}/upload-image', [EquipmentController::class, 'uploadImage']);
Route::post('/equipment/{id}/upload-images', [EquipmentController::class, 'uploadMultipleImages']);



// Protected admin routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/profile', [AdminAuthController::class, 'profile']);

     Route::put('/requisitions/{id}', [RequisitionController::class, 'update']);
    Route::patch('/requisitions/{id}', [RequisitionController::class, 'update']); 
    Route::delete('/requisitions/{id}', [RequisitionController::class, 'destroy']);

    // Add other protected routes here
});

