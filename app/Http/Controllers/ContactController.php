<?php

namespace App\Http\Controllers;

use App\Http\Resources\ContactResource;
use App\Models\Company;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    // Store a new inquiry
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:15',
            'message' => 'required|string',
            'api_key' => 'required|string', // API key is required
        ]);

        // Check if the API key exists in the companies table
        $company = Company::where('api_key', $validatedData['api_key'])->first();

        if (!$company) {
            return response()->json([
                'message' => 'Invalid API key.',
            ], 401); // Unauthorized
        }

        // If API key is valid, proceed to create the inquiry
        Contact::create(attributes: [
            'full_name' => $validatedData['full_name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'message' => $validatedData['message'],
            'company_id' => $company->id, // Associate with the company
        ]);

        return response()->json([
            'message' => 'Inquiry submitted successfully.',
        ], 201); // Created
    }

    // Delete an inquiry
    public function destroy($id)
    {
        $inquiry = Contact::findOrFail($id);
        $inquiry->delete();

        return response()->json(['message' => 'Inquiry deleted successfully.']);
    }

    // Update the status of an inquiry
    public function updateStatus(Request $request, $id)
    {
        $inquiry = Contact::findOrFail($id);

        $validatedData = $request->validate([
            'status' => 'required|in:pending,resolved,rejected',
        ]);

        $inquiry->update(['status' => $validatedData['status']]);

        return response()->json(['message' => 'Inquiry status updated successfully.', 'inquiry' => $inquiry]);
    }

    // List all inquiries
    public function index()
    {
        $inquiries = Contact::with('company:id,title,favicon')->get();

        return ContactResource::collection($inquiries);
    }

}
