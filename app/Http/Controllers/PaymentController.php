<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Exception;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Webhook;

class PaymentController extends Controller
{
    public function showPaymentForm()
    {
        return view('payment');
    }

public function processPayment(Request $request)
{
    $this->validate($request, [
        'stripeToken' => 'required',
        'order_id' => 'required|exists:orders,id',
    ]);

    Stripe::setApiKey(config('services.stripe.secret'));

    try {
        $charge = Charge::create([
            'amount'      => 1000,
            'currency'    => 'usd',
            'source'      => $request->stripeToken,
            'description' => 'Payment for Order ' . $request->order_id,
        ]);

        Payment::create([
            'order_id'          => $request->order_id,
            'stripe_charge_id'  => $charge->id,
            'amount'            => $charge->amount / 100,
            'currency'          => $charge->currency,
            'status'            => $charge->status,
        ]);

        return redirect()->route('payment.success')->with('success', 'Payment successful!');
    } catch (Exception $e) {
        return redirect()->back()->with('error', $e->getMessage());
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
                    'order_id' => $paymentIntent->metadata->order_id,
                    'stripe_charge_id' => $paymentIntent->id,
                    'amount' => $paymentIntent->amount / 100,
                    'currency' => $paymentIntent->currency,
                    'status' => $paymentIntent->status,
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
