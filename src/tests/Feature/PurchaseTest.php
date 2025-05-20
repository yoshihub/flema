<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Condition;
use App\Models\Exhibition;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $seller;
    private $exhibition;
    private $address;

    public function setUp(): void
    {
        parent::setUp();

        // 購入者ユーザーの作成
        $this->user = User::factory()->create();

        // 購入者の住所情報を作成
        $this->address = UserAddress::create([
            'postCode' => '123-4567',
            'address' => 'テスト県テスト市1-2-3',
            'building' => 'テストビル101',
            'user_id' => $this->user->id
        ]);

        // 出品者ユーザーの作成
        $this->seller = User::factory()->create();

        // 商品に必要なデータの準備
        $condition = Condition::factory()->create();
        $category = Category::factory()->create();

        // テスト用の商品を作成
        $this->exhibition = Exhibition::factory()->create([
            'name' => 'テスト商品',
            'price' => 5000,
            'user_id' => $this->seller->id,
            'condition_id' => $condition->id,
            'is_sold' => false
        ]);

        // カテゴリを商品に紐付け
        $this->exhibition->categories()->attach($category->id);
    }

    /**
     * 「購入する」ボタンを押下すると購入が完了する
     */
    public function test_ユーザーが商品を購入できる()
    {
        // ユーザーとしてログイン
        $this->actingAs($this->user);

        // 商品購入リクエストを送信
        $response = $this->post(route('purchase.store'), [
            'payment' => 'コンビニ払い',
            'postCode' => '123-4567',
            'address' => 'テスト県テスト市1-2-3',
            'building' => 'テストビル101',
            'exhibition_id' => $this->exhibition->id
        ]);

        // リダイレクトを確認
        $response->assertStatus(302);
        $response->assertSessionHas('message', '購入しました');

        // データベースに購入情報が登録されているか確認
        $this->assertDatabaseHas('purchases', [
            'payment' => 'コンビニ払い',
            'postCode' => '123-4567',
            'address' => 'テスト県テスト市1-2-3',
            'building' => 'テストビル101',
            'exhibition_id' => $this->exhibition->id,
            'user_id' => $this->user->id
        ]);

        // 商品のis_soldフラグがtrueになっているか確認
        $this->assertDatabaseHas('exhibitions', [
            'id' => $this->exhibition->id,
            'is_sold' => true
        ]);
    }

    /**
     * 購入した商品は商品一覧画面にて「sold」と表示される
     */
    public function test_購入した商品は一覧画面でsoldと表示される()
    {
        // ユーザーとしてログイン
        $this->actingAs($this->user);

        // 商品を購入
        $this->post(route('purchase.store'), [
            'payment' => 'コンビニ払い',
            'postCode' => '123-4567',
            'address' => 'テスト県テスト市1-2-3',
            'building' => 'テストビル101',
            'exhibition_id' => $this->exhibition->id
        ]);

        // 商品一覧画面にアクセス
        $response = $this->get(route('exhibition.index'));

        // レスポンスのステータスコードを確認
        $response->assertStatus(200);

        // 商品名とSoldタグが表示されているか確認
        $response->assertSee($this->exhibition->name, false);
        $response->assertSee('<span class="sold">Sold</span>', false);
    }

    /**
     * 「プロフィール/購入した商品一覧」に追加されている
     */
    public function test_購入した商品はマイページの購入一覧に表示される()
    {
        // ユーザーとしてログイン
        $this->actingAs($this->user);

        // 商品を購入
        $this->post(route('purchase.store'), [
            'payment' => 'コンビニ払い',
            'postCode' => '123-4567',
            'address' => 'テスト県テスト市1-2-3',
            'building' => 'テストビル101',
            'exhibition_id' => $this->exhibition->id
        ]);

        // マイページの購入一覧にアクセス
        $response = $this->get('/mypage?tab=buy');

        // レスポンスのステータスコードを確認
        $response->assertStatus(200);

        // 購入した商品名が表示されているか確認
        $response->assertSee($this->exhibition->name, false);

        // 「購入した商品」タブがアクティブになっているか確認
        $response->assertSee('class="purchase-link active"', false);
    }
}
