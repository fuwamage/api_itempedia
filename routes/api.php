<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\Email\UserOTPController;
use App\Http\Controllers\PaymentGateway\MidtransController;

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
    Route::get('/auth/user/detail', [AuthController::class, 'getUser']);
    
    Route::put('/auth/user/update', [AuthController::class, 'updateAuthUser']);
    Route::post('/auth/user/store/avatar', [AuthController::class, 'storeAvatar']);

    Route::delete('/auth/user/tokensdestroy', [AuthController::class, 'destroyTokens']);
    Route::get('/auth/user/signout', [AuthController::class, 'signOutUser']);

    Route::get('/auth/user/get/merchant', [ShopController::class, 'getAuthMerchant']);
    Route::post('/auth/user/store/merchant', [ShopController::class, 'storeMerchant']);

    Route::post('/auth/user/topup/midtrans', [MidtransController::class, 'ServerKeyMidtrans']);
});

// PUBLIC ROUTES
Route::get('/users', [AuthController::class, 'IndexAllUsers']);
Route::get('/users/{custom}', [AuthController::class, 'IndexUserByName']);


Route::post('/users/checkemail', [AuthController::class, 'indexAllEmail']);
Route::get('/users/checkreferral/{code}', [AuthController::class, 'indexReferrerCode']);
Route::post('/users/signup', [AuthController::class, 'storeUser']);
Route::post('/users/signin', [AuthController::class, 'signInUser']);

Route::post('/sendOTP', [UserOTPController::class, 'otp']);
