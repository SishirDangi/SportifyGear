<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => 'Pending',    'order' => 1],
            ['name' => 'Confirmed',  'order' => 2],
            ['name' => 'Processing', 'order' => 3],
            ['name' => 'Shipped',    'order' => 4],
            ['name' => 'Delivered',  'order' => 5],
            ['name' => 'Cancelled',  'order' => 99],
            ['name' => 'Returned',   'order' => 100],
        ];

        DB::table('statuses')->insert($statuses);
    }
}
