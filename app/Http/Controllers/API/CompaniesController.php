<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Illuminate\Support\Facades\File;

class CompaniesController extends Controller
{
    // List all companies
    public function index()
    {
        return response()->json(Company::all());
    }
    public function select()
    {
        return response()->json(Company::all());
    }

    // Store a new company
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:companies,slug',
            'description' => 'required|string',
            'seo_keywords' => 'required|string',
            'email' => 'required|email|unique:companies,email',
            'phone' => 'required|string',
            'location' => 'required|string',
            'status' => 'required|boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,ico|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,ico|max:2048',
        ]);

        // Check if the logo file is uploaded
        $logoPath = null;
        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            $file = $request->file('logo');

            // Generate a unique file name for logo
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('public/temp/logos', $fileName);

            // Define the destination path for logo
            $companyLogoPath = storage_path('app/public/companies/logos');
            if (!File::exists($companyLogoPath)) {
                File::makeDirectory($companyLogoPath, 0777, true);  // Create directories if not exist
            }

            // Move the logo file to the final location
            $logoMoved = Storage::move($filePath, 'public/companies/logos/' . $fileName);
            if ($logoMoved) {
                $logoPath = 'companies/logos/' . $fileName;
            }
        }

        // Check if the favicon file is uploaded
        $faviconPath = null;
        if ($request->hasFile('favicon') && $request->file('favicon')->isValid()) {
            $file = $request->file('favicon');

            // Generate a unique file name for favicon
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('public/temp/favicons', $fileName);

            // Define the destination path for favicon
            $companyFaviconPath = storage_path('app/public/companies/favicons');
            if (!File::exists($companyFaviconPath)) {
                File::makeDirectory($companyFaviconPath, 0777, true);  // Create directories if not exist
            }

            // Move the favicon file to the final location
            $faviconMoved = Storage::move($filePath, 'public/companies/favicons/' . $fileName);
            if ($faviconMoved) {
                $faviconPath = 'companies/favicons/' . $fileName;
            }
        }

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Something went wrong, please try again.',
            ], 400);
        }

        // Generate an API key for the new company
        $apiKey = Company::generateApiKey();

        // Save the company record with the logo and favicon paths
        $company = Company::create([
            'title' => $request->title,
            'slug' => $request->slug,
            'description' => $request->description,
            'seo_keywords' => $request->seo_keywords,
            'email' => $request->email,
            'phone' => $request->phone,
            'location' => $request->location,
            'status' => $request->status,
            'api_key' => $apiKey,
            'created_by' => $user->id,
            'logo' => $logoPath,
            'favicon' => $faviconPath,  // Store favicon path if available
        ]);

        return response()->json([
            'message' => 'Company created successfully.',
            'favicon' => $faviconPath,
            'company' => $company,
        ], 201);
    }


    // Get company metadata by API key
    public function show($key)
    {
        $company = Company::where('api_key', $key)->first();

        if (!$company) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        return response()->json($company);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:companies,slug,' . $id,
            'description' => 'sometimes|string',
            'seo_keywords' => 'sometimes|string',
            'email' => 'sometimes|email|unique:companies,email,' . $id,
            'phone' => 'sometimes|string',
            'location' => 'sometimes|string',
            'status' => 'sometimes|boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,ico|max:2048',
        ]);

        $company = Company::find($id);

        if (!$company) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        $logoPath = $company->logo;

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $newLogoPath = 'logos/' . uniqid() . '.' . $logo->getClientOriginalExtension();

            // Save and optimize the new logo
            $logo->storeAs('temp', $newLogoPath, 'public');
            $tempFilePath = storage_path('app/public/temp/' . $newLogoPath);
            $optimizedFilePath = storage_path('app/public/' . $newLogoPath);

            $optimizerChain = OptimizerChainFactory::create();
            $optimizerChain->optimize($tempFilePath);

            rename($tempFilePath, $optimizedFilePath);

            // Delete the old logo
            if ($logoPath) {
                Storage::delete('public/' . $logoPath);
            }

            $logoPath = $newLogoPath;
        }


        $user = $request->user();

        if (!$user) {
            return response()->json([

                'message' => 'Something went wrong plz try again',
            ]);
        }
        $apiKey = Company::generateApiKey();
        $company->update([
            'title' => $request->title ?? $company->title,
            'slug' => $request->slug ?? $company->slug,
            'description' => $request->description ?? $company->description,
            'seo_keywords' => $request->seo_keywords ?? $company->seo_keywords,
            'email' => $request->email ?? $company->email,
            'phone' => $request->phone ?? $company->phone,
            'location' => $request->location ?? $company->location,
            'status' => $request->status ?? $company->status,
            'api_key' => $apiKey,
            'logo' => $logoPath,
            'created_by' => $user->id,
        ]);

        $company->logo_url = asset('storage/' . $company->logo);

        return response()->json([
            'company' => $company,
            'message' => 'Company updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $company = Company::find($id);

        if (!$company) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        // Delete the logo file if it exists
        if ($company->logo) {
            Storage::delete('public/' . $company->logo);
        }

        $company->delete();

        return response()->json(['message' => 'Company deleted successfully']);
    }
}
