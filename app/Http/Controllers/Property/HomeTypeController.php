<?php

namespace App\Http\Controllers\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HomeType;
use Illuminate\Support\Facades\Validator;

class HomeTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $searchTerm = $request->query('searchTerm');
            $query = HomeType::query();

            if ($searchTerm) {
                $query->where('home_type', 'LIKE', "%{$searchTerm}%");
            }

            $home_types = $query->get();
            return response()->json($home_types, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Server error'], 500);
        }
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
            'home_type' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $home_types = HomeType::create($request->all());
        return response()->json(['message' => 'Home type created successfully', 'home_types' => $home_types]);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $single_home_type = HomeType::find($id);
        return response()->json(['home_type' => $single_home_type]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $home_types = HomeType::find($id);
        return response()->json(['home_types' => $home_types]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $validator = Validator::make($request->all(), [
            'home_type' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $home_types = HomeType::find($id);
        $home_types->update($request->all());
        return response()->json(['message' => 'Home type updated successfully', 'home_types' => $home_types]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $home_types = HomeType::find($id);
        $home_types->delete();
        return response()->json(['message' => 'Home type deleted successfully']);
    }

    // Fetch all home types with optional search functionality
public function getHomeTypes(Request $request)
{
    try {
        $searchTerm = $request->query('searchTerm');
        $query = HomeType::query();

        if ($searchTerm) {
            $query->where('home_type', 'LIKE', "%{$searchTerm}%");
        }

        $home_types = $query->get();
        return response()->json($home_types, 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Server error'], 500);
    }
}
}


