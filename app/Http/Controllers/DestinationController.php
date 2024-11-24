<?php

namespace App\Http\Controllers;

use App\Http\Resources\DestinationResource;
use App\Models\Company;
use App\Models\Destination;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $key = $request->header('api_key') ?? $request->query('api_key');

        if (!$key) {
            return response()->json(['message' => 'API key is required'], 400);
        }

        $company = Company::where('api_key', $key)->first();

        if (!$company) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        // Fetch all destinations and transform them using the resource
        $destinations = DestinationResource::collection(Destination::all());

        return response()->json($destinations);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // This method is not typically used for APIs.
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'continent' => 'required|string|max:255',
        ]);

        $destination = Destination::create($validated);
        return response()->json($destination, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Destination $destination)
    {
        return response()->json($destination);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Destination $destination)
    {
        // This method is not typically used for APIs.
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Destination $destination)
    {
        $validated = $request->validate([
            'city' => 'string|max:255',
            'country' => 'string|max:255',
            'continent' => 'string|max:255',
        ]);

        $destination->update($validated);
        return response()->json($destination);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Destination $destination)
    {
        $destination->delete();
        return response()->json(['message' => 'Destination deleted successfully']);
    }

    /**
     * Return destinations grouped by continent and then by country.
     */
    public function data(Request $request)
    {
        $key = $request->header('api_key') ?? $request->query('api_key');

        if (!$key) {
            return response()->json(['message' => 'API key is required'], 400);
        }

        $company = Company::where('api_key', $key)->first();

        if (!$company) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        $destinations = Destination::all();


        return response()->json($destinations);
    }

}
