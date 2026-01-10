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

        // Prevent duplicate payment
        if ($order->payment_status === 'paid') {
            return response()->json([ 'success' => false, 'message' => 'Order already paid' ], 400);
        }

        $intent = PaymentIntent::create([
            'amount' => (int) round($order->grand_total * 100), // INR → paise
            'currency' => 'inr',
            'description' => "Order #{$order->order_number}",
            'metadata' => [
                'order_id' => $order->id,
            ],
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);

        // Save as pending
        Payment::create([
            'order_id' => $order->id,
            'gateway' => 'stripe',
            'transaction_id' => $intent->id,
            'amount' => $order->grand_total,
            'currency' => 'INR',
            'status' => 'pending',
            'response' => json_encode($intent),
        ]);

        return response()->json([ 'success' => true, 'client_secret' => $intent->client_secret ]);
    }

    public function confirm(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $intent = PaymentIntent::retrieve($request->payment_intent_id);

        if ($intent->status === 'succeeded') {

            Payment::where('transaction_id', $intent->id)->update([
                'status' => 'success',
                'response' => json_encode($intent),
            ]);

            Order::where('id', $request->order_id)->update([
                'payment_status' => 'paid'
            ]);

            return response()->json(['success' => true]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Payment not completed'
        ], 400);
    }

}
