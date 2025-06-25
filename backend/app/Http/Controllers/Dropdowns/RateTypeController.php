<?php

namespace App\Http\Controllers\Dropdowns;

use App\Http\Controllers\Controller;
use App\Models\LookupTables\RateType;

class RateTypeController extends Controller
{
    public function index()
    {
        return response()->json(RateType::all());
    }
}
