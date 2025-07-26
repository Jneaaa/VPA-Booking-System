<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FormStatus;
use Illuminate\Http\JsonResponse;

class FormStatusController extends Controller
{
    public function index(): JsonResponse
    {
        $status = FormStatus::orderBy('status_id')->get(['status_name', 'status_name']);
        return response()->json($status);
    }
}
