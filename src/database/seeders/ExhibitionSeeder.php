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
                'exhibition_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
                'condition_id' => 1,
                'user_id' => 1,
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'explanation' => '高速で信頼性の高いハードディスク',
                'exhibition_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',
                'condition_id' => 2,
                'user_id' => 1,
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'explanation' => '新鮮な玉ねぎ3束のセット',
                'exhibition_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',
                'condition_id' => 3,
                'user_id' => 1,
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'explanation' => 'クラシックなデザインの革靴',
                'exhibition_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg',
                'condition_id' => 4,
                'user_id' => 1,
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'explanation' => '高性能なノートパソコン',
                'exhibition_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg',
                'condition_id' => 1,
                'user_id' => 1,
            ],
            [
                'name' => 'マイク',
                'price' => 8000,
                'explanation' => '高音質のレコーディング用マイク',
                'exhibition_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg',
                'condition_id' => 2,
                'user_id' => 1,
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'explanation' => 'おしゃれなショルダーバッグ',
                'exhibition_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
                'condition_id' => 3,
                'user_id' => 2,
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'explanation' => '使いやすいタンブラー',
                'exhibition_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg',
                'condition_id' => 4,
                'user_id' => 2,
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'explanation' => '手動のコーヒーミル',
                'exhibition_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
                'condition_id' => 1,
                'user_id' => 2,
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'explanation' => '便利なメイクアップセット',
                'exhibition_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/外出メイクアップセット.jpg',
                'condition_id' => 2,
                'user_id' => 3,
            ],
        ]);

        DB::table('category_exhibition')->insert([
            [
                'exhibition_id' => 1,
                'category_id' => 1,
            ],
            [
                'exhibition_id' => 2,
                'category_id' => 1,
            ],
            [
                'exhibition_id' => 3,
                'category_id' => 1,
            ],
            [
                'exhibition_id' => 4,
                'category_id' => 1,
            ],
            [
                'exhibition_id' => 5,
                'category_id' => 1,
            ],
            [
                'exhibition_id' => 6,
                'category_id' => 1,
            ],
            [
                'exhibition_id' => 7,
                'category_id' => 1,
            ],
            [
                'exhibition_id' => 8,
                'category_id' => 1,
            ],
            [
                'exhibition_id' => 9,
                'category_id' => 2,
            ],
            [
                'exhibition_id' => 10,
                'category_id' => 1,
            ],
            [
                'exhibition_id' => 10,
                'category_id' => 3,
            ],
        ]);
    }
}
