<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;


class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $bookings = Book::orderBy('created_at', 'desc')->get(); // Fetch all bookings in descending order

            return response()->json([
                'success' => true,
                'data' => $bookings,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // fetch all my bookings
    public function mybookings()
    {
        $bookings = Book::where('user_id', Auth::id())->get();
        return response()->json(['bookings' => $bookings]);
    }

  // update the status of the booking
public function updateStatus(Request $request, $bookingId)
{
    // Validate the incoming request
    $request->validate([
        'status' => 'required|string|in:pending,approved,rejected,canceled',
    ]);

    // Find the booking
    $booking = Book::find($bookingId);
    if (!$booking) {
        return response()->json(['message' => 'Booking not found'], 404);
    }

    // Get the previous status
    $previousStatus = $booking->status;

    // If the previous status and the new status are the same, do nothing
    if ($previousStatus === $request->status) {
        return response()->json(['message' => 'No status change detected'], 200);
    }

    // Find the property associated with the booking
    $property = Property::find($booking->property_id);
    if (!$property) {
        return response()->json(['message' => 'Property not found'], 404);
    }

    // Adjust the property quantity based on the status change
    if ($previousStatus === 'pending') {
        if ($request->status === 'approved') {
            // No change in quantity, only status change
        } elseif ($request->status === 'rejected') {
            $property->quantity += 1; // Increase quantity if booking is rejected from pending
        }
    } elseif ($previousStatus === 'approved') {
        if ($request->status === 'approved') {
            return response()->json(['message' => 'Booking is already approved and cannot be approved again'], 400);
        } elseif ($request->status === 'rejected') {
            $property->quantity += 1; // Increase quantity if booking is rejected from approved
        }
    } elseif ($previousStatus === 'rejected') {
        if ($request->status === 'approved') {
            $property->quantity -= 1; // Decrease quantity if booking is approved from rejected
        } elseif ($request->status === 'rejected') {
            return response()->json(['message' => 'Booking is already rejected and cannot be rejected again'], 400);
        }
    }

    // Update the booking status
    $booking->status = $request->status;
    $booking->save();

    // Update the property status based on the quantity
    if ($property->quantity == 0) {
        $property->status = 0; // Set property status to 0 if quantity is 0
    } else {
        $property->status = 1; // Set property status to 1 if quantity is greater than 0
    }

    $property->save();

    return response()->json(['message' => 'Booking status updated successfully', 'booking' => $booking]);
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request,$propertyId)
    {
       // Validate the incoming request
        $request->validate([
            'phone_number' => 'required|string|max:255',
            'description' => 'required|string',
        ]);
        // Find the property by ID
    $property = Property::find($propertyId);

     // Check if the booking already exists for the user and property
     $existingBooking = Book::where('user_id', Auth::id())
     ->where('property_id', $propertyId)
     ->first();

 if ($existingBooking) {
     return response()->json(['message' => 'You have already booked this property.'], 409);
 }

        // Create the booking
        $booking = Book::create([
            'user_id' => Auth::id(),
            'property_id' => $propertyId,
            'phone_number' => $request->phone_number,
            'description' => $request->description,
        ]);

        $property->quantity -= 1;
        if ($property->quantity == 0) {
            $property->status = 0; // Set property status to 0 if quantity is 0
        }
        $property->save();

        return response()->json(['message' => 'Booking created successfully', 'booking' => $booking]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate the incoming request
        $request->validate([
            'phone_number' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        // Update the booking
        $booking = Book::find($id);
        $booking->phone_number = $request->phone_number;
        $booking->description = $request->description;
        $booking->save();

        return response()->json(['message' => 'Booking updated successfully', 'booking' => $booking]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Delete the booking
        $booking = Book::find($id);

        if ($booking->status === 'approved') {
            return response()->json(['message' => 'Cannot delete an approved booking.'], 403);
        }

        // Find the property associated with the booking
        $property = Property::find($booking->property_id);

        if ($property) {
            // Update the property quantity and status
            $property->quantity += 1;
            if ($property->quantity > 0) {
                $property->status = 1;
            }
            $property->save();
        }


        $booking->delete();

        return response()->json(['message' => 'Booking deleted successfully']);
    }
}
