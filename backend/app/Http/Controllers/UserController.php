<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    // List all users ordered by full name
    public function index()
    {
        $users = User::orderBy('full_name')->get();

        return response()->json($users);
    }

    // Search user by name/email 

    public function search(Request $request)
    {
        $search = $request->input('q');

        $users = User::where('full_name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->get();

        return response()->json($users);
    }

    // Shows user's requisition form history
    public function showWithRequisitions($user_id)
    {
        $user = User::with(['requisitions' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])->findOrFail($user_id);

        return response()->json($user);
    }

    // Store or fetch user by submitted credentials
    public function storeOrFetch(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'department' => 'required|string|max:100',
            // Add other user fields here if needed
        ]);

        // Try to find existing user by email (or use another unique field)
        $user = User::firstOrCreate(
            ['email' => $request->email],
            [
                'full_name' => $request->full_name,
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
