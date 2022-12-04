<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// PROTECTED ROUTES
Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::get('/auth/user', [AuthController::class, 'IndexAuthUser']);
    Route::get('/auth/user/detail', [AuthController::class, 'getUser']);
    Route::post('/auth/user/signout', [AuthController::class, 'signOutUser']);
});

// PUBLIC ROUTES
Route::get('/users', [AuthController::class, 'IndexUsers']);
Route::get('/users/{custom}', [AuthController::class, 'IndexUserByName']);


Route::post('/users/checkemail', [AuthController::class, 'indexAllEmail']);
Route::get('/users/checkreferral/{code}', [AuthController::class, 'indexReferrerCode']);
Route::post('/users/signup', [AuthController::class, 'storeUser']);
Route::post('/users/signin', [AuthController::class, 'signInUser']);
