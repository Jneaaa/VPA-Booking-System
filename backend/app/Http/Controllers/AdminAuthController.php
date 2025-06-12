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
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        // Fix: Use 'hashed_password' instead of 'password'
        if (!$admin || !Hash::check($request->password, $admin->hashed_password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $admin->createToken('admin-token', ['*'], now()->addDays(30))->plainTextToken;

        return response()->json([
            'token' => $token,
            'admin' => $admin->makeHidden('hashed_password') // Don't return password
        ]);

        
    }

    public function profile(Request $request)
    {
        return response()->json([
            'admin' => $request->user() // Returns the authenticated admin
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }


}