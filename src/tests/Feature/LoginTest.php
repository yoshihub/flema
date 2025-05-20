<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * テスト実行前の準備
     */
    protected function setUp(): void
    {
        parent::setUp();

        // テストユーザーの作成
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);
    }

    /**
     * メールアドレスが入力されていない場合、バリデーションメッセージが表示される
     *
     * @return void
     */
    public function test_email_is_required()
    {
        $response = $this->post('/login', [
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $response->assertStatus(302);
    }

    /**
     * パスワードが入力されていない場合、バリデーションメッセージが表示される
     *
     * @return void
     */
    public function test_password_is_required()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHasErrors('password');
        $response->assertStatus(302);
    }

    /**
     * 入力情報が間違っている場合、バリデーションメッセージが表示される
     *
     * @return void
     */
    public function test_credentials_are_invalid()
    {
        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $response->assertStatus(302);
    }

    /**
     * 正しい情報が入力された場合、ログイン処理が実行される
     *
     * @return void
     */
    public function test_user_can_login_with_correct_credentials()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/'); // ログイン後のリダイレクト先
    }

    /**
     * ログアウト機能のテスト
     *
     * @return void
     */
    public function test_user_can_logout()
    {
        // 1. ユーザーにログインをする
        $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();

        // 2. ログアウトボタンを押す（ログアウトリクエストを送信）
        $response = $this->post('/logout');

        // 3. ログアウト処理が実行される
        $this->assertGuest(); // ユーザーが非認証状態になっていることを確認
        $response->assertRedirect('/'); // ログアウト後のリダイレクト先
    }
}
