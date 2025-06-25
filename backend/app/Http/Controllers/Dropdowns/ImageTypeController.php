<?php

namespace App\Http\Controllers\Dropdowns;

use App\Http\Controllers\Controller;
use App\Models\LookupTables\ImageType;

class ImageTypeController extends Controller
{
    public function index()
    {
        return response()->json(ImageType::where('is_active', true)->get());
    }
}
