<?php

use App\Http\Controllers\AppliesController;
use App\Http\Controllers\SaudiApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ChecksController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\ComplaintsController;
use App\Http\Controllers\FeedbacksController;
use App\Http\Controllers\LocationsController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\QuestionsController;
use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('user-request', [AuthController::class, 'userRequest']);
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::post('create-feedback', [FeedbacksController::class, 'create']);

Route::post('create-question', [QuestionsController::class, 'create']);

Route::post('create-complaint', [ComplaintsController::class, 'create']);

Route::post('create-check', [ChecksController::class, 'create']);

Route::post('create-apply', [AppliesController::class, 'create']);

Route::post('create-saudi-application', [SaudiApplicationController::class, 'create']);

Route::get('get-categories', [CategoriesController::class, 'index']);

Route::get('check-certificate/{key}', [CertificateController::class, 'checkCertificate']);

Route::get('get-products', [ProductsController::class, 'index']);

Route::get('get-categories-with-products', [ProductsController::class, 'getCategoriesWithProducts']);

Route::get('get-locations', [LocationsController::class, 'index']);

Route::get('get-active-posts/{limit?}', [BlogController::class, 'activePosts']);
Route::get('posts/{slug}', [BlogController::class, 'showBySlug']);

Route::middleware(['auth:sanctum', 'isAPIAdmin'])->group(function () {

    Route::get('checkingAuthenticated', function (Request $request) {
        return response()->json(['user' => $request->user(), 'message' => 'You are in'], 200);
    });

    Route::get('get-users-requests', [AuthController::class, 'getUsersRequests']);
    Route::delete('decline-user-request/{id}', [AuthController::class, 'declineUserRequest']);

    Route::get('get-users', [UsersController::class, 'index']);
    Route::post('add-user', [UsersController::class, 'add']);
    Route::get('edit-user/{id}', [UsersController::class, 'edit']);
    Route::put('update-user/{id}', [UsersController::class, 'update']);
    Route::delete('delete-user/{id}', [UsersController::class, 'destroy']);

    Route::post('create-category', [CategoriesController::class, 'create']);
    Route::get('edit-category/{id}', [CategoriesController::class, 'getOne']);
    Route::put('update-category/{id}', [CategoriesController::class, 'update']);
    Route::delete('delete-category/{id}', [CategoriesController::class, 'destroy']);

    Route::post('create-certificate', [CertificateController::class, 'create']);
    Route::put('update-certificate/{id}', [CertificateController::class, 'update']);
    Route::get('get-certificates', [CertificateController::class, 'index']);
    Route::delete('delete-certificate/{id}', [CertificateController::class, 'destroy']);

    Route::post('create-product', [ProductsController::class, 'create']);
    Route::get('edit-product/{id}', [ProductsController::class, 'getOne']);
    Route::put('update-product/{id}', [ProductsController::class, 'update']);
    Route::delete('delete-product/{id}', [ProductsController::class, 'destroy']);

    Route::post('create-location', [LocationsController::class, 'create']);
    Route::get('edit-location/{id}', [LocationsController::class, 'getOne']);
    Route::put('update-location/{id}', [LocationsController::class, 'update']);
    Route::delete('delete-location/{id}', [LocationsController::class, 'destroy']);

    Route::put('update-feedback/{id}', [FeedbacksController::class, 'update']);
    Route::delete('delete-feedback/{id}', [FeedbacksController::class, 'destroy']);

    Route::put('update-question/{id}', [QuestionsController::class, 'update']);
    Route::delete('delete-question/{id}', [QuestionsController::class, 'destroy']);

    Route::put('update-complaint/{id}', [ComplaintsController::class, 'update']);
    Route::delete('delete-complaint/{id}', [ComplaintsController::class, 'destroy']);

    Route::put('update-check/{id}', [ChecksController::class, 'update']);
    Route::delete('delete-check/{id}', [ChecksController::class, 'destroy']);

    Route::put('update-apply/{id}', [AppliesController::class, 'update']);
    Route::delete('delete-apply/{id}', [AppliesController::class, 'destroy']);

    Route::put('update-saudi-application/{id}', [SaudiApplicationController::class, 'update']);
    Route::delete('delete-saudi-application/{id}', [SaudiApplicationController::class, 'destroy']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('checkLoggedIn', function (Request $request) {
        return response()->json(['user' => $request->user(), 'message' => 'You are Logged in'], 200);
    });

    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('get-feedbacks', [FeedbacksController::class, 'index']);
    Route::get('edit-feedback/{id}', [FeedbacksController::class, 'getOne']);

    Route::get('get-questions', [QuestionsController::class, 'index']);
    Route::get('edit-question/{id}', [QuestionsController::class, 'getOne']);

    Route::get('get-complaints', [ComplaintsController::class, 'index']);
    Route::get('edit-complaint/{id}', [ComplaintsController::class, 'getOne']);

    Route::get('get-checks', [ChecksController::class, 'index']);
    Route::get('edit-checks/{id}', [ChecksController::class, 'getOne']);

    Route::get('get-applies', [AppliesController::class, 'index']);
    Route::get('edit-apply/{id}', [AppliesController::class, 'getOne']);

    Route::get('get-saudi-applications', [SaudiApplicationController::class, 'index']);
    Route::get('edit-saudi-application/{id}', [SaudiApplicationController::class, 'getOne']);
    
    Route::get('get-posts', [BlogController::class, 'index']);
    Route::delete('content-items/{id}', [BlogController::class, 'deleteContentItem']);

    Route::post('posts', [BlogController::class, 'store']);
    Route::put('posts/{slug}', [BlogController::class, 'update']);
    Route::delete('posts/{id}', [BlogController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
