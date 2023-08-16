<?php

namespace Database\Factories;

use App\Data\ProductMetadataData;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_uuid' => function() {
                return Category::factory()->create()->uuid;
            },
            'title' => $this->faker->sentence(),
            'uuid' => Str::uuid(),
            'price' => $this->faker->randomFloat(2, 40, 100),
            'description' => $this->faker->paragraphs(random_int(1, 3), true),
            'metadata' => new ProductMetadataData(
                brand: Str::uuid(),
                image: Str::uuid()
            ),
        ];
    }
}
