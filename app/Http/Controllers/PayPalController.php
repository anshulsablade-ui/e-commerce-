<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'order_id' => 'required|exists:orders,id'
        ]);

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();

        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('paypal.success', ['order_id' => $request->order_id]),
                "cancel_url" => route('paypal.cancel', ['order_id' => $request->order_id]),
            ],
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => config('paypal.currency'),
                        "value" => $request->amount
                    ],
                    "description" => $request->description ?? "PayPal Payment"
                ]
            ]
        ]);

        if (isset($response['id']) && $response['id'] != null) {
            foreach ($response['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    return response()->json(['success' => true, 'approval_url' => $link['href']]);
                }
            }
        }

        return response()->json(['success' => false, 'message' => 'Something went wrong with PayPal'], 500);
    }

    public function success(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();

        $response = $provider->capturePaymentOrder($request->token);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            $order = Order::find($request->order_id);
            if (!$order) {
                return redirect()->route('order.index')->with('error', 'Order not found');
            }
            
            Payment::create([
                'order_id' => $request->order_id,
                'transaction_id' => $response['id'],
                'gateway' => 'paypal',
                'amount' => $response['purchase_units'][0]['payments']['captures'][0]['amount']['value'],
                'currency' => $response['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code'],
                'status' => 'success',
                'response' => json_encode($response)
            ]);
            
            $order->update(['payment_status' => 'paid']);
            session()->flash('success', 'Payment successful');
            return redirect()->route('order.show', $request->order_id)->with(['response' => $response, 'order' => $order, 'status' => 'success', 'message' => 'Payment successful']);
            
        }

        return redirect()->route('order.show', $request->order_id)->with('error', 'Payment failed');
    }

    public function cancel(Request $request)
    {
        session()->flash('error', 'Payment cancelled');
        $orderId = $request->order_id;
        return redirect()->route('order.show', $orderId)->with('error', 'Payment cancelled');
    }
}