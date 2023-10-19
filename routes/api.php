<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\VerificationController;

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

Route::post('auth/login', [LoginController::class, 'login'])->name('smedco_login');
Route::middleware(['auth:sanctum'])->group(function () {
    Route::controller(VerificationController::class)->group(function () {
        Route::post('verify-otp', 'verifyOTP')->name('verify_otp');
        Route::post('request-new-otp', 'requestOTPCode')->name('request_otp');
    });

    Route::middleware(['checkotp'])->group(function () {
        Route::post('logout', function (Request $request) {
            $user = $request->user();

            if ($user && $user->currentAccessToken()) {
                $user->currentAccessToken()->delete();

                return response()->json([
                    'status' => 200,
                    'success' => true,
                    'message' => 'You made it out.'
                ], 200);
            } else {
                return response()->json([
                    'status' => 400,
                    'success' => false,
                    'message' => 'No application token found.'
                ], 400);
            }
        })->name('smedco_logout');
    });
});
