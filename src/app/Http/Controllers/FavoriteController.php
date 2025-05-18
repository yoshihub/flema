<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    // お気に入り登録
    public function favorite($id)
    {
        $user = Auth::user();
        $user->exhibitions()->attach($id);
        return back();
    }

    // お気に入り解除
    public function unfavorite($id)
    {
        $user = Auth::user();
        $user->exhibitions()->detach($id);
        return back();
    }
}
