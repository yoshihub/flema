<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Condition;
use App\Models\Exhibition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
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
     * ログイン済みのユーザーはコメントを送信できる
     */
    public function test_ログイン済みユーザーはコメントを送信できる()
    {
        // ユーザーとしてログイン
        $this->actingAs($this->user);

        // コメント投稿リクエストを送信
        $response = $this->post(route('comments.store', $this->exhibition->id), [
            'content' => 'テストコメントです'
        ]);

        // リダイレクトを確認
        $response->assertStatus(302);
        $response->assertSessionHas('message', 'コメントしました');

        // データベースにコメントが登録されているか確認
        $this->assertDatabaseHas('comments', [
            'content' => 'テストコメントです',
            'user_id' => $this->user->id,
            'exhibition_id' => $this->exhibition->id,
        ]);

        // 商品詳細ページにアクセスして内容を確認
        $showResponse = $this->get(route('exhibition.show', $this->exhibition->id));

        // コメントが表示されているか確認
        $showResponse->assertSee('テストコメントです', false);

        // コメント数が1になっているか確認
        $showResponse->assertSee('コメント(1)', false);
    }

    /**
     * ログイン前のユーザーはコメントを送信できない
     */
    public function test_未ログインユーザーはコメントを送信できない()
    {
        // 未ログイン状態でコメント投稿リクエストを送信
        $response = $this->post(route('comments.store', $this->exhibition->id), [
            'content' => 'テストコメントです'
        ]);

        // ログインページにリダイレクトされるか確認
        $response->assertRedirect(route('login'));

        // データベースにコメントが登録されていないか確認
        $this->assertDatabaseMissing('comments', [
            'content' => 'テストコメントです',
            'exhibition_id' => $this->exhibition->id,
        ]);
    }

    /**
     * コメントが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_コメントが空の場合バリデーションエラーが表示される()
    {
        // ユーザーとしてログイン
        $this->actingAs($this->user);

        // 空のコメントでリクエストを送信
        $response = $this->from(route('exhibition.show', $this->exhibition->id))
            ->post(route('comments.store', $this->exhibition->id), [
                'content' => ''
            ]);

        // リダイレクトを確認
        $response->assertStatus(302);
        $response->assertRedirect(route('exhibition.show', $this->exhibition->id));

        // バリデーションエラーが存在するか確認
        $response->assertSessionHasErrors('content');

        // エラーメッセージの内容を確認
        $response->assertSessionHasErrors([
            'content' => 'コメントを入力してください'
        ]);

        // データベースにコメントが登録されていないか確認
        $this->assertDatabaseMissing('comments', [
            'user_id' => $this->user->id,
            'exhibition_id' => $this->exhibition->id,
        ]);
    }

    /**
     * コメントが255字以上の場合、バリデーションメッセージが表示される
     */
    public function test_コメントが255文字以上の場合バリデーションエラーが表示される()
    {
        // ユーザーとしてログイン
        $this->actingAs($this->user);

        // 256文字のコメントを生成
        $longComment = str_repeat('あ', 256);

        // 長すぎるコメントでリクエストを送信
        $response = $this->from(route('exhibition.show', $this->exhibition->id))
            ->post(route('comments.store', $this->exhibition->id), [
                'content' => $longComment
            ]);

        // リダイレクトを確認
        $response->assertStatus(302);
        $response->assertRedirect(route('exhibition.show', $this->exhibition->id));

        // バリデーションエラーが存在するか確認
        $response->assertSessionHasErrors('content');

        // エラーメッセージの内容を確認
        $response->assertSessionHasErrors([
            'content' => 'コメントは255文字以内で入力してください'
        ]);

        // データベースにコメントが登録されていないか確認
        $this->assertDatabaseMissing('comments', [
            'user_id' => $this->user->id,
            'exhibition_id' => $this->exhibition->id,
        ]);
    }
}
