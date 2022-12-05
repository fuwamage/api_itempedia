<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\User;


class AuthController extends Controller
{
    public function IndexUsers() {
        $users = DB::table('users')->get();

        return response()->json([
            'status' => true,
            'message' => 'got all of the available users',
            'data' => $users
        ]);
    }

    public function IndexAuthUser() {
        $auth_user = DB::table('users')->where('id', auth()->user()->id)->first();
        return response()->json([
            'status' => true,
            'message' => 'got your authenticated information',
            'data' => $auth_user
        ]);
    }

    public function IndexUserByName($name){
        $auth_user = DB::table('users')->where('name', $name)->first();

        return response()->json([
            'status' => true,
            'message' => 'got your authenticated information',
            'data' => $auth_user
        ]);
    }

    public function indexAllEmail(Request $request) {

        $validation = Validator::make($request->all(), [                                                      
            'email' => 'required|email|unique:users',
        ]);
        
        if ($validation->fails()) {            
            return response()->json([
                "status" => true,
                "message" => $validation->errors(),
                "data" => "error",
            ], 200);
        }

        return response()->json([
            "status" => true,
            "message" => 'Email is Unique',
            "otp" => str_pad((random_int(0, 999999)), 6, 0, STR_PAD_LEFT)
        ]);
    }

    public function indexReferrerCode($referral) {
        $fetch_referrer = DB::table('users')
            ->select('users.*')
            ->where('users.referral', $referral)
            ->first();

        if($fetch_referrer) {
            return response()->json([
                "status" => true,
                "message" => '*Data Referrer Code Collected',
                "data" => $fetch_referrer
            ]);
        } else {
            return response()->json([
                "status" => false,
                "message" => '*Invalid Referrer Code'
            ], 403);
        }
    }

    public function storeUser(Request $request) {

        $validation = Validator::make($request->all(), [
            'name' => 'required|unique:users|min:4|max:12',                                                      
            'email' => 'required|email|unique:users',
            'password' => 'required|min:4|max:12|required_with:repassword|same:repassword',
            'repassword' => 'min:4|max:12|'
        ]);

        if ($validation->fails()) :
            return response()->json([
                "status" => false,
                "message" => $validation->errors(),
            ], 403);
        endif;

        $referrer = User::where('referral', $request['referrer'])->first();

        if(!$referrer) {
            if ($request['referrer'] == null) {
                $insertUser = User::create([
                    "name" => StrToLower(Trim($request['name'])),
                    "email" => StrToLower(Trim($request['email'])),
                    "password" => Hash::make($request['password']),
                    "referral" => $this->generateUniqueCode(),
                    "referrer" => $request['referrer']                    
                ]);
                
                $user = User::where('name', StrToLower(Trim($request['name'])))->first();
                $token = $user->createToken('itempedia_token')->plainTextToken;

                $res = [
                    'user' => $insertUser,
                    'access_token' => $token
                ];
        
                if ($res) :
                    return response()->json([
                        "status" => true,
                        "message" => 'Signup Completed',
                        "data" => $res
                    ]);
                endif;
            }

            return response([
                'message' => 'Referral Code not found'
            ], 406);
            
        } else {
            $insertUser = User::create([
                "name" => StrToLower(Trim($request['name'])),
                "email" => StrToLower(Trim($request['email'])),
                "password" => Hash::make($request['password']),
                "referral" => $this->generateUniqueCode(),
                "referrer" => $request['referrer']                    
            ]);

            $user = User::where('name', StrToLower(Trim($request['name'])))->first();
            $token = $user->createToken('itempedia_token')->plainTextToken;

            $res = [
                'user' => $insertUser,
                'access_token' => $token
            ];
    
            if ($res) :
                return response()->json([
                    "status" => true,
                    "message" => 'Signup Completed',
                    "data" => $res
                ]);
            endif;
        }
    }



    public function generateUniqueCode() {

        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersNumber = strlen($characters);
        $codeLength = 6;

        $code = '';

        while (strlen($code) < 6) {
            $position = rand(0, $charactersNumber - 1);
            $character = $characters[$position];
            $code = $code.$character;        
        }

        if (User::where('referral', $code)->exists()) {
            $this->generateUniqueCode();
        }

        return $code;
    }



    // =============================================================== Sign In ================================================================================


    public function signInUser(Request $request)
    {
        // check email
        $user = User::where('email', $request['email'])->first();

        // check password
        if(!$user || !Hash::check($request['password'], $user->password)) {
            return response([
                'message' => 'Bad Credentials'
            ], 401);
        }

        $token = $user->createToken('itempedia_token')->plainTextToken;

        return response()->json([
            "status" => true,
            "message" => 'Hi '.$user->name.', welcome to Itempedia',
            "data" => $user,
            "access_token" => $token,
            "token_type" => 'Bearer'
        ]);
    }

    public function getUser()
    {
        // return auth()->user();
        $token = request()->user()->currentAccessToken()->token;

        $userAuth = auth()->user();

        $path = public_path('assets/avatar'); 
        
        $avaPath = '/assets/avatar/';
        
        // check user avatar availability
        if(!empty($userAuth->avatar) && file_exists($path.'/'.$userAuth->avatar)) {       // validation if user has avatar data
            // $token = $request->bearerToken();
            $userAuth->avatar = $avaPath . $userAuth->avatar;
            
            return response()->json([
                "status" => true,
                "message" => 'Data collected',
                "access_token" => $token,
                'token_type' => 'Bearer',
                "data" => $userAuth
            ]);
        } else {            
            $userAuth->avatar = $avaPath . "default.png";

            return response()->json([
                "status" => true,
                "message" => 'Data collected',
                "access_token" => $token,
                'token_type' => 'Bearer',
                "data" => $userAuth
            ]);            
        }        
    }

    public function destroyTokens() {
        $key = [];
        
        $tokens_count = DB::table('personal_access_tokens')
        ->count();

        $deleteUs = DB::table('personal_access_tokens')
        ->where('tokenable_id', '=', auth()->user()->id)
        ->latest()
        ->take($tokens_count)
        ->skip(3)
        ->get();

        foreach($deleteUs as $deleteMe) {
            $key[] = $deleteMe->id;
        }
        
        $asd = DB::table('personal_access_tokens')
        ->delete($key);

        if ($asd) {
            return response()->json([
                'status' => true,
                'message' => 'Other tokens is deleted except 3 newest'
            ]);
        }
    }


    // =============================================================== Sign Out ================================================================================


    public function signOutUser()
    {
        // auth()->user()->tokens()->delete();                                  // Revoke all of tokens based on logged ID
        auth()->user()->currentAccessToken()->delete();                         // Revoke current token based on logged ID

        return response()->json([
            "status" => true,
            "message" => 'You have successfully logged out',
        ]);
    }
}
