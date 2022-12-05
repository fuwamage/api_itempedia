<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Mail\UserOTP;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class UserOTPController extends Controller
{
    public function otp(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return new JsonResponse(['success' => false, 'message' => $validator->errors()], 422);
        }  

        $email = $request->all()['email'];
        $otp = $request->all()['otp'];

        if ($email) {
            Mail::to($email)->send(new UserOTP($email, $otp));
            return new JsonResponse([
                'status' => true,
                'success' => true,
                'data' => $email,
                'message' => "Thank you and please check your inbox"
            ], 200);
        }
    }
}
