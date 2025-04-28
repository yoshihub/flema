<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExhibitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('exhibitions')->insert([
            [
                'name' => '腕時計',
                'price' => 15000,
                'explanation' => 'スタイリッシュなデザインのメンズ腕時計',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
                // 'condition_id' => 1,
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'explanation' => '高速で信頼性の高いハードディスク',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',
                // 'condition_id' => 2,
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'explanation' => '新鮮な玉ねぎ3束のセット',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',
                // 'condition_id' => 3,
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'explanation' => 'クラシックなデザインの革靴',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg',
                // 'condition_id' => 4,
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'explanation' => '高性能なノートパソコン',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg',
                // 'condition_id' => 1,
            ],
            [
                'name' => 'マイク',
                'price' => 8000,
                'explanation' => '高音質のレコーディング用マイク',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg',
                // 'condition_id' => 2,
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'explanation' => 'おしゃれなショルダーバッグ',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
                // 'condition_id' => 3,
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'explanation' => '使いやすいタンブラー',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg',
                // 'condition_id' => 4,
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'explanation' => '手動のコーヒーミル',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
                // 'condition_id' => 1,
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'explanation' => '便利なメイクアップセット',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/外出メイクアップセット.jpg',
                // 'condition_id' => 2
            ],
        ]);
    }
}
