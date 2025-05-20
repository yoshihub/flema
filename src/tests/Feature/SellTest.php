<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Condition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SellTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;
    private $categories;
    private $condition;

    public function setUp(): void
    {
        parent::setUp();

        // テスト用のストレージドライバを設定
        Storage::fake('public');

        // テストユーザーの作成
        $this->user = User::factory()->create();

        // テスト用のカテゴリーを作成
        $this->categories = [
            Category::factory()->create(['content' => 'テストカテゴリー1']),
            Category::factory()->create(['content' => 'テストカテゴリー2']),
        ];

        // テスト用の商品状態を作成
        $this->condition = Condition::factory()->create(['content' => '新品・未使用']);
    }

    /**
     * 商品出品画面にアクセスできることを確認するテスト
     */
    public function test_出品ページにアクセスできる()
    {
        // ユーザーとしてログイン
        $this->actingAs($this->user);

        // 商品出品ページにアクセス
        $response = $this->get('/sell');

        // レスポンスのステータスコードを確認
        $response->assertStatus(200);

        // 必要な要素が表示されているか確認
        $response->assertSee('商品の出品', false);
        $response->assertSee('商品画像', false);
        $response->assertSee('カテゴリー', false);
        $response->assertSee('商品の状態', false);
        $response->assertSee('商品名', false);
        $response->assertSee('商品の説明', false);
        $response->assertSee('販売価格', false);
        $response->assertSee('出品する', false);
    }

    /**
     * 商品出品情報が正しく保存されることを確認するテスト
     */
    public function test_商品出品情報が正しく保存される()
    {
        // ユーザーとしてログイン
        $this->actingAs($this->user);

        // GD拡張に依存しないダミーファイルを作成
        $image = UploadedFile::fake()->create(
            'test_product.jpg',
            100,
            'image/jpeg'
        );

        // 商品出品リクエストを送信
        $response = $this->post('/sell', [
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'explanation' => 'これはテスト用の商品説明です。',
            'price' => 5000,
            'categories' => [$this->categories[0]->id, $this->categories[1]->id],
            'condition' => $this->condition->id,
            'exhibition_image' => $image,
        ]);

        // リダイレクトとメッセージを確認
        $response->assertStatus(302);
        $response->assertSessionHas('message', '出品しました');

        // データベースに商品が登録されているか確認
        $this->assertDatabaseHas('exhibitions', [
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'explanation' => 'これはテスト用の商品説明です。',
            'price' => 5000,
            'condition_id' => $this->condition->id,
            'user_id' => $this->user->id,
        ]);

        // 最後に作成された商品を取得
        $exhibition = \App\Models\Exhibition::latest()->first();

        // 画像ファイル名が保存されているか確認
        $this->assertNotEmpty($exhibition->exhibition_image);

        // ファイル名のフォーマットを確認（通常はタイムスタンプ.jpg形式）
        $this->assertMatchesRegularExpression('/^\d+\.jpg$/', $exhibition->exhibition_image);

        // カテゴリーが正しく紐づけられているか確認
        $this->assertDatabaseHas('category_exhibition', [
            'exhibition_id' => $exhibition->id,
            'category_id' => $this->categories[0]->id,
        ]);
        $this->assertDatabaseHas('category_exhibition', [
            'exhibition_id' => $exhibition->id,
            'category_id' => $this->categories[1]->id,
        ]);
    }

    /**
     * 必須項目が不足している場合にバリデーションエラーが表示されることを確認するテスト
     */
    public function test_必須項目が不足している場合にエラーが表示される()
    {
        // ユーザーとしてログイン
        $this->actingAs($this->user);

        // 必須項目が不足したリクエストを送信
        $response = $this->from('/sell')->post('/sell', [
            'name' => '', // 商品名が空
            'brand' => 'テストブランド',
            'explanation' => '', // 商品説明が空
            'price' => '', // 価格が空
            'categories' => [], // カテゴリーが未選択
            'condition' => '', // 商品状態が未選択
            'exhibition_image' => null, // 画像が未選択
        ]);

        // バリデーションエラーと共に元のページにリダイレクトされるか確認
        $response->assertStatus(302);
        $response->assertRedirect('/sell');

        // バリデーションエラーが存在するか確認
        $response->assertSessionHasErrors([
            'name',
            'explanation',
            'price',
            'categories',
            'condition',
            'exhibition_image'
        ]);

        // データベースに商品が登録されていないか確認
        $this->assertEquals(0, \App\Models\Exhibition::count());
    }

    /**
     * カテゴリーが複数選択できることを確認するテスト
     */
    public function test_カテゴリーが複数選択できる()
    {
        // さらにテスト用のカテゴリーを追加
        $category3 = Category::factory()->create(['content' => 'テストカテゴリー3']);

        // ユーザーとしてログイン
        $this->actingAs($this->user);

        // GD拡張に依存しないダミーファイルを作成
        $image = UploadedFile::fake()->create(
            'test_product.jpg',
            100,
            'image/jpeg'
        );

        // 複数のカテゴリーを選択して商品出品リクエストを送信
        $response = $this->post('/sell', [
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'explanation' => 'これはテスト用の商品説明です。',
            'price' => 5000,
            'categories' => [$this->categories[0]->id, $this->categories[1]->id, $category3->id],
            'condition' => $this->condition->id,
            'exhibition_image' => $image,
        ]);

        // リダイレクトを確認
        $response->assertStatus(302);

        // 最後に作成された商品を取得
        $exhibition = \App\Models\Exhibition::latest()->first();

        // 全てのカテゴリーが正しく紐づけられているか確認
        $this->assertCount(3, $exhibition->categories);
        $this->assertTrue($exhibition->categories->contains($this->categories[0]));
        $this->assertTrue($exhibition->categories->contains($this->categories[1]));
        $this->assertTrue($exhibition->categories->contains($category3));
    }
}
