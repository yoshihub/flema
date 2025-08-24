<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\ExhibitionController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\MessageReadController;
use App\Http\Controllers\MyPageController;
use App\Http\Controllers\PurchaseCompleteController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SellController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ExhibitionController::class, 'index'])->name('exhibition.index');
Route::get('/item/{id}', [ExhibitionController::class, 'show'])->name('exhibition.show');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/mypage', [MyPageController::class, 'index']);
    Route::get('/mypage/profile', [MyPageController::class, 'profile']);
    Route::post('/mypage/profile', [MyPageController::class, 'update']);

    Route::get('/sell', [SellController::class, 'index']);
    Route::post('/sell', [SellController::class, 'store']);

    Route::post('/favorite/{id}', [FavoriteController::class, 'favorite'])->name('favorite');
    Route::post('/unfavorite/{id}', [FavoriteController::class, 'unfavorite'])->name('unfavorite');

    Route::post('/comment/{id}', [ExhibitionController::class, 'comment'])->name('comments.store');

    Route::get('/purchase/{id}', [PurchaseController::class, 'index'])->name('purchase.index');
    Route::post('/purchase', [PurchaseController::class, 'store'])->name('purchase.store');

    Route::get('/purchase/address/{id}', [PurchaseController::class, 'purchaseAddress'])->name('purchase.address.index');
    Route::post('/purchase/address', [PurchaseController::class, 'purchaseAddressStore'])->name('purchase.address.store');


    // 取引チャット画面へ遷移（サイドバーで別取引へも切替） FN002 / FN003
    Route::get('/trades/{purchase}', [ChatController::class, 'show'])
        ->whereNumber('purchase')
        ->name('trades.chat.show');

    // 取引チャット：メッセージ投稿（本文必須・400文字・画像はjpeg/png任意） US002 / FN006 / FN007 / FN008 / FN009
    Route::post('/trades/{purchase}/messages', [ChatMessageController::class, 'store'])
        ->whereNumber('purchase')
        ->name('trades.messages.store');

    // 取引チャット：メッセージ編集・削除 US003 / FN010 / FN011
    Route::match(['put', 'patch'], '/trades/{purchase}/messages/{message}', [ChatMessageController::class, 'update'])
        ->whereNumber('purchase')
        ->whereNumber('message')
        ->name('trades.messages.update');

    Route::delete('/trades/{purchase}/messages/{message}', [ChatMessageController::class, 'destroy'])
        ->whereNumber('purchase')
        ->whereNumber('message')
        ->name('trades.messages.destroy');

    // 未読管理：この取引チャットを既読にする（バッジ件数/新着ソート用） FN004 / FN005
    Route::post('/trades/{purchase}/reads', [MessageReadController::class, 'store'])
        ->whereNumber('purchase')
        ->name('trades.reads.store');

    // 購入者：取引完了ボタン（メール送信：出品者宛） US005 / FN015 / FN016
    // ※押下→モーダルで評価入力へ導線（UI側で表示）、サーバ側は完了フラグ＆メール送信
    Route::post('/trades/{purchase}/complete', [PurchaseCompleteController::class, 'store'])
        ->whereNumber('purchase')
        ->name('trades.complete.store');

    // 取引後評価（購入者→出品者 / 出品者→購入者） US004 / FN012 / FN013 / FN014
    // モーダルの中身を単体画面としても表示できるようGET用意（必要に応じて使う）
    Route::get('/trades/{purchase}/reviews/create', [ReviewController::class, 'create'])
        ->whereNumber('purchase')
        ->name('trades.reviews.create');

    Route::post('/trades/{purchase}/reviews', [ReviewController::class, 'store'])
        ->whereNumber('purchase')
        ->name('trades.reviews.store');
});
