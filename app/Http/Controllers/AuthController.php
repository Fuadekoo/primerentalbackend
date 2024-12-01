<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{

    // Register a new user
    public function register(Request $request)
{
    // Validate the incoming request
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'role' => 'string|in:admin,customer',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Check if the user already exists
    if (User::where('email', $request->email)->exists()) {
        return response()->json(['message' => 'User already exists'], 409);
    }


    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'role' => $request->role ?? 'customer', // Default to customer
    ]);

    // Exclude the password from the user object
    $user->makeHidden('password');

    return response()->json(['success' => true, 'message' => 'User registered successfully']);
}


// Login user
public function login(Request $request)
{
    // Validate the incoming request
    $validator = Validator::make($request->all(), [
        'email' => 'required|string|email|max:255',
        'password' => 'required|string|min:8',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'User logged in successfully',
        'success' => true,
        'data' => $token,
    ]);
}
// Get user by ID

public function getUserById(Request $request)
    {
        try {
            $user = User::find($request->user()->id);
            if (!$user) {
                return response()->json([
                    'message' => 'User not found',
                    'success' => false,
                    'data' => null,
                ], 404);
            }
            return response()->json([
                'message' => 'User fetched successfully',
                'success' => true,
                'data' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'success' => false,
                'data' => null,
            ], 500);
        }
    }

// Logout user
public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();

    return response()->json(['message' => 'Logged out']);
}


}
