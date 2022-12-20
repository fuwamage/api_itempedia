<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Merchant;

class ShopController extends Controller
{
    public function getAuthMerchant(Request $request) {
        $authMerch = Merchant::where('userID', auth()->user()->id)->first();
        
        if($authMerch) {
            return response()->json([
                "status" => true,
                "message" => "we got your merchant.",
                "data" => $authMerch
            ]);
        } else {
            return response()->json([
                "status" => false,
                "message" => "your merchant was not found",
                "data" => $authMerch
            ], 404);
        }
    }
    
    public function storeMerchant(Request $request) {
        $validation = Validator::make($request->all(), [                                                     
            'userID' => 'required|unique:merchant',
            'merchantName' => 'required|unique:merchant|min:4|max:12|',
            'merchantBio' => 'required|min:10|max:100'
        ]);

        if ($validation->fails()) :
            return response()->json([
                "status" => false,
                "message" => $validation->errors(),
            ], 403);
        endif;

        $uniqueID = $this->generateMerchantID();

        if ($uniqueID):
            $insertMerchant = Merchant::create([
                "merchantID" => $uniqueID,
                "userID" => $request['userID'],
                "merchantName" => StrToLower($request['merchantName']),      
                "merchantBio" => StrToLower($request['merchantBio'])
            ]);

            if ($insertMerchant):
                $merchant = Merchant::where('merchantID', $uniqueID)->first();
                return response()->json([
                    'status' => true,
                    'message' => 'merchant has been successfully created',
                    'data' => $merchant
                ]);
            endif;
        endif;
    }

    public function generateMerchantID() {

        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersNumber = strlen($characters);
        $codeLength = 11;

        $code = '';

        while (strlen($code) < 11) {
            $position = rand(0, $charactersNumber - 1);
            $character = $characters[$position];
            $code = $code.$character;        
        }

        if (Merchant::where('merchantID', $code)->exists()) {
            $this->generateMerchantID();
        }

        return $code;
    }
    
}
