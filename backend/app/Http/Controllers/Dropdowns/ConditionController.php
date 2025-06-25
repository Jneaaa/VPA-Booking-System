<?php

namespace App\Http\Controllers\Dropdowns;

use App\Http\Controllers\Controller;
use App\Models\LookupTables\Condition;

class ConditionController extends Controller
{
    public function index()
    {
        return response()->json(Condition::where('is_active', true)->get());
    }
}
