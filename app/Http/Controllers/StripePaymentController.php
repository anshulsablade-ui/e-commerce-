<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripePaymentController extends Controller
{

public function createIntent(Request $request)
{
    Stripe::setApiKey(config('services.stripe.secret'));

    $order = Order::findOrFail($request->order_id);

    $intent = PaymentIntent::create([
        'amount' => (int) ($order->grand_total * 100),
        'currency' => 'inr',
        'description' => "Order #{$order->order_number}",
        'metadata' => [
            'order_id' => $order->id
        ],
    ]);
// dd($intent->toArray());
    return response()->json([
        'success' => true,
        'client_secret' => $intent->client_secret
    ]);
}


public function confirmPayment(Request $request)
{
    $payment = Payment::create([
        'order_id' => $request->order_id,
        'gateway' => 'stripe',
        'payment_intent_id' => $request->payment_intent_id,
        'amount' => $request->amount,
        'currency' => 'INR',
        'status' => 'success',
        'response' => json_encode($request->all()),
    ]);

    Order::where('id', $request->order_id)->update(['status' => 'paid']);

    return response()->json(['success' => true]);
}

}
