<?php

namespace App\Http\Controllers\Dropdowns;

use App\Http\Controllers\Controller;
use App\Models\LookupTables\FacilityCategory;

class FacilityCategoryController extends Controller
{
    public function index()
    {
        return response()->json(FacilityCategory::all());
    }

    public function indexWithSubcategories()
    {
        $categories = FacilityCategory::with('subcategories')->get();

        return response()->json($categories);
    }
}
