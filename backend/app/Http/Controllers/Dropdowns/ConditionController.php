<?php

namespace App\Http\Controllers\Dropdowns;

use App\Http\Controllers\Controller;
use App\Models\LookupTables\Condition;

class ConditionController extends Controller
{
    public function index()
    {
        $conditions = Condition::orderBy('condition_id')->get(['condition_name', 'condition_name']);
        return response()->json($conditions);
    }
}
