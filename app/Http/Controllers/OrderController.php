<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\StoreOrderRequest;

class OrderController extends Controller
{
    public function placeOrder(StoreOrderRequest $request)
    {
        $orderDetails = $request->validated();

        try {
            return $this->successResponse($orderDetails, 'Order placed successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }
}
