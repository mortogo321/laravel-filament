<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_factory_creates_with_unique_slug_and_sku(): void
    {
        $a = Product::factory()->create();
        $b = Product::factory()->create();

        $this->assertNotSame($a->slug, $b->slug);
        $this->assertNotSame($a->sku, $b->sku);
        $this->assertDatabaseHas('products', ['id' => $a->id]);
    }

    public function test_product_casts_json_columns_to_array(): void
    {
        $product = Product::factory()->create([
            'tags' => ['audio', 'wireless'],
            'specifications' => ['Battery Life' => '30 hours'],
        ]);

        $product = $product->fresh();

        $this->assertIsArray($product->tags);
        $this->assertSame(['audio', 'wireless'], $product->tags);
        $this->assertIsArray($product->specifications);
    }

    public function test_product_soft_deletes_instead_of_force_deleting(): void
    {
        $product = Product::factory()->create();

        $product->delete();

        $this->assertSoftDeleted('products', ['id' => $product->id]);
        $this->assertCount(0, Product::all());
        $this->assertCount(1, Product::withTrashed()->get());
    }

    public function test_duplicate_slug_violates_unique_constraint(): void
    {
        Product::factory()->create(['slug' => 'same-slug', 'sku' => 'SKU-AAA01']);

        $this->expectException(QueryException::class);

        Product::factory()->create(['slug' => 'same-slug', 'sku' => 'SKU-BBB02']);
    }

    public function test_featured_and_status_filters_work(): void
    {
        Product::factory()->create(['status' => 'active', 'is_featured' => true, 'stock' => 5]);
        Product::factory()->create(['status' => 'draft', 'is_featured' => false, 'stock' => 50]);

        $this->assertSame(1, Product::where('status', 'active')->count());
        $this->assertSame(1, Product::where('is_featured', true)->count());
        $this->assertSame(1, Product::where('stock', '<', 20)->count());
    }
}
