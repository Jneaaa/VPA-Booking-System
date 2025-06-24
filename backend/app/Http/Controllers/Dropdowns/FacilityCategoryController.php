<?php

namespace App\Http\Controllers\Dropdowns;

use App\Http\Controllers\Controller;
use App\Models\FacilityCategory;

class FacilityCategoryController extends Controller
{
    public function index()
    {
        return response()->json(FacilityCategory::where('is_active', true)->get());
    }
}
