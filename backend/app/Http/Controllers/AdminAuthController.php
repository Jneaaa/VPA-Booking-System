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
            'email' => 'required|string',
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
}