<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    // Fetch all users with optional search functionality
    public function getUsers(Request $request)
    {
        try {
            $searchTerm = $request->query('searchTerm');
            $query = User::query();

            if ($searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('phone', 'LIKE', "%{$searchTerm}%");
                });
            }

            $users = $query->where('isAdmin', false)->get();
            return response()->json($users, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Server error'], 500);
        }
    }

    // Block or unblock a user
    public function toggleBlockUser(Request $request, $userId)
    {
        try {
            $isBlocked = $request->input('isBlocked');

            $user = User::find($userId);
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $user->isBlocked = $isBlocked;
            $user->save();

            return response()->json([
                'message' => 'User has been ' . ($isBlocked ? 'blocked' : 'unblocked'),
                'success' => true,
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Server error'], 500);
        }
    }
}
