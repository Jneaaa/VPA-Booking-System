<?php

namespace App\Http\Controllers\Dropdowns;

use App\Http\Controllers\Controller;
use App\Models\LookupTables\AvailabilityStatus;

class AvailabilityStatusController extends Controller
{
    public function index()
    {
        return response()->json(AvailabilityStatus::where('is_active', true)->get());
    }
}
