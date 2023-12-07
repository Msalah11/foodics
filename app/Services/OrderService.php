<?php

namespace App\Services;

use App\Exceptions\NotEnoughStockException;
use App\Models\Order;
use App\Models\Product;
use App\Models\Ingredient;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function processOrder(array $orderDetails)
    {
        // Collect the order details
        $productCollection = collect($orderDetails['products']);

        // Collect product IDs from the order details
        $productIds = $productCollection->pluck('product_id')->toArray();

        // Eager load products with their ingredients
        $products = Product::with('ingredients')->find($productIds);

        DB::transaction(function () use ($productCollection, $products) {
            // create a new order object
            $order = Order::create();

            // format the products to attach to the order
            $productsToAttach = $productCollection->mapWithKeys(function ($product) {
               return [$product['product_id']  => ['quantity' => $product['quantity']]];
            });

            // attach the products to the order
            $order->products()->attach($productsToAttach);

            // update the stock of the ingredients
            $this->checkUpdateIngredientsStock($productCollection, $products);

            return $order;
        });

    }

    private function checkUpdateIngredientsStock($productCollection, $products): void
    {
        // Iterate through each product in the order
        $productCollection->each(function ($orderProduct) use ($products) {
            // Find the product in the existing products collection
            $product = $products->firstWhere('id', $orderProduct['product_id']);

            // Iterate through each ingredient in the product
            $product->ingredients->each(function ($ingredient) use ($orderProduct) {
                // Calculate the quantity of the ingredient required for the order
                $ingredientRequestsQuantity = $orderProduct['quantity'] * $ingredient->pivot->quantity;

                // Check if there is enough stock for the ingredient
                if ($ingredient->current_stock < $ingredientRequestsQuantity) {
                    throw new NotEnoughStockException('Not enough stock for ingredient: ' . $ingredient->name);
                }

                // Update the stock of the ingredient
                $ingredient->decrement('current_stock', $ingredientRequestsQuantity);
            });
        });
    }
}
