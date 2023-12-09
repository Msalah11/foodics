<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\StoreOrderRequest;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(public OrderService $orderService)
    {

    }

    public function placeOrder(StoreOrderRequest $request)
    {
        $orderDetails = $request->validated();

        try {
            $this->orderService->processOrder($orderDetails);

            return $this->successResponse($orderDetails, __('Order placed successfully'));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
