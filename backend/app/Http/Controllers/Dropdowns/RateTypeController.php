<?php

namespace App\Http\Controllers\Dropdowns;

use App\Http\Controllers\Controller;
use App\Models\RateType;

class RateTypeController extends Controller
{
    public function index()
    {
        return response()->json(RateType::where('is_active', true)->get());
    }
}
