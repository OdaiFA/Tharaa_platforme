<?php

namespace Database\Seeders;

use App\Models\ArticleCategory;
use Illuminate\Database\Seeder;

class ArticleCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'الثقافة المالية', 'slug' => 'financial-literacy', 'description' => 'أساسيات الثقافة المالية'],
            ['name' => 'الادخار', 'slug' => 'saving', 'description' => 'مهارات الادخار'],
            ['name' => 'الميزانية', 'slug' => 'budgeting', 'description' => 'إدارة الميزانية الشخصية'],
            ['name' => 'الاستثمار', 'slug' => 'investing', 'description' => 'أساسيات الاستثمار'],
            ['name' => 'ريادة الأعمال', 'slug' => 'entrepreneurship', 'description' => 'ريادة الأعمال والمشاريع'],
        ];

        foreach ($categories as $category) {
            ArticleCategory::firstOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
