<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Condition;
use App\Models\Exhibition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $exhibition;

    public function setUp(): void
    {
        parent::setUp();

        // テストユーザーの作成
        $this->user = User::factory()->create();

        // 商品出品者
        $seller = User::factory()->create();

        // 商品に必要なデータの準備
        $condition = Condition::factory()->create();
        $category = Category::factory()->create();

        // テスト用の商品を作成
        $this->exhibition = Exhibition::factory()->create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
        ]);

        // カテゴリを商品に紐付け
        $this->exhibition->categories()->attach($category->id);
    }

    /**
     * いいねアイコンを押下することによって、いいねした商品として登録することができる
     */
    public function test_ユーザーがいいねを登録できる()
    {
        // ユーザーとしてログイン
        $this->actingAs($this->user);

        // いいね登録リクエストを送信
        $response = $this->post(route('favorite', $this->exhibition->id));

        // リダイレクトを確認
        $response->assertStatus(302);

        // データベースにいいねが登録されているか確認
        $this->assertDatabaseHas('exhibition_user', [
            'user_id' => $this->user->id,
            'exhibition_id' => $this->exhibition->id,
        ]);

        // 商品詳細ページにアクセスして内容を確認
        $showResponse = $this->get(route('exhibition.show', $this->exhibition->id));

        // いいねアイコンが色付きになっているか確認（星アイコンが黄色になっているかのクラスを確認）
        $showResponse->assertSee('fa-solid fa-star', false);
        $showResponse->assertSee('style="color: #FFD700;"', false);

        // いいね数が1になっているか確認
        $showResponse->assertSee('<span class="action-count">1</span>', false);
    }

    /**
     * 追加済みのアイコンは色が変化する
     */
    public function test_いいね済みの場合はアイコンの色が変化している()
    {
        // ユーザーとしてログイン
        $this->actingAs($this->user);

        // あらかじめいいねを登録しておく
        $this->exhibition->users()->attach($this->user->id);

        // 商品詳細ページにアクセス
        $response = $this->get(route('exhibition.show', $this->exhibition->id));

        // いいねアイコンが色付きになっているか確認
        $response->assertSee('fa-solid fa-star', false);
        $response->assertSee('style="color: #FFD700;"', false);
    }

    /**
     * 再度いいねアイコンを押下することによって、いいねを解除することができる
     */
    public function test_いいねを解除できる()
    {
        // ユーザーとしてログイン
        $this->actingAs($this->user);

        // あらかじめいいねを登録しておく
        $this->exhibition->users()->attach($this->user->id);

        // いいね解除リクエストを送信
        $response = $this->post(route('unfavorite', $this->exhibition->id));

        // リダイレクトを確認
        $response->assertStatus(302);

        // データベースからいいねが削除されているか確認
        $this->assertDatabaseMissing('exhibition_user', [
            'user_id' => $this->user->id,
            'exhibition_id' => $this->exhibition->id,
        ]);

        // 商品詳細ページにアクセスして内容を確認
        $showResponse = $this->get(route('exhibition.show', $this->exhibition->id));

        // いいねアイコンが通常状態に戻っているか確認
        $showResponse->assertSee('fa-regular fa-star', false);

        // いいね数が0になっているか確認
        $showResponse->assertSee('<span class="action-count">0</span>', false);
    }
}
