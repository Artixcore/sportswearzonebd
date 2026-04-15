<?php

namespace Tests\Unit;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductGenerateUniqueSlugTest extends TestCase
{
    use RefreshDatabase;

    public function test_appends_numeric_suffix_when_base_slug_exists(): void
    {
        Product::create([
            'name' => 'Brazil Jarsy',
            'slug' => 'brazil-jarsy',
            'price' => 10.00,
        ]);

        $slug = Product::generateUniqueSlug('Brazil Jarsy');

        $this->assertSame('brazil-jarsy-2', $slug);
    }

    public function test_ignores_current_product_when_updating(): void
    {
        $p = Product::create([
            'name' => 'Brazil Jarsy',
            'slug' => 'brazil-jarsy',
            'price' => 10.00,
        ]);

        $slug = Product::generateUniqueSlug('Brazil Jarsy', $p->id);

        $this->assertSame('brazil-jarsy', $slug);
    }

    public function test_includes_soft_deleted_rows_in_collision_check(): void
    {
        $p = Product::create([
            'name' => 'Brazil Jarsy',
            'slug' => 'brazil-jarsy',
            'price' => 10.00,
        ]);
        $p->delete();

        $slug = Product::generateUniqueSlug('Brazil Jarsy');

        $this->assertSame('brazil-jarsy-2', $slug);
    }
}
