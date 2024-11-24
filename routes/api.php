<?php

use App\Events\CommentIncrement;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\CompaniesController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\QueriesController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DestinationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Authenticated routes using Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResources([
        "post" => PostController::class,
        "comment" => CommentController::class,
    ]);

    Route::post("/auth/logout", [AuthController::class, 'logout']);
    Route::post("/update/profile", [UserController::class, 'updateProfileImage']);

    // Protected company routes
    Route::get('/companies', [CompaniesController::class, 'index']);
    Route::get('/companies/logos', [CompaniesController::class, 'select']);
    Route::post('/company', [CompaniesController::class, 'store']);

    // Protected contact routes
    Route::prefix('contact')->group(function () {
        Route::get('/', [ContactController::class, 'index']); // List inquiries
        Route::delete('/{id}', [ContactController::class, 'destroy']); // Delete inquiry
        Route::patch('/{id}/status', [ContactController::class, 'updateStatus']); // Update inquiry status
    });

    Route::get('/queries', [QueriesController::class, 'index']);

    // Protected destination routes
    Route::get('destinations/{destination}', [DestinationController::class, 'show']); // Get a single destination
    Route::post('destinations', [DestinationController::class, 'store']); // Create a new destination
    Route::put('destinations/{destination}', [DestinationController::class, 'update']); // Update an existing destination
    Route::delete('destinations/{destination}', [DestinationController::class, 'destroy']); // Delete a destination
});

// Public routes that don't require authentication
Route::get('/destinations', [DestinationController::class, 'index']); // Get destinations grouped by continent and country
Route::get('/companies/{key}', [CompaniesController::class, 'show']); // Fetch company metadata based on a key
Route::post('contact/', [ContactController::class, 'store']); // Submit contact inquiry
Route::post('/queries', [QueriesController::class, 'store']); // Submit a query

Route::post("/auth/login", [AuthController::class, 'login']);
Route::post("/auth/register", [AuthController::class, 'register']);
Route::post("/auth/checkCredentials", [AuthController::class, 'checkCredentials']); // Fixed typo

// Broadcast routes (for real-time events)
Broadcast::routes(['middleware' => ['auth:sanctum']]);

