<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Condition;
use App\Models\Exhibition;
use App\Models\Purchase;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $address;
    private $sellExhibition;
    private $buyExhibition;

    public function setUp(): void
    {
        parent::setUp();

        // テスト用のストレージドライバを設定
        Storage::fake('public');

        // テストユーザーの作成
        $this->user = User::factory()->create([
            'name' => 'テストユーザー',
            'profile_image' => 'test_profile.jpg'
        ]);

        // ユーザーの住所情報を作成
        $this->address = UserAddress::create([
            'postCode' => '123-4567',
            'address' => 'テスト県テスト市1-2-3',
            'building' => 'テストビル101',
            'user_id' => $this->user->id
        ]);

        // 出品用の商品データを準備
        $condition = Condition::factory()->create();
        $category = Category::factory()->create();

        // 出品した商品を作成
        $this->sellExhibition = Exhibition::factory()->create([
            'name' => '出品したテスト商品',
            'price' => 5000,
            'user_id' => $this->user->id,
            'condition_id' => $condition->id
        ]);

        // カテゴリを商品に紐付け
        $this->sellExhibition->categories()->attach($category->id);

        // 他のユーザーの商品を作成（購入用）
        $seller = User::factory()->create();
        $this->buyExhibition = Exhibition::factory()->create([
            'name' => '購入したテスト商品',
            'price' => 3000,
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'is_sold' => true
        ]);

        // 購入情報を作成
        Purchase::create([
            'payment' => 'コンビニ払い',
            'postCode' => '123-4567',
            'address' => 'テスト県テスト市1-2-3',
            'building' => 'テストビル101',
            'exhibition_id' => $this->buyExhibition->id,
            'user_id' => $this->user->id
        ]);
    }

    /**
     * ユーザーマイページに必要な情報が表示されることを確認するテスト
     */
    public function test_マイページにユーザー情報が表示される()
    {
        // ユーザーとしてログイン
        $this->actingAs($this->user);

        // マイページにアクセス
        $response = $this->get('/mypage');

        // レスポンスのステータスコードを確認
        $response->assertStatus(200);

        // ユーザー名が表示されているか確認
        $response->assertSee($this->user->name, false);

        // プロフィール画像パスが含まれているか確認
        $response->assertSee('storage/profile_images/' . $this->user->profile_image, false);

        // 「プロフィールを編集」リンクが表示されているか確認
        $response->assertSee('プロフィールを編集', false);

        // タブリンクが表示されているか確認
        $response->assertSee('出品した商品', false);
        $response->assertSee('購入した商品', false);
    }

    /**
     * 出品した商品一覧が正しく表示されることを確認するテスト
     */
    public function test_出品した商品一覧が表示される()
    {
        // ユーザーとしてログイン
        $this->actingAs($this->user);

        // 出品商品タブを指定してマイページにアクセス
        $response = $this->get('/mypage?tab=sell');

        // レスポンスのステータスコードを確認
        $response->assertStatus(200);

        // 出品した商品が表示されているか確認
        $response->assertSee($this->sellExhibition->name, false);

        // 「出品した商品」タブがアクティブになっているか確認
        $response->assertSee('class="sell-link active"', false);
    }

    /**
     * 購入した商品一覧が正しく表示されることを確認するテスト
     */
    public function test_購入した商品一覧が表示される()
    {
        // ユーザーとしてログイン
        $this->actingAs($this->user);

        // 購入商品タブを指定してマイページにアクセス
        $response = $this->get('/mypage?tab=buy');

        // レスポンスのステータスコードを確認
        $response->assertStatus(200);

        // 購入した商品が表示されているか確認
        $response->assertSee($this->buyExhibition->name, false);

        // 「購入した商品」タブがアクティブになっているか確認
        $response->assertSee('class="purchase-link active"', false);
    }

    /**
     * プロフィール編集ページに正しい情報が表示されることを確認するテスト
     */
    public function test_プロフィール編集ページに正しい情報が表示される()
    {
        // ユーザーとしてログイン
        $this->actingAs($this->user);

        // プロフィール編集ページにアクセス
        $response = $this->get('/mypage/profile');

        // レスポンスのステータスコードを確認
        $response->assertStatus(200);

        // ユーザー名が表示されているか確認
        $response->assertSee('value="' . $this->user->name . '"', false);

        // 住所情報が表示されているか確認
        $response->assertSee('value="' . $this->address->postCode . '"', false);
        $response->assertSee('value="' . $this->address->address . '"', false);
        $response->assertSee('value="' . $this->address->building . '"', false);

        // プロフィール画像パスが含まれているか確認
        $response->assertSee('storage/profile_images/' . $this->user->profile_image, false);
    }
}
