<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Exhibition;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExhibitionTest extends TestCase
{
    use RefreshDatabase;

    public function test_未認証ユーザーがマイリストページにアクセスした場合_何も表示されない()
    {
        $response = $this->get(route('exhibition.index', ['page' => 'mylist']));

        $response->assertStatus(200);
        $response->assertViewHas('exhibitions', collect());
    }

    public function test_認証済みユーザーがマイリストページにアクセスした場合_いいねした商品のみ表示される()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $condition = Condition::factory()->create();

        // いいねした商品
        $likedExhibition = Exhibition::factory()->create([
            'user_id' => $otherUser->id,
            'condition_id' => $condition->id,
        ]);
        $user->exhibitions()->attach($likedExhibition->id);

        // いいねしていない商品
        Exhibition::factory()->create([
            'user_id' => $otherUser->id,
            'condition_id' => $condition->id,
        ]);

        $response = $this->actingAs($user)
            ->get(route('exhibition.index', ['page' => 'mylist']));

        $response->assertStatus(200);

        $response->assertViewHas('exhibitions', function ($exhibitions) use ($likedExhibition) {
            return $exhibitions->count() === 1 && $exhibitions->first()->id === $likedExhibition->id;
        });
    }

    public function test_購入済み商品にはSoldラベルが表示される()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $condition = Condition::factory()->create();

        // 購入済みの商品
        $soldExhibition = Exhibition::factory()->sold()->create([
            'user_id' => $otherUser->id,
            'condition_id' => $condition->id,
        ]);
        $user->exhibitions()->attach($soldExhibition->id);

        $response = $this->actingAs($user)
            ->get(route('exhibition.index', ['page' => 'mylist']));

        $response->assertStatus(200);
        $response->assertViewHas('exhibitions', function ($exhibitions) use ($soldExhibition) {
            return $exhibitions->first()->is_sold === true;
        });
    }

    public function test_自分が出品した商品はマイリストに表示されない()
    {
        $user = User::factory()->create();
        $condition = Condition::factory()->create();

        // 自分が出品した商品
        Exhibition::factory()->create([
            'user_id' => $user->id,
            'condition_id' => $condition->id,
        ]);

        $response = $this->actingAs($user)
            ->get(route('exhibition.index', ['page' => 'mylist']));

        $response->assertStatus(200);
        $response->assertViewHas('exhibitions', function ($exhibitions) {
            return $exhibitions->isEmpty();
        });
    }

    public function test_商品名で部分一致検索ができる()
    {
        $user = User::factory()->create();
        $condition = Condition::factory()->create();

        // 検索対象の商品
        Exhibition::factory()->create([
            'name' => 'テスト商品A',
            'user_id' => $user->id,
            'condition_id' => $condition->id,
        ]);

        // 検索対象外の商品
        Exhibition::factory()->create([
            'name' => '別の商品B',
            'user_id' => $user->id,
            'condition_id' => $condition->id,
        ]);

        $response = $this->get(route('exhibition.index', ['search' => 'テスト']));

        $response->assertStatus(200);
        $response->assertViewHas('exhibitions', function ($exhibitions) {
            return $exhibitions->count() === 1 && $exhibitions->first()->name === 'テスト商品A';
        });
    }

    public function test_検索状態がマイリストでも保持されている()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $condition = Condition::factory()->create();

        // 検索対象の商品（いいね済み）
        $likedExhibition = Exhibition::factory()->create([
            'name' => 'テスト商品A',
            'user_id' => $otherUser->id,
            'condition_id' => $condition->id,
        ]);
        $user->exhibitions()->attach($likedExhibition->id);

        // 検索対象外の商品（いいね済み）
        $otherLikedExhibition = Exhibition::factory()->create([
            'name' => '別の商品B',
            'user_id' => $otherUser->id,
            'condition_id' => $condition->id,
        ]);
        $user->exhibitions()->attach($otherLikedExhibition->id);

        // ホームページで検索
        $response = $this->actingAs($user)
            ->get(route('exhibition.index', ['search' => 'テスト']));

        $response->assertStatus(200);
        $response->assertViewHas('exhibitions', function ($exhibitions) {
            return $exhibitions->count() === 1 && $exhibitions->first()->name === 'テスト商品A';
        });

        // マイリストページに遷移
        $response = $this->actingAs($user)
            ->get(route('exhibition.index', ['page' => 'mylist', 'search' => 'テスト']));

        $response->assertStatus(200);
        $response->assertViewHas('exhibitions', function ($exhibitions) {
            return $exhibitions->count() === 1 && $exhibitions->first()->name === 'テスト商品A';
        });
    }
}
