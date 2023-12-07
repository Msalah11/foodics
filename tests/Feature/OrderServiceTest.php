<?php
// tests/Feature/OrderServiceTest.php

namespace Tests\Feature;

use App\Models\Ingredient;
use App\Models\Merchant;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_order_can_be_processed()
    {
        $merchant = Merchant::factory()->create();
        $product = Product::factory()->create();
        $ingredient = Ingredient::factory()->create(['merchant_id' => $merchant->id]);
        $product->ingredients()->attach($ingredient->id, ['quantity' => 5]);

        $orderDetails = [
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ];

        $orderService = new OrderService();
        $orderService->processOrder($orderDetails);

        $this->assertDatabaseHas('orders');
        $this->assertDatabaseHas('order_product', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
        $this->assertDatabaseHas('ingredients', [
            'id' => $ingredient->id,
            'current_stock' => 3,
        ]);
    }

    public function test_not_enough_stock_throws_exception()
    {
        $this->expectException(\RuntimeException::class);

        $product = Product::factory()->create();
        $ingredient = Ingredient::factory()->create(['current_stock' => 0]);
        $product->ingredients()->attach($ingredient->id, ['quantity' => 1]);

        $orderDetails = [
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ];

        $orderService = new OrderService();
        $orderService->processOrder($orderDetails);
    }

    public function test_email_notification_sent_on_low_stock()
    {
        Mail::fake();

        $merchant = Merchant::factory()->create();
        $ingredient = Ingredient::factory()->create([
            'merchant_id' => $merchant->id,
            'current_stock' => 2,
        ]);
        $product = Product::factory()->create();
        $product->ingredients()->attach($ingredient->id, ['quantity' => 1]);

        $orderDetails = [
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ];

        $orderService = new OrderService();
        $orderService->processOrder($orderDetails);

        Mail::assertSent(function ($mail) use ($merchant) {
            return $mail->hasTo($merchant->email) && $mail->subject('Low Stock Alert');
        });
    }

    public function test_email_notification_not_sent_on_sufficient_stock()
    {
        Mail::fake();

        $merchant = Merchant::factory()->create();
        $ingredient = Ingredient::factory()->create([
            'merchant_id' => $merchant->id,
            'current_stock' => 10,
        ]);
        $product = Product::factory()->create();
        $product->ingredients()->attach($ingredient->id, ['quantity' => 5]);

        $orderDetails = [
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 3,
                ],
            ],
        ];

        $orderService = new OrderService();
        $orderService->processOrder($orderDetails);

        Mail::assertNotSent(function ($mail) use ($merchant) {
            return $mail->hasTo($merchant->email);
        });
    }

    public function test_request_validation()
    {
        // Case: Valid order data
        $response = $this->postJson('/api/place-order', [
            'products' => [
                [
                    'product_id' => 1,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertOk();

        // Case: Missing products key
        $response = $this->postJson('/api/place-order', [
            'invalid_key' => [
                [
                    'product_id' => 1,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['products']);

        // Case: Missing product_id in products
        $response = $this->postJson('/api/place-order', [
            'products' => [
                [
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['products.0.product_id']);

        // Case: Missing quantity in products
        $response = $this->postJson('/api/place-order', [
            'products' => [
                [
                    'product_id' => 1,
                ],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['products.0.quantity']);

        // Case: Non-numeric quantity in products
        $response = $this->postJson('/api/place-order', [
            'products' => [
                [
                    'product_id' => 1,
                    'quantity' => 'non-numeric',
                ],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['products.0.quantity']);
    }
}
