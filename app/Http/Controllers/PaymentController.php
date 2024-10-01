<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Webhook;
use Throwable;

class PaymentController extends Controller
{
    public function showPaymentForm(Order $order)
    {
    return view('stripe', compact('order'));
    }


    public function stripePost(Request $request)
    {
        $request->validate([
            'order_id'    => 'required|exists:orders,id',
            'stripeToken' => 'required',
        ]);

        $order = Order::findOrFail($request->order_id);
        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $charge = Charge::create([
                'amount'      => $order->total_price * 100,
                'currency'    => 'usd',
                'source'      => $request->stripeToken,
                'description' => 'Payment for order ' . $order->id,
            ]);

            Payment::create([
                'order_id'         => $order->id,
                'stripe_charge_id' => $charge->id,
                'amount'           => $charge->amount,
                'currency'         => $charge->currency,
                'status'           => $charge->status,
            ]);

            Session::flash('success', 'Payment successful');
            return back();
            } catch (Throwable $e) {
                Session::flash('success', 'Payment failed. Please try again');
                return back();
        }
    }



    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $event = null;

        try {
            $event = Webhook::constructEvent(
                $payload,
                $request->header('Stripe-Signature'),
                config('services.stripe.webhook_secret')
            );
        } catch (Exception $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;

                Payment::create([
                    'order_id'         => $paymentIntent->metadata->order_id,
                    'stripe_charge_id' => $paymentIntent->id,
                    'amount'           => $paymentIntent->amount / 100,
                    'currency'         => $paymentIntent->currency,
                    'status'           => $paymentIntent->status,
                ]);
                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;

                Payment::create([
                    'order_id'         => $paymentIntent->metadata->order_id,
                    'stripe_charge_id' => $paymentIntent->id,
                    'amount'           => $paymentIntent->amount / 100,
                    'currency'         => $paymentIntent->currency,
                    'status'           => 'failed',
                ]);
                break;

            default:
                echo 'Received unknown event type ' . $event->type;
        }

        return response()->json(['status' => 'success'], 200);
    }

}
