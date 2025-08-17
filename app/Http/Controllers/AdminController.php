<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Admin;
use App\Models\LookupTables\AdminRole;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class AdminController extends Controller
{
    // Add this new method
    public function adminRoles()
    {
        $roles = AdminRole::select('role_id', 'role_title')->get();
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
        $currentAdminId = auth()->id();
        $admins = Admin::with(['role', 'departments'])
            ->where('admin_id', '!=', $currentAdminId)  // Changed to explicit inequality check
            ->get();
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

        // Set default photo if not provided
        $validated['photo_url'] = $validated['photo_url'] ?? 'https://res.cloudinary.com/dn98ntlkd/image/upload/v1751033911/ksdmh4mmpxdtjogdgjmm.png';
        $validated['photo_public_id'] = $validated['photo_public_id'] ?? 'ksdmh4mmpxdtjogdgjmm';

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

    // Delete an admin
    public function deleteAdmin(Admin $admin)
    {
        try {
            $admin->departments()->detach(); // Remove department associations
            $admin->delete();

            return response()->json([
                'message' => 'Admin deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete admin',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Admin $admin)
{
    $validated = $request->validate([
        'first_name' => 'required|string|max:50',
        'last_name' => 'required|string|max:50',
        'middle_name' => 'nullable|string|max:50',
        'email' => 'required|email|max:150|unique:admins,email,'.$admin->admin_id.',admin_id',
        'contact_number' => 'nullable|string|max:20',
        'role_id' => 'required|exists:admin_roles,role_id',
        'school_id' => 'nullable|string|max:20',
        'password' => 'nullable|string|min:8|max:50',
    ]);

    // Update password only if provided
    if (!empty($validated['password'])) {
        $validated['hashed_password'] = bcrypt($validated['password']);
        unset($validated['password']);
    } else {
        unset($validated['password']);
    }

    $admin->update($validated);

    return response()->json([
        'message' => 'Admin updated successfully',
        'admin' => $admin
    ]);
}

    public function updatePhoto(Request $request)
    {
        try {
            $request->validate([
                'photo' => 'required_without:wallpaper|image|max:2048',
                'wallpaper' => 'required_without:photo|image|max:5120',
                'type' => 'required|in:photo,wallpaper'
            ]);

            $admin = $request->user();
            $type = $request->type;
            $file = $request->file($type); // Either 'photo' or 'wallpaper' file

            if (!$file) {
                throw new \Exception('No file provided');
            }

            // Define config based on type
            $config = [
                'photo' => [
                    'folder' => 'admin-photos',
                    'transformation' => ['width' => 400, 'height' => 400, 'crop' => 'fill', 'gravity' => 'face'],
                    'url_field' => 'photo_url',
                    'public_id_field' => 'photo_public_id'
                ],
                'wallpaper' => [
                    'folder' => 'admin-wallpapers',
                    'transformation' => ['width' => 1920, 'height' => 400, 'crop' => 'fill'],
                    'url_field' => 'wallpaper_url',
                    'public_id_field' => 'wallpaper_public_id'
                ]
            ][$type];

            // Delete old image if exists and not default
            $oldPublicId = $admin->{$config['public_id_field']};
            $defaultIds = ['ksdmh4mmpxdtjogdgjmm', 'verzp7lqedwsfn3hz8xf'];
            
            if ($oldPublicId && !in_array($oldPublicId, $defaultIds)) {
                try {
                    Cloudinary::destroy($oldPublicId);
                } catch (\Exception $e) {
                    \Log::warning("Failed to delete old image: {$oldPublicId}");
                }
            }

            // Upload new image
            $result = Cloudinary::upload($file->getRealPath(), [
                'folder' => $config['folder'],
                'transformation' => $config['transformation']
            ]);

            // Update admin record with new image info
            $updateData = [
                $config['url_field'] => $result->getSecurePath(),
                $config['public_id_field'] => $result->getPublicId()
            ];

            $admin->update($updateData);

            return response()->json([
                'message' => ucfirst($type) . ' updated successfully',
                $type . '_url' => $result->getSecurePath(),
                $type . '_public_id' => $result->getPublicId()
            ]);

        } catch (\Exception $e) {
            \Log::error('Photo upload error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to update ' . ($request->type ?? 'image'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

