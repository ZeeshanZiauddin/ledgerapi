<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Query;
use Illuminate\Http\Request;

class QueriesController extends Controller
{
    /**
     * Display a listing of the queries.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get all queries with their associated company
        $queries = Query::with('company')->get();

        // Return the queries as a JSON response
        return response()->json(['data' => $queries], 200);
    }

    /**
     * Store a newly created query in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data excluding the api_key
        $request->validate([
            'departure' => 'required|string',
            'arrival' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'passengers' => 'required|array',
            'date_range' => 'required|array',
        ]);

        // Check if the API key exists in the database
        $company = Company::where('api_key', $request->api_key)->first();

        if (!$company) {
            // If the API key does not exist, return an error response
            return response()->json(['error' => 'Invalid API key'], 400);
        }

        // Create the new query and associate it with the company
        $query = Query::create([
            'departure' => $request->departure,
            'arrival' => $request->arrival,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'passengers' => json_encode($request->passengers), // Store passengers as JSON
            'date_range' => json_encode($request->date_range), // Store date range as JSON
            'company_id' => $company->id, // Associate with company_id
        ]);

        // Return the created query with a success message
        return response()->json(['message' => 'Query created successfully', 'query' => $query], 201);
    }


    /**
     * Display the specified query.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id)
    {
        // Find the query by its ID
        $query = Query::with('company')->find($id);

        // If the query is not found, return an error response
        if (!$query) {
            return response()->json(['error' => 'Query not found'], 404);
        }

        // Return the query with its associated company
        return response()->json(['query' => $query], 200);
    }

    /**
     * Update the specified query in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $id)
    {
        // Find the query by its ID
        $query = Query::find($id);

        if (!$query) {
            return response()->json(['error' => 'Query not found'], 404);
        }

        // Validate the incoming request data
        $request->validate([
            'departure' => 'required|string',
            'arrival' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'passengers' => 'required|array',
            'date_range' => 'required|array',
        ]);

        // Update the query with the new data
        $query->update([
            'departure' => $request->departure,
            'arrival' => $request->arrival,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'passengers' => json_encode($request->passengers),
            'date_range' => json_encode($request->date_range),
        ]);

        return response()->json(['message' => 'Query updated successfully', 'query' => $query], 200);
    }

    /**
     * Remove the specified query from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id)
    {
        // Find the query by its ID
        $query = Query::find($id);

        // If the query is not found, return an error response
        if (!$query) {
            return response()->json(['error' => 'Query not found'], 404);
        }

        // Delete the query from the database
        $query->delete();

        // Return a success message
        return response()->json(['message' => 'Query deleted successfully'], 200);
    }
}
