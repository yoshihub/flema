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


    // 取引チャット画面（要ログイン）
    Route::get('/purchases/{purchase}/chat', [ChatController::class, 'show'])
        ->name('purchase.chat');

    // 取引メッセージ送信（画像添付対応、要ログイン）
    Route::post('/purchases/{purchase}/messages', [ChatController::class, 'store'])
        ->name('messages.store');

    // 取引メッセージ編集・更新・削除（要ログイン）
    Route::get('/purchases/{purchase}/messages/{message}/edit', [ChatController::class, 'edit'])
        ->name('messages.edit');
    Route::put('/purchases/{purchase}/messages/{message}', [ChatController::class, 'update'])
        ->name('messages.update');
    Route::delete('/purchases/{purchase}/messages/{message}', [ChatController::class, 'destroy'])
        ->name('messages.destroy');

    // 取引完了処理（購入者が実行、要ログイン）
    Route::post('/purchases/{purchase}/complete', [PurchaseController::class, 'complete'])
        ->name('purchase.complete');

    // 取引評価送信（購入者・出品者双方、要ログイン）
    Route::post('/purchases/{purchase}/rate', [PurchaseController::class, 'rate'])
        ->name('purchase.rate');
});
