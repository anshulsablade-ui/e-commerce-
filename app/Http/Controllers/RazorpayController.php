<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Razorpay\Api\Api;

class RazorpayController extends Controller
{
    public function createOrder(Request $request)
    {
        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
        
        $order = $api->order->create([
            'receipt' => 'order_' . time(),
            'amount' => (int) round($request->amount * 100),
            'currency' => 'INR',
            'description' => $request->description
        ]);

        Payment::create([
            'order_id' => $request->order_id,
            'transaction_id' => $order['id'],
            'gateway' => 'razorpay',
            'amount' => $request->amount,
            'currency' => 'INR',
            'status' => 'pending',
            'response' => json_encode($order)
        ]);

        return response()->json([
            'order_id' => $order['id'],
            'key' => config('services.razorpay.key'),
            'amount' => $request->amount * 100,
            'currency' => 'INR'
        ]);
    }

    public function verifyPayment(Request $request)
    {
        $api = new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );

        $api->utility->verifyPaymentSignature([
            'razorpay_order_id' => $request->razorpay_order_id,
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_signature' => $request->razorpay_signature
        ]);

        Payment::where('transaction_id', $request->razorpay_order_id)->update([
            'status' => 'success',
            'response' => json_encode($request->all())
        ]);

        Order::where('id', $request->order_id)->update([
            'payment_status' => 'paid'
        ]);

        return response()->json(['status' => 'Payment Successful']);
    }
}
