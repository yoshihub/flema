<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Exhibition;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExhibitionShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_必要な情報が表示される()
    {
        // テストデータの準備
        $user = User::factory()->create();
        $condition = Condition::factory()->create();

        // カテゴリの作成
        $category1 = Category::factory()->create(['content' => 'カテゴリA']);
        $category2 = Category::factory()->create(['content' => 'カテゴリB']);

        // 商品の作成
        $exhibition = Exhibition::factory()->create([
            'name' => 'テスト商品',
            'exhibition_image' => 'test_image.jpg',
            'brand' => 'テストブランド',
            'explanation' => 'これはテスト商品の説明です',
            'price' => 10000,
            'user_id' => $user->id,
            'condition_id' => $condition->id,
        ]);

        // カテゴリを商品に紐付け
        $exhibition->categories()->attach([$category1->id, $category2->id]);

        // コメントの追加
        $commentUser = User::factory()->create();
        $comment = Comment::factory()->create([
            'content' => 'テストコメント',
            'user_id' => $commentUser->id,
            'exhibition_id' => $exhibition->id,
        ]);

        // いいねを追加
        $likeUser = User::factory()->create();
        $exhibition->users()->attach($likeUser->id);

        // 商品詳細ページへのアクセス
        $response = $this->get(route('exhibition.show', $exhibition->id));

        // レスポンスの確認
        $response->assertStatus(200);

        // 商品の基本情報が表示されていることを確認
        $response->assertSee($exhibition->name);
        $response->assertSee($exhibition->brand);
        $response->assertSee(number_format($exhibition->price));
        $response->assertSee($exhibition->explanation);
        $response->assertSee($condition->content);

        // カテゴリが表示されていることを確認
        $response->assertSee($category1->content);
        $response->assertSee($category2->content);

        // コメントが表示されていることを確認
        $response->assertSee($comment->content);
        $response->assertSee($commentUser->name);

        // いいね数が表示されていることを確認
        $response->assertSee('1');
    }

    public function test_複数選択されたカテゴリが表示されている()
    {
        // テストデータの準備
        $user = User::factory()->create();
        $condition = Condition::factory()->create();

        // 複数のカテゴリを作成
        $category1 = Category::factory()->create(['content' => 'カテゴリX']);
        $category2 = Category::factory()->create(['content' => 'カテゴリY']);
        $category3 = Category::factory()->create(['content' => 'カテゴリZ']);

        // 商品の作成
        $exhibition = Exhibition::factory()->create([
            'user_id' => $user->id,
            'condition_id' => $condition->id,
        ]);

        // 複数のカテゴリを商品に紐付け
        $exhibition->categories()->attach([$category1->id, $category2->id, $category3->id]);

        // 商品詳細ページへのアクセス
        $response = $this->get(route('exhibition.show', $exhibition->id));

        // レスポンスの確認
        $response->assertStatus(200);

        // すべてのカテゴリが表示されていることを確認
        $response->assertSee($category1->content);
        $response->assertSee($category2->content);
        $response->assertSee($category3->content);
    }
}
