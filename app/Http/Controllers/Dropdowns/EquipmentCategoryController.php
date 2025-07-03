<?php

namespace App\Http\Controllers\Dropdowns;

use App\Http\Controllers\Controller;
use App\Models\LookupTables\EquipmentCategory;
use Illuminate\http\JsonResponse;

class EquipmentCategoryController extends Controller

{
    public function index(): JsonResponse
    { 
        try {
            $categories = EquipmentCategory::all(['category_id', 'category_name']);
            return response()->json($categories);
        } catch (\Exception $e) {
            \Log::error('Error fetching equipment categories', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to fetch categories'], 500);
        }
    }
}