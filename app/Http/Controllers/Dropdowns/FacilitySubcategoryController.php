<?php

namespace App\Http\Controllers\Dropdowns;

use App\Http\Controllers\Controller;
use App\Models\LookupTables\FacilitySubcategory;

class FacilitySubcategoryController extends Controller
{
    public function index($category)
    {
        return response()->json(FacilitySubcategory::all());
    }
}
