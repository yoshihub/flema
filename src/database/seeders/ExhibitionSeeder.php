<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\UserAddress;

class ExhibitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // ダミーユーザーを3件作成（出品者A・出品者B・未紐づけユーザー）
        $sellerA = User::firstOrCreate(
            ['email' => 'seller1@example.com'],
            [
                'name' => 'Seller One',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        // 出品者Aの住所（なければ作成）
        UserAddress::firstOrCreate(
            ['user_id' => $sellerA->id],
            [
                'postCode' => '100-0001',
                'address' => '東京都千代田区1-1-1',
                'building' => 'テストAビル101',
            ]
        );

        $sellerB = User::firstOrCreate(
            ['email' => 'seller2@example.com'],
            [
                'name' => 'Seller Two',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        // 出品者Bの住所（なければ作成）
        UserAddress::firstOrCreate(
            ['user_id' => $sellerB->id],
            [
                'postCode' => '150-0001',
                'address' => '東京都渋谷区1-2-3',
                'building' => 'テストBビル202',
            ]
        );

        // 紐づけなしのユーザー（要件上作成のみ）
        $standalone = User::firstOrCreate(
            ['email' => 'standalone@example.com'],
            [
                'name' => 'Standalone User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        // 紐づけなしユーザーの住所（なければ作成）
        UserAddress::firstOrCreate(
            ['user_id' => $standalone->id],
            [
                'postCode' => '160-0001',
                'address' => '東京都新宿区1-3-5',
                'building' => 'スタンドアロンビル303',
            ]
        );

        $files = [
            [
                'name' => '腕時計',
                'image' => 'Clock.jpg',
                'price' => 15000,
                'explanation' => 'スタイリッシュなデザインのメンズ腕時計',
                'condition' => 1,
            ],
            [
                'name' => 'HDD',
                'image' => 'HardDisk.jpg',
                'price' => 5000,
                'explanation' => '高速で信頼性の高いハードディスク',
                'condition' => 2,
            ],
            [
                'name' => '玉ねぎ3束',
                'image' => 'Vegetable.jpg',
                'price' => 300,
                'explanation' => '新鮮な玉ねぎ3束のセット',
                'condition' => 3,
            ],
            [
                'name' => '革靴',
                'image' => 'LeatherShoes.jpg',
                'price' => 4000,
                'explanation' => 'クラシックなデザインの革靴',
                'condition' => 4,
            ],
            [
                'name' => 'ノートPC',
                'image' => 'NotePC.jpg',
                'price' => 45000,
                'explanation' => '高性能なノートパソコン',
                'condition' => 1,
            ],
            [
                'name' => 'マイク',
                'image' => 'MusicMic.jpg',
                'price' => 8000,
                'explanation' => '高音質のレコーディング用マイク',
                'condition' => 2,
            ],
            [
                'name' => 'ショルダーバッグ',
                'image' => 'RedBag.jpg',
                'price' => 3500,
                'explanation' => 'おしゃれなショルダーバッグ',
                'condition' => 3,
            ],
            [
                'name' => 'タンブラー',
                'image' => 'Tumbler.jpg',
                'price' => 500,
                'explanation' => '使いやすいタンブラー',
                'condition' => 4,
            ],
            [
                'name' => 'コーヒーミル',
                'image' => 'Coffee.jpg',
                'price' => 4000,
                'explanation' => '手動のコーヒーミル',
                'condition' => 1,
            ],
            [
                'name' => 'メイクセット',
                'image' => 'Cosme.jpg',
                'price' => 2500,
                'explanation' => '便利なメイクアップセット',
                'condition' => 2,
            ],
        ];

        foreach ($files as $index => $file) {
            // 元画像のパス（public/sample_images/時計.jpg）
            $sourcePath = public_path('sample_images/' . $file['image']);

            // 保存先のファイル名をユニークにする
            $filename = time() . '_' . Str::random(5) . '_' . $file['image'];

            // storage/app/public/exhibition_images にコピー
            Storage::disk('public')->put('exhibition_images/' . $filename, file_get_contents($sourcePath));

            // exhibitions テーブルに保存
            DB::table('exhibitions')->insert([
                'name' => $file['name'],
                'price' => $file['price'],
                'explanation' => $file['explanation'],
                'exhibition_image' => $filename,
                'condition_id' => $file['condition'],
                // 先頭5件は sellerA、後半5件は sellerB に紐づけ
                'user_id' => $index < 5 ? $sellerA->id : $sellerB->id,
            ]);

            // 遅延させてファイル名が重複しないようにする
            usleep(200000); // 0.2秒
        }

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
