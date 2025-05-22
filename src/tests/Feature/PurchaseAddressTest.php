<?php

namespace Tests\Feature;

use App\Models\Condition;
use App\Models\Exhibition;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseAddressTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $exhibition;
    private $userAddress;

    public function setUp(): void
    {
        parent::setUp();

        // テストユーザーの作成
        $this->user = User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
        ]);

        // ユーザーの住所情報を作成
        $this->userAddress = UserAddress::create([
            'postCode' => '123-4567',
            'address' => 'テスト県テスト市1-2-3',
            'building' => 'テストビル101',
            'user_id' => $this->user->id
        ]);

        // テスト用の商品を作成
        $condition = Condition::factory()->create();
        $seller = User::factory()->create();

        $this->exhibition = Exhibition::factory()->create([
            'name' => 'テスト商品',
            'price' => 5000,
            'user_id' => $seller->id, // 出品者は別のユーザー
            'condition_id' => $condition->id,
            'is_sold' => false
        ]);
    }

    /**
     * 送付先住所変更画面にて登録した住所が商品購入画面に反映されるかのテスト
     */
    public function test_updated_address_is_reflected_in_purchase_screen()
    {
        // ユーザーとしてログイン
        $this->actingAs($this->user);

        // 購入画面にアクセスして、初期値を確認
        $response = $this->get(route('purchase.index', $this->exhibition->id));
        $response->assertStatus(200);
        $response->assertSee($this->userAddress->postCode);
        $response->assertSee($this->userAddress->address);
        $response->assertSee($this->userAddress->building);

        // 新しい住所情報
        $newAddressData = [
            'name' => $this->user->name,
            'postCode' => '987-6543',
            'address' => '新県新市9-8-7',
            'building' => '新ビル999',
            'exhibition_id' => $this->exhibition->id
        ];

        // 住所変更ページにアクセス
        $response = $this->get(route('purchase.address.index', $this->exhibition->id));
        $response->assertStatus(200);

        // 住所を更新
        $response = $this->post(route('purchase.address.store'), $newAddressData);
        $response->assertRedirect(route('purchase.index', $this->exhibition->id));
        $response->assertSessionHas('message', '住所を更新しました');

        // 更新後の購入画面にアクセスして、変更された住所が表示されているか確認
        $response = $this->get(route('purchase.index', $this->exhibition->id));
        $response->assertStatus(200);
        $response->assertSee($newAddressData['postCode']);
        $response->assertSee($newAddressData['address']);
        $response->assertSee($newAddressData['building']);

        // データベースにも反映されていることを確認
        $this->assertDatabaseHas('user_addresses', [
            'user_id' => $this->user->id,
            'postCode' => $newAddressData['postCode'],
            'address' => $newAddressData['address'],
            'building' => $newAddressData['building']
        ]);
    }

    /**
     * 購入した商品に送付先住所が紐づいて登録されるかのテスト
     */
    public function test_purchase_is_created_with_correct_shipping_address()
    {
        // ユーザーとしてログイン
        $this->actingAs($this->user);

        // 新しい住所情報
        $newAddressData = [
            'name' => $this->user->name,
            'postCode' => '987-6543',
            'address' => '新県新市9-8-7',
            'building' => '新ビル999',
            'exhibition_id' => $this->exhibition->id
        ];

        // 住所を更新
        $this->post(route('purchase.address.store'), $newAddressData);

        // 購入情報
        $purchaseData = [
            'payment' => 'カード払い',
            'postCode' => $newAddressData['postCode'],
            'address' => $newAddressData['address'],
            'building' => $newAddressData['building'],
            'exhibition_id' => $this->exhibition->id
        ];

        // 商品を購入
        $response = $this->post(route('purchase.store'), $purchaseData);
        $response->assertSessionHas('message', '購入しました');

        // 購入データが正しく保存されているか確認
        $this->assertDatabaseHas('purchases', [
            'user_id' => $this->user->id,
            'exhibition_id' => $this->exhibition->id,
            'payment' => 'カード払い',
            'postCode' => $newAddressData['postCode'],
            'address' => $newAddressData['address'],
            'building' => $newAddressData['building']
        ]);

        // 商品のis_soldフラグが更新されているか確認
        $this->assertDatabaseHas('exhibitions', [
            'id' => $this->exhibition->id,
            'is_sold' => true
        ]);
    }
}
