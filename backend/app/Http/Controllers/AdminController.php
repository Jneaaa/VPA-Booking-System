<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Admin;

class AdminController extends Controller
{
    // Assign department to admin
    public function assignDepartment(Request $request, Admin $admin)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,department_id',
            'is_primary' => 'sometimes|boolean'
        ]);
    
        $departmentId = $validated['department_id'];
        $isPrimary = $validated['is_primary'] ?? false;

        // Explicitly load department (forces use of Department model)
        $department = Department::findOrFail($departmentId);

        // Check if admin already has a different primary department
        if ($isPrimary) {
            $alreadyPrimary = $admin->departments()
                ->wherePivot('is_primary', true)
                ->where('department_id', '!=', $departmentId)
                ->exists();
    
            if ($alreadyPrimary) {
                return response()->json([
                    'message' => 'This admin already has a primary department.'
                ], 409);
            }
        }

        // Assign department to admin
        $admin->departments()->syncWithoutDetaching([
            $department->department_id => ['is_primary' => $isPrimary]
        ]);
    
        return response()->json(['message' => 'Department assigned successfully']);
    }

    // Get admin's departments with department names and primary flag
    public function getAdminDepartments(Admin $admin)
    {
        $departments = $admin->departments()
            ->select('departments.*', 'admin_departments.is_primary')
            ->get();

        return response()->json($departments);
    }

    // Get all admin information
    public function getAllAdmins()
    {
        $admins = Admin::all();
        return response()->json($admins);
    }

    // Get information of a single admin
    public function getAdminInfo(Admin $admin)
    {
        $admin->load('departments'); // Ensure departments are included
        return response()->json($admin);
    }
}