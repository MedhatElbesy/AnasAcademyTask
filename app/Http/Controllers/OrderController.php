<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

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
        try {
            DB::beginTransaction();

            $totalPrice = collect($request->order_items)->sum(function ($item) {
                return $item['price'] * $item['quantity'];
            });

            $order = Order::create([
                'user_id' => auth()->id(),
                'total_price' => $totalPrice,
            ]);

            $orderItemsData = collect($request->order_items)->map(function ($item) use ($order) {
                return [
                    'order_id'   => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                ];
            })->toArray();

            OrderItem::insert($orderItemsData);

            DB::commit();
            return redirect()->route('payment.success', ['order' => $order]);

            // return ApiResponse::sendResponse(201, 'Order created successfully', new OrderResource($order));

        } catch (Throwable $e) {
            DB::rollBack();

            return ApiResponse::sendResponse(500, 'Failed to create order. Please try again later.');
        }
    }





}
