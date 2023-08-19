<?php

namespace Database\Factories;

use App\Data\OrderAddressData;
use App\Data\OrderProductData;
use App\Data\OrderProductsData;
use App\Models\OrderStatus;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $products = Product::factory()->count(random_int(3, 10))->create()->map(fn(Product $product) => new OrderProductData(
            productUuid: $product->uuid,
            quantity: $this->faker->numberBetween(2, 5)
        ))->toArray();

        return [
            'user_id' => User::factory(),
            'order_status_id' => OrderStatus::all()->random()->id,
            'payment_id' => Payment::factory(),
            'products' => new OrderProductsData($products),
            'address' => new OrderAddressData(
                billing: $this->faker->address(),
                shipping: $this->faker->address(),
            ),
            'delivery_fee' => $this->faker->randomFloat(2, 10, 20),
            'amount' => $this->faker->randomFloat(2, 100, 300),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
