<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MerchantSeeder extends Seeder
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
                'name' => 'Beef merchant',
                'email' => 'beef@example.com',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'Cheese merchant',
                'email' => 'cheese@example.com',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'Onion merchant',
                'email' => 'onion@example.com',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ];

        DB::table('merchants')->insert($initialSeederData);
    }
}
