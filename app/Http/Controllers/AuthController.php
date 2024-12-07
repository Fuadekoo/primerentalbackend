<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;



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


public function updateProfileInfo(Request $request)
{
    try {
        // Validate the incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|confirmed|min:6',
        ]);

        $user = Auth::user();

        // Update the user's name
        $user->name = $request->name;

        // Update the user's password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile information updated successfully.',
            'data' => [
                'name' => $user->name,
            ],
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}

public function updateProfilePhoto(Request $request)
{
    try {
        // Validate the incoming request
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Generate a unique filename using date and uniqid
            $imageName = date('YmdHis') . uniqid() . '.' . $request->file('avatar')->extension();

            // Move the uploaded file to the profile_images directory
            $request->file('avatar')->move(public_path('profile_images'), $imageName);

            // Delete the old avatar if it exists
            if ($user->avatar && file_exists(public_path('profile_images/' . $user->avatar))) {
                unlink(public_path('profile_images/' . $user->avatar));
            }

            // Save the new avatar name in the database
            $user->avatar = $imageName;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile photo updated successfully.',
            'data' => [
                'avatar_url' => url('profile_images/' . $user->avatar),
            ],
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}

public function getProfilePhoto()
{
    try {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'name' => $user->name,
                'avatar_url' => $user->avatar ? url('profile_images/' . $user->avatar) : null,
            ],
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}


}
