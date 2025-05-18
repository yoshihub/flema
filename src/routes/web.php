<?php

use App\Http\Controllers\ExhibitionController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\MyPageController;
use App\Http\Controllers\PurchaseController;
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
});
