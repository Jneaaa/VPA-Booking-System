<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Assign department to admin
public function assignDepartment(Request $request, Admin $admin)
{
    $validated = $request->validate([
        'department_id' => 'required|exists:departments,department_id',
        'is_primary' => 'sometimes|boolean'
    ]);

    $admin->departments()->syncWithoutDetaching([
        $validated['department_id'] => ['is_primary' => $validated['is_primary'] ?? false]
    ]);

    return response()->json(['message' => 'Department assigned successfully']);
}

// Get admin's departments
public function getAdminDepartments(Admin $admin)
{
    return response()->json($admin->departments);
}
}
