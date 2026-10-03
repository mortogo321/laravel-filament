<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
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
        $name = $this->faker->unique()->words(3, true);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name).'-'.$this->faker->unique()->randomNumber(5),
            'sku' => 'SKU-'.$this->faker->unique()->bothify('###??'),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 5, 500),
            'cost' => $this->faker->randomFloat(2, 1, 300),
            'stock' => $this->faker->numberBetween(0, 200),
            'status' => $this->faker->randomElement(['draft', 'active', 'archived']),
            'is_featured' => $this->faker->boolean(20),
            'is_visible' => true,
            'brand' => $this->faker->company(),
            'category' => $this->faker->randomElement(['Electronics', 'Apparel', 'Home', 'Books']),
            'images' => null,
            'tags' => [$this->faker->word(), $this->faker->word()],
            'specifications' => ['Weight' => $this->faker->randomFloat(1, 0.1, 10).' kg'],
            'published_at' => $this->faker->optional()->date(),
            'user_id' => null,
        ];
    }
}
