<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['category_name' => 'Electronics', 'description' => 'Electronic devices and gadgets'],
            ['category_name' => 'Clothing', 'description' => 'Fashion and apparel'],
            ['category_name' => 'Home & Garden', 'description' => 'Home improvement and garden supplies'],
            ['category_name' => 'Services', 'description' => 'Professional and personal services'],
            ['category_name' => 'Vehicles', 'description' => 'Cars, motorcycles and parts'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        $this->command->info('Created 5 categories');
    }
}
