<?php

namespace App\Http\Controllers\Dropdowns;

use App\Http\Controllers\Controller;
use App\Models\RequisitionPurpose;

class RequisitionPurposeController extends Controller
{
    public function index()
    {
        $purpose = RequisitionPurpose::orderBy('purpose_id')->get(['purpose_name', 'purpose_name']);
        return response()->json($purpose);
    }
}
