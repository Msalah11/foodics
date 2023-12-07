<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $initialSeederData = [
            [
                'id' => 1,
                'name' => 'Beef',
                'original_stock' => 20 * 1000,
                'current_stock' => 20 * 1000,
                'merchant_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'Cheese',
                'original_stock' => 5 * 1000,
                'current_stock' => 5 * 1000,
                'merchant_id' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'Onion',
                'original_stock' => 1000,
                'current_stock' => 1000,
                'merchant_id' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ];

        DB::table('ingredients')->insert($initialSeederData);
    }
}
