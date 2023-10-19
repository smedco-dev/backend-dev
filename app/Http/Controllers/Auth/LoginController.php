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
        ], [
            'personal_number.required' => 'The personal number cannot be empty.',
            'personal_number.numeric' => 'The personal number must be a number.',
            'password.required' => 'The password cannot be empty.'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 401);
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
                'status' => 200,
                'success' => true,
                'access_token' => $token,
                'message' => 'OTP code has been sent to ' . $user->email
            ], 200);
        } else {
            return response()->json([
                'status' => 401,
                'success' => false,
                'message' => 'Your personal number or password is incorrect.'
            ], 401);
        }
    }
}
