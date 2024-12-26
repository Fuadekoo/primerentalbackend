<?php

namespace App\Http\Controllers\Filter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Book;
use App\Models\User;



class SearchController extends Controller
{
    public function filter(Request $request)
    {
    }

    public function search_by_price(Request $request)
    {
       // Validate the incoming request
       $request->validate([
        'price' => 'required|numeric|min:0',
    ]);

    $price = $request->price;

    // Fetch properties less than or equal to the specified price
    $properties = Property::where('price', '<=', $price)->get();

    if ($properties->isEmpty()) {
        return response()->json(['message' => "No properties found for less than or equal to $price. Please search again."], 404);
    }

    return response()->json(['properties' => $properties]);

    }

    public function dashboard()
    {
        // Total posted houses
        $totalHouses = Property::count();

        // Active and inactive houses
        $activeHouses = Property::where('status', 1)->count();
        $inactiveHouses = Property::where('status', 0)->count();

        // Total bookings with different statuses
        $pendingBookings = Book::where('status', 'pending')->count();
        $approvedBookings = Book::where('status', 'approved')->count();
        $rejectedBookings = Book::where('status', 'rejected')->count();

        // Total users
        $totalUsers = User::count();

        // Users who have not destroyed their tokens
        $activeUsers = User::whereNotNull('remember_token')->count();

        return response()->json([
            'total_houses' => $totalHouses,
            'active_houses' => $activeHouses,
            'inactive_houses' => $inactiveHouses,
            'total_bookings' => [
                'pending' => $pendingBookings,
                'approved' => $approvedBookings,
                'rejected' => $rejectedBookings,
            ],
            'total_users' => $totalUsers,
        ]);
    }
}
