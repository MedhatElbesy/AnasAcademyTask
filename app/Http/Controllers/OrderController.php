<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Charge;
use Stripe\Stripe;

class OrderController extends Controller
{

    public function index()
    {
        try {
            $orders = Order::where('user_id', auth()->id())->with('orderItems.product')->get();
            return ApiResponse::sendResponse(200, 'Orders fetched successfully', OrderResource::collection($orders));
        } catch (Exception $e) {
            return ApiResponse::sendResponse(500, 'An error occurred while fetching orders: ' . $e->getMessage(), null);
        }
    }

    public function show($id)
    {
        try {
            $order = Order::where('id', $id)->where('user_id', auth()->id())->with('orderItems.product')->firstOrFail();
            return ApiResponse::sendResponse(200, 'Order fetched successfully', new OrderResource($order));
        } catch (Exception $e) {
            return ApiResponse::sendResponse(500, 'An error occurred while fetching the order: ' . $e->getMessage(), null);
        }
    }

    public function store(StoreOrderRequest $request)
    {
        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => auth()->id(),
                'total_price' => 0
            ]);

            $totalPrice = 0;
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $product->price * $item['quantity']
                ]);
                $totalPrice += $orderItem->price;
            }

            $order->update(['total_price' => $totalPrice]);

            Stripe::setApiKey(config('services.stripe.secret'));

            $charge = Charge::create([
                'amount' => $totalPrice * 100, // Stripe accepts the amount in cents
                'currency' => 'usd',
                'source' => $request->stripeToken,
                'description' => 'Payment for Order ' . $order->id,
            ]);

            Payment::create([
                'order_id'          => $order->id,
                'stripe_charge_id'  => $charge->id,
                'amount'            => $charge->amount / 100,
                'currency'          => $charge->currency,
                'status'            => $charge->status,
            ]);

            DB::commit();

            return ApiResponse::sendResponse(201, 'Order created and payment successful', new OrderResource($order->load('orderItems.product')));

        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse::sendResponse(500, 'An error occurred while creating the order or processing payment: ' . $e->getMessage(), null);
        }
    }







}
