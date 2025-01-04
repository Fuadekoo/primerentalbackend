<?php

namespace App\Http\Controllers\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $properties = Property::where('status', 1)
            ->orderBy('created_at', 'desc')
            ->take(9)->with('homeType')
            ->get();
        return response()->json($properties);
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
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'offer_type' => 'required|string',
            'location' => 'required|string',
            'price' => 'required|integer',
            'type_id' => 'required|integer',
            'quantity' => 'required|integer|min:1|max:50', // Validation for quantity
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'bathrooms' => 'required|integer',
            'kitchen' => 'required|integer',
            'bedrooms' => 'required|integer',
            'squaremeters' => 'required|integer',
            'parking' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        $imageNames = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = Str::random(32) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('property_images'), $imageName);
                $imageNames[] = $imageName;
            }
        }

        $propertyData = $request->all();
        $propertyData['images'] = json_encode($imageNames);

        try {
            $property = Property::create($propertyData);
            return response()->json(['status' => 'success', 'message' => 'Property created successfully', 'property' => $property]);
        } catch (\Exception $e) {
            \Log::error('Error creating property: ' . $e->getMessage());
            return response()->json(['status' => 'failed', 'message' => 'Error creating property', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $single_property = Property::find($id);

            if (!$single_property) {
                return response()->json(['message' => 'Property not found'], 404);
            }

            // Add image URLs to the property
            $single_property->images = json_decode($single_property->images);
            if ($single_property->images) {
                $single_property->images = array_map(function ($image) {
                    return url('property_images/' . $image);
                }, $single_property->images);
            }

            return response()->json(['property' => $single_property], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Server error'], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $property = Property::find($id);
        return response()->json(['property' => $property]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'offer_type' => 'required|string',
            'location' => 'required|string',
            'price' => 'required|integer',
            'type_id' => 'required|integer',
            'quantity' => 'required|integer|min:1|max:50', // Validation for quantity
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'bathrooms' => 'required|integer',
            'kitchen' => 'required|integer',
            'bedrooms' => 'required|integer',
            'squaremeters' => 'required|integer',
            'parking' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        $property = Property::find($id);
        if (!$property) {
            return response()->json(['status' => 'failed', 'message' => 'Property not found'], 404);
        }

        $property->title = $request->title;
        $property->description = $request->description;
        $property->offer_type = $request->offer_type;
        $property->location = $request->location;
        $property->price = $request->price;
        $property->type_id = $request->type_id;
        $property->quantity = $request->quantity; // Update quantity
        $property->bathrooms = $request->bathrooms; // Update bathrooms
        $property->kitchen = $request->kitchen; // Update kitchen
        $property->bedrooms = $request->bedrooms; // Update bedrooms
        $property->squaremeters = $request->squaremeters; // Update square meters
        $property->parking = $request->parking; // Update parking


        $imageNames = json_decode($property->images, true) ?? [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = Str::random(32) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('property_images'), $imageName);
                $imageNames[] = $imageName;
            }
        }

        $property->images = json_encode($imageNames);
        $property->save();

        return response()->json(['status' => 'success', 'message' => 'Property updated successfully', 'property' => $property], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $property = Property::find($id);
        $property->delete();
        return response()->json(['message' => 'Property deleted successfully']);
    }

    public function changestatus(Request $request, string $id)
    {
        $property = Property::find($id);
        $property->status = $request->status;
        $property->save();
        return response()->json(['message' => 'Property status updated successfully', 'property' => $property]);
    }

    public function getProperty(Request $request)
    {
        try {
            // Extract search inputs from the request
            $location = $request->input('location');
            $offerType = $request->input('offer_type');
            $typeId = $request->input('type_id');

            // Start the query
            $query = Property::query();

            // Apply filters if input fields are provided
            if ($location) {
                $query->where('location', 'LIKE', "%{$location}%");
            }
            if ($offerType) {
                $query->where('offer_type', 'LIKE', "%{$offerType}%");
            }
            if ($typeId) {
                $query->where('type_id', 'LIKE', "%{$typeId}%");
            }

            // Execute the query and fetch the results in descending order by created_at
            $properties = $query->orderBy('created_at', 'desc')->get();

            // Add image URLs to the properties
            $properties->transform(function ($property) {
                $property->images = json_decode($property->images);
                if ($property->images) {
                    $property->images = array_map(function ($image) {
                        return url('property_images/' . $image);
                    }, $property->images);
                }
                return $property;
            });

            // Return the filtered properties as JSON
            return response()->json($properties, 200);
        } catch (\Exception $e) {
            // Handle any exceptions and return an error response
            return response()->json(['message' => 'Server error'], 500);
        }
    }

    public function filterProperty(Request $request)
    {
        try {
            // Extract search inputs from the request
            $location = $request->input('location');
            $offerType = $request->input('offer_type');
            $typeId = $request->input('type_id');

            // Start the query
            $query = Property::query();

            // Apply filters if input fields are provided
            if (!empty($location)) {
                $query->where('location', 'LIKE', "%{$location}%");
            }
            if (!empty($offerType)) {
                $query->where('offer_type', $offerType);
            }
            if (!empty($typeId)) {
                $query->where('type_id', $typeId);
            }

            // Fetch only properties with status = 1
            $query->where('status', 1);

            // Execute the query and fetch the results
            $properties = $query->get();

            // Add image URLs to the properties if needed
            $properties->transform(function ($property) {
                $property->image_url = url('path/to/images/' . $property->image);
                return $property;
            });

            return response()->json($properties, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch properties'], 500);
        }
    }
}
