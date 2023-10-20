<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Mail\OtpEmail;
use App\Models\VerifyCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'personal_number' => 'required|numeric',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'validation'      => $validator->errors(),
                'response_code'   => '00',
                'response_status' => true,
                'date_request'    => Carbon::now('Asia/Jakarta')->toDateTimeString()
            ], 401);
        }

        $user = User::where('personal_number', $request->personal_number)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken('smedco-token')->plainTextToken;

            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $otpLength = 6;
            $otpCode = '';

            for ($i = 0; $i < $otpLength; $i++) {
                $otpCode .= $characters[rand(0, strlen($characters) - 1)];
            }

            VerifyCode::create([
                'users_id' => $user->id,
                'otp' => $otpCode,
                'expire_at' => Carbon::now()->addMinutes(10)
            ]);

            Mail::to($user->email)->send(new OtpEmail($otpCode));

            return response()->json([
                'access_token'     => $token,
                'response_code'    => '00',
                'response_status'  => true,
                'response_message' => 'OTP code has been sent to ' . $user->email,
                'date_request'     => Carbon::now('Asia/Jakarta')->toDateTimeString()
            ], 200);
        } else {
            return response()->json([
                'response_code'    => '01',
                'response_status'  => false,
                'response_message' => 'Your personal number or password is incorrect',
                'date_request'     => Carbon::now('Asia/Jakarta')->toDateTimeString()
            ], 401);
        }
    }
}
