<?php

namespace App\Http\Controllers\Filter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Book;



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

    public function dashboard(){
         // Total posted houses
         $totalHouses = Property::count();


         // Total booked houses (accepted and rejected)
         $acceptedBookings = Book::where('status', 'accepted')->count();
         $rejectedBookings = Book::where('status', 'rejected')->count();

         return response()->json([
             'total_houses' => $totalHouses,
             'accepted_bookings' => $acceptedBookings,
             'rejected_bookings' => $rejectedBookings,
         ]);
    }
}
