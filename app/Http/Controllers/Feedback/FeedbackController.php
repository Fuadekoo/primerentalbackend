<?php

namespace App\Http\Controllers\Feedback;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($propertyId)
    {
        // Fetch feedback for the specified property
        $feedback = Feedback::where('property_id', $propertyId)->get();

        return response()->json(['feedback' => $feedback]);
    }

    public function myfedback($propertyId){
        $feedback = Feedback::where('property_id', $propertyId)->where('user_id', Auth::id())->get();
        return response()->json(['feedback' => $feedback]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request,$propertyId)
    {
         // Validate the incoming request
         $request->validate([
            'feedback' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        // Create the feedback
        $feedback = Feedback::create([
            'user_id' => Auth::id(),
            'property_id' => $propertyId,
            'feedback' => $request->feedback,
            'rating' => $request->rating,
        ]);

        return response()->json(['message' => 'Feedback submitted successfully', 'feedback' => $feedback], 201);

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
    public function update(Request $request, string $id, $propertyId)
    {
        // validate the incoming request
        $request->validate([
            'feedback' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        // Find the feedback byID aditional fether is  property_id and my feedback by user_id
        $feedback = Feedback::where('id', $id)->where('property_id', $propertyId)->where('user_id', Auth::id())->first();

        // Check if the feedback exists
        if (!$feedback) {
            return response()->json(['message' => 'Feedback not found'], 404);
        }

        // Update the feedback
        $feedback->update([
            'feedback' => $request->feedback,
            'rating' => $request->rating,
        ]);

        return response()->json(['message' => 'Feedback updated successfully', 'feedback' => $feedback]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id,$propertyId)
    {
        // Find the feedback byID aditional fether is  property_id and my feedback by user_id
        $feedback = Feedback::where('id', $id)->where('property_id', $propertyId)->where('user_id', Auth::id())->first();

        // Check if the feedback exists
        if (!$feedback) {
            return response()->json(['message' => 'Feedback not found'], 404);
        }

        // Delete the feedback
        $feedback->delete();

        return response()->json(['message' => 'Feedback deleted successfully']);
    }
}
