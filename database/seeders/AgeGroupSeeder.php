<?php

namespace Database\Seeders;

use App\Models\AgeGroup;
use Illuminate\Database\Seeder;

class AgeGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            ['name' => 'أطفال (7-12 سنة)', 'min_age' => 7, 'max_age' => 12],
            ['name' => 'مراهقون (13-17 سنة)', 'min_age' => 13, 'max_age' => 17],
            ['name' => 'شباب (18-24 سنة)', 'min_age' => 18, 'max_age' => 24],
            ['name' => 'بالغون (25-40 سنة)', 'min_age' => 25, 'max_age' => 40],
            ['name' => 'كبار (41+ سنة)', 'min_age' => 41, 'max_age' => 120],
        ];

        foreach ($groups as $group) {
            AgeGroup::firstOrCreate(
                ['name' => $group['name']],
                $group,
            );
        }
    }
}
