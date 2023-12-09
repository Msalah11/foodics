<?php

namespace Tests\Feature;

use App\Jobs\SendLowStockAlert;
use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Event;

class PlaceOrderTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    private string $endpoint = '/api/place-order';
    public function test_order_can_be_processed()
    {
        $product = Product::factory()->create();
        $ingredient = Ingredient::factory()->create();
        $product->ingredients()->attach($ingredient->id, ['quantity' => 5]);

        $product2 = Product::factory()->create();
        $ingredient2 = Ingredient::factory()->create();
        $product2->ingredients()->attach($ingredient2->id, ['quantity' => 5]);

        $requestData = [
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
                [
                    'product_id' => $product2->id,
                    'quantity' => 2,
                ],
            ],
        ];

        $response = $this->postJson($this->endpoint, $requestData);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'Order placed successfully',
            'success' => true,
            'data' => [],
        ]);
    }

    public function test_not_enough_stock_throws_exception()
    {
        $product = Product::factory()->create();
        $ingredient = Ingredient::factory()->create(['current_stock' => 0]);
        $product->ingredients()->attach($ingredient->id, ['quantity' => 1]);

        $requestData = [
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ];

        $response = $this->postJson($this->endpoint, $requestData);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
        $response->assertJson([
            'error' => 'Not enough stock for ingredient: ' . $ingredient->name,
            'success' => false,
        ]);
    }

    public function test_it_returns_an_error_when_product_id_is_missing()
    {
        $orderData = [
            'products' => [
                ['quantity' => 2],
            ],
        ];
        $response = $this->postJson($this->endpoint, $orderData);
        $response->assertJsonValidationErrors('products.0.product_id');
    }

    public function test_it_returns_an_error_when_product_id_is_invalid()
    {
        $orderData = [
            'products' => [
                ['product_id' => rand(100, 500), 'quantity' => 2],
            ],
        ];
        $response = $this->postJson($this->endpoint, $orderData);
        $response->assertJsonValidationErrors('products.0.product_id');
    }

    public function test_it_returns_an_error_when_quantity_is_missing()
    {
        $product = Product::factory()->create();
        $orderData = [
            'products' => [
                ['product_id' => $product->id],
            ],
        ];
        $response = $this->postJson($this->endpoint, $orderData);
        $response->assertJsonValidationErrors('products.0.quantity');
    }

    public function test_it_returns_an_error_when_quantity_is_invalid()
    {
        $product = Product::factory()->create();

        $orderData = [
            'products' => [
                ['product_id' => $product, 'quantity' => 0],
            ],
        ];
        $response = $this->postJson($this->endpoint, $orderData);
        $response->assertJsonValidationErrors('products.0.quantity');
    }

    public function test_it_does_not_dispatch_job_if_not_meeting_condition()
    {
        Bus::fake();
        Event::fake();

        $ingredient = Ingredient::factory()->create([
            'current_stock' => 15,
            'original_stock' => 20,
            'merchant_notified_at' => null,
        ]);

        $ingredient->update(['current_stock' => 12]);

        Bus::assertNotDispatched(SendLowStockAlert::class);
    }

    public function test_it_does_not_dispatch_job_if_already_notified()
    {
        Bus::fake();
        Event::fake();

        $ingredient = Ingredient::factory()->create([
            'current_stock' => 8,
            'original_stock' => 20,
            'merchant_notified_at' => now(),
        ]);

        $ingredient->update(['current_stock' => 5]);

        Bus::assertNotDispatched(SendLowStockAlert::class);
    }
}
