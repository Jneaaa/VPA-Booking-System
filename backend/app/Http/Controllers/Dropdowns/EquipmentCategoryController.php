<?php

namespace App\Http\Controllers\Dropdowns;

use App\Http\Controllers\Controller;
use App\Models\LookupTables\EquipmentCategory;

class EquipmentCategoryController extends Controller
{
    public function index()
    {
        return response()->json(EquipmentCategory::where('is_active', true)->get());
    }
}
