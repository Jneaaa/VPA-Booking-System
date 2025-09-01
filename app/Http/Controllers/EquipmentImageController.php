<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\EquipmentImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EquipmentImageController extends Controller
{
        public function getEquipmentImages($equipmentId): JsonResponse
    {
        try {
            $images = EquipmentImage::where('equipment_id', $equipmentId)
                ->orderBy('sort_order')
                ->get(['image_id', 'equipment_id', 'image_url', 'cloudinary_public_id', 'description', 'is_primary', 'sort_order']);

            return response()->json([
                'data' => $images
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching equipment images', [
                'error' => $e->getMessage(),
                'equipment_id' => $equipmentId
            ]);
            return response()->json([
                'message' => 'Failed to fetch equipment images',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
