<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    // Search users by email
    public function search(Request $request)
    {
        $search = $request->input('q');
        $users = User::where('email', 'like', "%$search%")
                    ->get();

        return response()->json($users);
    }

    // Show user with their requisitions
    public function showWithRequisitions($user_id)
    {
        $user = User::with(['requisitions' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])->findOrFail($user_id);

        return response()->json($user);
    }
    
    // List users 
    public function index()
    {
        $users = User::orderBy('email')->get();

        return response()->json($users);
    }

    // Store or fetch user by submitted credentials
    public function storeOrFetch(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'department' => 'required|string|max:100',
            // Add other user fields here if needed
        ]);

        // Try to find existing user by email (or use another unique field)
        $user = User::firstOrCreate(
            ['email' => $request->email],
            [
                'first_name' => $request->full_name,
                'department' => $request->department,
                // Add other fields if necessary
            ]
        );

        return response()->json([
            'message' => 'User stored or fetched successfully.',
            'user_id' => $user->user_id,
            'user' => $user
        ]);
    }
}