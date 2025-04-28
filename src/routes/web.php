<?php

use App\Http\Controllers\ExhibitionController;
use App\Http\Controllers\MyPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ExhibitionController::class, 'index']);

Route::get('/mypage', [MyPageController::class, 'index']);
Route::get('/mypage/profile', [MyPageController::class, 'profile']);
Route::post('/mypage/profile', [MyPageController::class, 'update']);
