<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $tree = [
            'Panjabi' => ['Regular Panjabi', 'Premium Panjabi', 'Wedding Panjabi'],
            'Attar' => ['Alcohol Free', 'Imported', 'Local'],
            'Bottomwear' => ['Pant', 'Shorts'],
            'T-Shirt' => ['Sports T-Shirt', 'Casual T-Shirt', 'Polo T-Shirt'],
            'Accessories' => ['Tupi', 'Belt'],
        ];

        $sortOrder = 0;
        foreach ($tree as $parentName => $childNames) {
            $sortOrder++;
            $parentSlug = Str::slug($parentName);
            $parent = Category::updateOrCreate(
                ['slug' => $parentSlug],
                [
                    'name' => $parentName,
                    'parent_id' => null,
                    'sort_order' => $sortOrder,
                    'is_active' => true,
                ]
            );

            $childSort = 0;
            foreach ($childNames as $childName) {
                $childSort++;
                $childSlug = Str::slug($childName);
                Category::updateOrCreate(
                    ['slug' => $childSlug],
                    [
                        'name' => $childName,
                        'parent_id' => $parent->id,
                        'sort_order' => $childSort,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
