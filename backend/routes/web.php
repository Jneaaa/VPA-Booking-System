<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RequisitionFormController; 

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/requisition/add-item', [RequisitionFormController::class, 'addToForm']); 