<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('conditions')->insert([
            [
                'content' => '良好',
            ],
            [
                'content' => '目立った傷や汚れなし',
            ],
            [
                'content' => 'やや傷や汚れあり',
            ],
            [
                'content' => '状態が悪い',
            ],
        ]);
    }
}
