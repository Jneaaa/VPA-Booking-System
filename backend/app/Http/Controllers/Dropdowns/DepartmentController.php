<?php

namespace App\Http\Controllers\Dropdowns;


use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\JsonResponse;

class DepartmentController extends Controller
{

    public function index(): JsonResponse
    {
        
        try {
            $departments = Department::all(['department_name']);
            return response()->json($departments);
        } catch (\Exception $e) {
            \Log::error('Error fetching departments', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to fetch departments'], 500);
        }

    }
}
