<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;


class PayPalController extends Controller
{
    public function createOrder(Request $request)
    {
        // dd($request->all());
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $order = $provider->createOrder([
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => $request->amount // dynamic
                    ]
                ]
            ]
        ]);
        
        return response()->json($order);
    }

    public function captureOrder(Request $request)
    {
        // dd($request->all());
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $result = $provider->capturePaymentOrder($request->orderID);

        if ($result['status'] === 'COMPLETED') {
            // Save payment to DB here
            return response()->json([
                'status' => 'success',
                'payment_id' => $result['id']
            ]);
        }

        return response()->json(['status' => 'error'], 400);
    }
}
