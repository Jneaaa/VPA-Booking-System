<?php

namespace App\Http\Controllers\Dropdowns;

use App\Http\Controllers\Controller;
use App\Models\LookupTables\AvailabilityStatus;
use Illuminate\Http\JsonResponse;

class AvailabilityStatusController extends Controller
{
    public function index(): JsonResponse
    {
        $status = AvailabilityStatus::orderBy('status_id')->get(['status_name', 'status_name']);
        return response()->json($status);
    }
}