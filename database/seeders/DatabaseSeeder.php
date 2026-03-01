<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => Hash::make('password'), 'email_verified_at' => now(), 'remember_token' => Str::random(10), 'is_admin' => true]
        );

        $this->ensurePlaceholderImage();

        $mens = Category::firstOrCreate(['slug' => 'mens'], ['name' => "Men's", 'sort_order' => 1]);
        $tshirts = Category::firstOrCreate(['slug' => 'mens-tshirts'], ['name' => "Men's T-Shirts", 'parent_id' => $mens->id, 'sort_order' => 1]);
        $pants = Category::firstOrCreate(['slug' => 'mens-pants'], ['name' => "Men's Pants", 'parent_id' => $mens->id, 'sort_order' => 2]);
        $jackets = Category::firstOrCreate(['slug' => 'mens-jackets'], ['name' => "Men's Jackets", 'parent_id' => $mens->id, 'sort_order' => 3]);
        $shorts = Category::firstOrCreate(['slug' => 'mens-shorts'], ['name' => "Men's Shorts", 'parent_id' => $mens->id, 'sort_order' => 4]);
        $women = Category::firstOrCreate(['slug' => 'womens'], ['name' => "Women's", 'sort_order' => 2]);
        $kids = Category::firstOrCreate(['slug' => 'kids'], ['name' => "Kids", 'sort_order' => 3]);

        $products = [
            ['name' => 'Blue Sports T-Shirt', 'category_id' => $tshirts->id, 'price' => 1200, 'compare_at_price' => 1500, 'sku' => 'M-T-001', 'is_featured' => true],
            ['name' => 'Black Cotton Tee', 'category_id' => $tshirts->id, 'price' => 899, 'compare_at_price' => 1100, 'sku' => 'M-T-002', 'is_featured' => true],
            ['name' => 'White Running Tee', 'category_id' => $tshirts->id, 'price' => 950, 'sku' => 'M-T-003'],
            ['name' => 'Grey Crew Neck', 'category_id' => $tshirts->id, 'price' => 750, 'compare_at_price' => 999, 'sku' => 'M-T-004'],
            ['name' => 'Navy Polo Shirt', 'category_id' => $tshirts->id, 'price' => 1499, 'sku' => 'M-T-005', 'is_featured' => true],
            ['name' => 'Running Shorts', 'category_id' => $shorts->id, 'price' => 800, 'sku' => 'M-S-001'],
            ['name' => 'Training Shorts Black', 'category_id' => $shorts->id, 'price' => 699, 'compare_at_price' => 899, 'sku' => 'M-S-002'],
            ['name' => 'Cargo Shorts', 'category_id' => $shorts->id, 'price' => 1299, 'sku' => 'M-S-003'],
            ['name' => 'Jogger Pants', 'category_id' => $pants->id, 'price' => 1899, 'compare_at_price' => 2200, 'sku' => 'M-P-001', 'is_featured' => true],
            ['name' => 'Track Pants', 'category_id' => $pants->id, 'price' => 1599, 'sku' => 'M-P-002'],
            ['name' => 'Chino Trousers', 'category_id' => $pants->id, 'price' => 2199, 'sku' => 'M-P-003'],
            ['name' => 'Sports Jacket', 'category_id' => $jackets->id, 'price' => 2999, 'compare_at_price' => 3500, 'sku' => 'M-J-001', 'is_featured' => true],
            ['name' => 'Light Windbreaker', 'category_id' => $jackets->id, 'price' => 2499, 'sku' => 'M-J-002'],
            ['name' => 'Hooded Sweatshirt', 'category_id' => $jackets->id, 'price' => 1799, 'sku' => 'M-J-003'],
            ['name' => 'Yoga Leggings', 'category_id' => $women->id, 'price' => 1800, 'compare_at_price' => 2200, 'sku' => 'W-L-001', 'is_featured' => true],
            ['name' => 'Sports Bra', 'category_id' => $women->id, 'price' => 950, 'sku' => 'W-B-001'],
            ['name' => 'Kids Track Pants', 'category_id' => $kids->id, 'price' => 650, 'sku' => 'K-P-001', 'is_featured' => true],
        ];

        foreach ($products as $i => $p) {
            $product = Product::updateOrCreate(
                ['sku' => $p['sku']],
                array_merge($p, [
                    'slug' => Str::slug($p['name']),
                    'short_description' => 'Quality sportswear for active lifestyle. Comfortable fit and durable fabric.',
                    'description' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.\n\nUt enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.",
                    'stock' => rand(10, 100),
                    'is_active' => true,
                    'sort_order' => $i + 1,
                ])
            );

            if (! $product->images()->where('path', 'placeholders/placeholder.jpg')->exists()) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => 'placeholders/placeholder.jpg',
                    'sort_order' => 0,
                ]);
            }
        }
    }

    private function ensurePlaceholderImage(): void
    {
        $dir = 'placeholders';
        $path = $dir . '/placeholder.jpg';
        $fullPath = storage_path('app/public/' . $path);

        if (file_exists($fullPath)) {
            return;
        }

        $dirPath = dirname($fullPath);
        if (! is_dir($dirPath)) {
            mkdir($dirPath, 0755, true);
        }

        if (function_exists('imagecreatetruecolor')) {
            $img = imagecreatetruecolor(400, 400);
            if ($img !== false) {
                $grey = imagecolorallocate($img, 240, 240, 240);
                imagefill($img, 0, 0, $grey);
                imagejpeg($img, $fullPath, 85);
                imagedestroy($img);
                return;
            }
        }

        $gif1x1 = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        if ($gif1x1 !== false) {
            file_put_contents($fullPath, $gif1x1);
        }
    }
}
