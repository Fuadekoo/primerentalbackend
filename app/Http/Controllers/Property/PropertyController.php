<?php

namespace App\Http\Controllers\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use Illuminate\Support\Facades\Validator;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $properties = Property::select()->take(9)->orderBy('created_at', 'desc')->get();
        return response()->json($properties);

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
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string',
            'price' => 'required|integer',
           'type_id' => 'required|integer',
            'image' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $property = Property::create($request->all());
        return response()->json(['message' => 'Property created successfully', 'property' => $property]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $single_property = Property::find($id);
        return response()->json(['property' => $single_property]);
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
            'location' => 'required|string',
            'price' => 'required|integer',
            'type_id' => 'required|integer',
            'image' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $property = Property::find($id);
        $property->update($request->all());
        return response()->json(['message' => 'Property updated successfully', 'property' => $property]);
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
}
