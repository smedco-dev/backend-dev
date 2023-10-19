<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Mail\OtpEmail;
use App\Models\VerifyCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class VerificationController extends Controller
{
    public function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required',
        ], [
            'otp.required' => 'The OTP code is required.',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = $request->user();
        if ($user && $user->currentAccessToken()) {
            $otpCode = $request->input('otp');
            $getOtpCode = VerifyCode::where('users_id', $user->id)->where('expire_at', '>', now())->value('otp');

            if ($otpCode === $getOtpCode) {
                $user->currentAccessToken()->is_verified_otp = true;
                $user->currentAccessToken()->save();

                $getOtpCodeByUsers = VerifyCode::where('users_id', $user->id)->get();
                foreach ($getOtpCodeByUsers as $getOtpCodeByUser) {
                    $getOtpCodeByUser->delete();
                }

                return response()->json([
                    'status' => 200,
                    'success' => true,
                    'message' => 'OTP verification successful.'
                ], 200);
            } else {
                return response()->json([
                    'status' => 400,
                    'success' => false,
                    'message' => 'Invalid OTP code.'
                ], 400);
            }
        } else {
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => 'No application token found.'
            ], 400);
        }
    }

    public function requestOTPCode(Request $request)
    {
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $otpLength = 6;
            $newOtpCode = '';

            for ($i = 0; $i < $otpLength; $i++) {
                $newOtpCode .= $characters[rand(0, strlen($characters) - 1)];
            }

            $existingOtpCode = VerifyCode::where('users_id', $user->id)->first();

            if ($existingOtpCode) {
                $existingOtpCode->update([
                    'otp' => $newOtpCode,
                    'expire_at' => Carbon::now()->addMinutes(10)
                ]);
            }

            Mail::to($user->email)->send(new OtpEmail($newOtpCode));

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'A new OTP code has been sent to ' . $user->email
            ], 200);
        } else {
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => 'No application token found.'
            ], 400);
        }
    }
}
