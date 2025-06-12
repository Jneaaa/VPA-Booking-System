<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| These routes are loaded by the RouteServiceProvider and all of them
| will be assigned to the "api" middleware group. Make something great!
|
*/

// Public login route
Route::post('/admin/login', [AdminAuthController::class, 'login']);

//Route::post('/admin/login', function () {
    //return response()->json(['message' => 'It works!']);
//});

Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->middleware('auth:sanctum');

Route::post('/admin/login', [AdminAuthController::class, 'login'])
    ->middleware('throttle:login'); // Uses the named limiter

// Protected admin routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/profile', [AdminAuthController::class, 'profile']);
    // Add other protected routes here
});

