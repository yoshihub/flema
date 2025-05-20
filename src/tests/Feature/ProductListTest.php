<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Exhibition;

class ProductListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * テストユーザー
     */
    protected $user;

    /**
     * 別のテストユーザー
     */
    protected $otherUser;

    /**
     * テスト実行前の準備
     */
    protected function setUp(): void
    {
        parent::setUp();

        // テストユーザーの作成
        $this->user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
        ]);

        // 別のユーザーを作成（他のユーザーの商品を表示するため）
        $this->otherUser = User::factory()->create();
    }

    /**
     * すべての商品が表示されるテスト
     *
     * @return void
     */
    public function test_all_products_are_displayed()
    {
        // 複数の商品を作成
        $product1 = Exhibition::factory()->create(['name' => '商品1']);
        $product2 = Exhibition::factory()->create(['name' => '商品2']);
        $product3 = Exhibition::factory()->create(['name' => '商品3']);

        // 商品一覧ページにアクセス
        $response = $this->get('/');

        // ステータスコードの確認
        $response->assertStatus(200);

        // すべての商品が表示されていることを確認
        $response->assertSee('商品1');
        $response->assertSee('商品2');
        $response->assertSee('商品3');
    }

    /**
     * 購入済み商品は「Sold」と表示されるテスト
     *
     * @return void
     */
    public function test_sold_products_are_marked_as_sold()
    {
        // 商品を作成
        $product1 = Exhibition::factory()->create(['name' => '通常商品']);

        // 購入済み商品を作成
        $soldProduct = Exhibition::factory()->create([
            'name' => '購入済み商品',
            'is_sold' => true
        ]);

        // 商品一覧ページにアクセス
        $response = $this->get('/');

        // ステータスコードの確認
        $response->assertStatus(200);

        // 通常商品と購入済み商品が表示されていることを確認
        $response->assertSee('通常商品');
        $response->assertSee('購入済み商品');

        // 購入済み商品に「Sold」ラベルが表示されていることを確認
        $response->assertSee('Sold');
    }

    /**
     * 自分が出品した商品は表示されないテスト
     *
     * @return void
     */
    public function test_own_products_are_not_displayed()
    {
        // ログインユーザーの商品を作成
        $ownProduct = Exhibition::factory()->create([
            'name' => '自分の商品',
            'user_id' => $this->user->id
        ]);

        // 他のユーザーの商品を作成
        $otherProduct = Exhibition::factory()->create([
            'name' => '他人の商品',
            'user_id' => $this->otherUser->id
        ]);

        // ユーザーとしてログイン
        $this->actingAs($this->user);

        // 商品一覧ページにアクセス
        $response = $this->get('/');

        // ステータスコードの確認
        $response->assertStatus(200);

        // 他のユーザーの商品は表示され、自分の商品は表示されないことを確認
        $response->assertDontSee('自分の商品');
        $response->assertSee('他人の商品');
    }
}
