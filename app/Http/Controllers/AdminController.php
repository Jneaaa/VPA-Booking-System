<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Admin;
use App\Models\LookupTables\AdminRole;

class AdminController extends Controller
{
    // Add this new method
    public function adminRoles()
    {
        $roles = AdminRole::select('role_id', 'role_name')->get();
        \Log::debug('Fetched admin roles:', $roles->toArray());
        return response()->json($roles);
    }

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

    // Add a new method to create admin records
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'email' => 'required|email|unique:admins,email|max:150',
            'contact_number' => 'nullable|string|max:20',
            'role_id' => 'required|exists:admin_roles,role_id',
            'school_id' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|max:50',
            'photo_url' => 'nullable|string',
            'photo_public_id' => 'nullable|string',
            'wallpaper_url' => 'nullable|string',
            'wallpaper_public_id' => 'nullable|string',
        ]);

        // Hash the password
        $validated['hashed_password'] = bcrypt($validated['password']);
        unset($validated['password']); // Remove the plain password

        // Create new admin
        $admin = Admin::create($validated);

        return response()->json([
            'message' => 'Admin created successfully',
            'admin' => $admin
        ], 201);
    }
}