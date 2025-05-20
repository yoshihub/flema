<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Condition;
use App\Models\Exhibition;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentSelectionTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $exhibition;
    private $address;

    public function setUp(): void
    {
        parent::setUp();

        // テストユーザーの作成
        $this->user = User::factory()->create();

        // ユーザーの住所情報を作成
        $this->address = UserAddress::create([
            'postCode' => '123-4567',
            'address' => 'テスト県テスト市1-2-3',
            'building' => 'テストビル101',
            'user_id' => $this->user->id
        ]);

        // 商品出品者
        $seller = User::factory()->create();

        // 商品に必要なデータの準備
        $condition = Condition::factory()->create();
        $category = Category::factory()->create();

        // テスト用の商品を作成
        $this->exhibition = Exhibition::factory()->create([
            'name' => 'テスト商品',
            'price' => 5000,
            'user_id' => $seller->id,
            'condition_id' => $condition->id
        ]);

        // カテゴリを商品に紐付け
        $this->exhibition->categories()->attach($category->id);
    }

    /**
     * 支払い方法が選択されているかのバリデーションが機能するかテスト
     */
    public function test_支払い方法が未選択の場合エラーメッセージが表示される()
    {
        // ユーザーとしてログイン
        $this->actingAs($this->user);

        // 支払い方法が未選択の状態で購入リクエストを送信
        $response = $this->from(route('purchase.index', $this->exhibition->id))
            ->post(route('purchase.store'), [
                'payment' => '', // 空の支払い方法
                'postCode' => '123-4567',
                'address' => 'テスト県テスト市1-2-3',
                'building' => 'テストビル101',
                'exhibition_id' => $this->exhibition->id
            ]);

        // エラーメッセージを確認
        $response->assertSessionHasErrors('payment');
        $response->assertSessionHasErrors([
            'payment' => '支払い方法を入力してください'
        ]);

        // 購入ページにリダイレクトされるか確認
        $response->assertRedirect(route('purchase.index', $this->exhibition->id));
    }
}
