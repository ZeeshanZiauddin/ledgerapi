<?php


use App\Events\CommentIncrement;
use App\Events\PostBroadCastEvent;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\CompaniesController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\QueriesController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\ContactController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResources([
        "post" => PostController::class,
        "comment" => CommentController::class,
    ]);

    Route::post("/auth/logout", [AuthController::class, 'logout']);

    Route::post("/update/profile", [UserController::class, 'updateProfileImage']);
});


Route::post("/test/channel", function (Request $request) {
    // $post = Post::where("id", "2")->with('user')->first();
    // PostBroadCastEvent::dispatch($post);
    CommentIncrement::dispatch(2);
    // TestEvent::dispatch($request->all());
    return response()->json(["message" => "data sent successfully!"]);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/companies', [CompaniesController::class, 'index']); // List all companies
    Route::get('/companies/logos', [CompaniesController::class, 'select']); // List all companies
    Route::post('/company', [CompaniesController::class, 'store']); // Create a company


    Route::prefix('contact')->group(function () {
        Route::get('/', [ContactController::class, 'index']); // List inquiries
        Route::delete('/{id}', [ContactController::class, 'destroy']); // Delete inquiry
        Route::patch('/{id}/status', [ContactController::class, 'updateStatus']); // Update inquiry status
    });

    Route::get('/queries', [QueriesController::class, 'index']);



});


Route::prefix('contact')->group(function () {
    Route::post('/', action: [ContactController::class, 'store']);
});

//move to sanctum after postman done
Route::get('/companies/{key}', action: [CompaniesController::class, 'show']); // Public endpoint to fetch company metadata
Route::post('/queries', [QueriesController::class, 'store']);


Route::post("/auth/login", [AuthController::class, 'login']);
Route::post("/auth/register", [AuthController::class, 'register']);
Route::post("/auth/checkCredentials", [AuthController::class, 'checkCredentias']);

Broadcast::routes(['middleware' => ['auth:sanctum']]);


