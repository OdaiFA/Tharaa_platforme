<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'الراتب', 'type' => 'income', 'icon' => '💰'],
            ['name' => 'العمل الحر', 'type' => 'income', 'icon' => '💼'],
            ['name' => 'الاستثمارات', 'type' => 'income', 'icon' => '📈'],
            ['name' => 'الهدايا', 'type' => 'income', 'icon' => '🎁'],
            ['name' => 'مصروف الجيب', 'type' => 'income', 'icon' => '🪙'],
            ['name' => 'الأغذية والمشروبات', 'type' => 'expense', 'icon' => '🛒'],
            ['name' => 'المواصلات', 'type' => 'expense', 'icon' => '🚗'],
            ['name' => 'السكن', 'type' => 'expense', 'icon' => '🏠'],
            ['name' => 'التعليم', 'type' => 'expense', 'icon' => '📚'],
            ['name' => 'الترفيه', 'type' => 'expense', 'icon' => '🎮'],
            ['name' => 'الصحة', 'type' => 'expense', 'icon' => '🏥'],
            ['name' => 'الملابس', 'type' => 'expense', 'icon' => '👕'],
            ['name' => 'الاتصالات والإنترنت', 'type' => 'expense', 'icon' => '📱'],
            ['name' => 'المصاريف الأخرى', 'type' => 'expense', 'icon' => '📦'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name'], 'type' => $category['type']],
                array_merge($category, ['is_system' => true]),
            );
        }
    }
}
