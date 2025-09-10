<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FormStatus;
use Illuminate\Http\JsonResponse;

class FormStatusController extends Controller
{
    public function index(): JsonResponse
    {
        $status = FormStatus::orderBy('status_id')->get(['status_id', 'status_name', 'color_code']);
        return response()->json($status);
    }
}
