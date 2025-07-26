<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class EquipmentItem extends Controller
{
    public function index()
    {
        $equipmentItems = DB::table('equipment_items')->get();
        return response()->json($equipmentItems);
    }
}
