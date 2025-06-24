<?php

namespace App\Http\Controllers;

use App\Models\EquipmentCategory;
use Illuminate\Http\JsonResponse;

class EquipmentCategoryController extends Controller
{
    /**
     * Return a list of equipment categories.
     */
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
