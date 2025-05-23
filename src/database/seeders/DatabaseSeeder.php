<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CategorySeeder::class,
            ConditionSeeder::class,
        ]);

        // その他のユーザーをファクトリーで作成
        User::factory(5)->create();

        // 特定のユーザーが存在しない場合のみ作成
        if (!User::where('email', 'aaa@aaa.com')->exists()) {
            $user = User::create([
                'name' => 'aaa',
                'email' => 'aaa@aaa.com',
                'password' => bcrypt('hogehoge1'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);

            UserAddress::create([
                'postCode' => '123-4567',
                'address' => 'テスト県テスト市1-2-3',
                'building' => 'テストビル101',
                'user_id' => $user->id
            ]);
        }

        $this->call(ExhibitionSeeder::class);
    }
}
