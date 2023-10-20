<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\RoleController;

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

Route::post('auth/login', [LoginController::class, 'login'])->name('login');
Route::middleware(['auth:sanctum'])->group(function () {
    Route::controller(VerificationController::class)->group(function () {
        Route::post('auth/verify-otp', 'verifyOTP')->name('verify_otp');
        Route::post('auth/request-new-otp', 'requestOTPCode')->name('request_otp');
    });

    Route::middleware(['checkotp'])->group(function () {
        Route::controller(RoleController::class)->group(function () {
            Route::get('role', 'index')->name('role.index');
            Route::post('role', 'store')->name('role.store');
            Route::get('role/{id}', 'show')->name('role.show');
            Route::put('role/{id}', 'update')->name('role.update');
            Route::delete('role/{id}', 'destroy')->name('role.destroy');
        });

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
        })->name('logout');
    });
});
