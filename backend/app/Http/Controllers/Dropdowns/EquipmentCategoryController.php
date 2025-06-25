<?php

namespace App\Http\Controllers\Dropdowns;

use App\Http\Controllers\Controller;
use App\Models\LookupTables\EquipmentCategory;
use Illuminate\Http\JsonResponse;

class EquipmentCategoryController extends Controller
{
    public function index()
    {
        $categories = EquipmentCategory::orderBy('category_name')->get(['category_id', 'category_name']);
        return response()->json($categories);
    }
}