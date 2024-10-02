<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\Payment;
use Exception;
use Stripe\Webhook;
use Stripe\Stripe;
use Stripe\Exception\SignatureVerificationException;

class WebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);

            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $this->handlePaymentSuccess($event->data->object);
                    break;
                case 'payment_intent.payment_failed':
                    $this->handlePaymentFailure($event->data->object);
                    break;
                default:
                    Log::info('Received unknown event type: ' . $event->type);
            }

            return response()->json(['status' => 'success']);
        } catch (Exception $e) {
            return response()->json(['status' => 'signature verification failed'], 400);
        }
    }

    protected function handlePaymentSuccess($paymentIntent)
    {
        $order = Order::where('id', $paymentIntent->metadata->order_id)->first();

        if ($order) {
            $order->update(['status' => 'paid']);

            Payment::where('stripe_charge_id', $paymentIntent->charges->data[0]->id)
                ->update(['status' => 'succeeded']);
        }
    }

    protected function handlePaymentFailure($paymentIntent)
    {
        $order = Order::where('id', $paymentIntent->metadata->order_id)->first();

        if ($order) {
            $order->update(['status' => 'failed']);
            Payment::where('stripe_charge_id', $paymentIntent->charges->data[0]->id)
                ->update(['status' => 'failed']);
        }
    }
}
