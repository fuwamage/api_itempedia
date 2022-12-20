<?php

namespace App\Http\Controllers\PaymentGateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentGateway\PaymentMidtrans;

use \Midtrans\Config;
use \Midtrans\Snap;


class MidtransController extends Controller
{
    /* =========================================== PAYMENT =========================================== */

    public function ServerKeyMidtrans(Request $request) {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');    // switch key in .env based on sandbox or production mode.
        Config::$isProduction = true;  // set true if production mode and vice versa.
        Config::$isSanitized = true;
        Config::$is3ds = true;
        
        $current_month = date("M");
        $rand = rand();
        $orderID = "$current_month-$rand";
        $params = array(
            'transaction_details' => array(
                'order_id' => "$orderID",
                'gross_amount' => $request['amount']          
            ),
            'customer_details' => array(
                'first_name' => 'budi',
                'last_name' => 'pratama',
                'email' => auth()->user()->email,
                'phone' => '08111222333'
            ),
        );
        
        // Get Snap Transaction Token
        $snapToken = Snap::getSnapToken($params);

        if($snapToken) {
            $data = [
                "order_id" => $orderID,
                "user_id" => auth()->user()->id,
                "token" => $snapToken
            ];
            
            $push = PaymentMidtrans::create($data);

            if($push) {
                return response()->json([
                    "status" => true,
                    "message" => 'New Token Pushed',
                    "data" => $snapToken,
                    "time" => date('Y-m-d').' 00:00:00'
                ]);
            } else {
                return response()->json([
                    "status" => false,
                    "message" => 'Not Stored'
                ]);
            }
        } else {
            return response()->json([
                "status" => false,
                "message" => 'Snap Token Not Created'
            ]);
        }
    }
}
