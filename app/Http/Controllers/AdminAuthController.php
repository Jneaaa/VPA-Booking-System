<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $admin = Admin::where('email', $request->email)->first();

            if (!$admin) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            if (!Hash::check($request->password, $admin->hashed_password)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            // Clear old tokens before issuing a new one
            $admin->tokens()->delete();
            
            // Create new token with 30-day expiration
            $token = $admin->createToken('admin-token', ['*'], now()->addDays(30))->plainTextToken;

            return response()->json([
                'token' => $token,
                'admin' => $admin->makeHidden('hashed_password')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Login failed',
                'error' => $e->getMessage()  // Remove in production
            ], 500);
        }
    }

    public function profile(Request $request)
    {
        return response()->json([
            'admin' => $request->user()
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}