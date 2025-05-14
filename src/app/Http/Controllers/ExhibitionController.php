<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use Illuminate\Http\Request;
use App\Models\Exhibition;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Condition;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ExhibitionController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->query('page');
        $search = $request->query('search');

        if ($page === 'mylist') {
            if (Auth::check()) {
                // マイリスト表示
                $query = Exhibition::where('user_id', Auth::id());

                // 検索条件がある場合は適用
                if ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                }

                $exhibitions = $query->get();
            } else {
                $exhibitions = collect();
            }
        } else {
            // 通常表示
            $query = Exhibition::query();

            // 検索条件がある場合は適用
            if ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            }

            // 自分の出品以外を取得
            if (Auth::check()) {
                $query->where('user_id', '!=', Auth::id());
            }

            $exhibitions = $query->get();
        }

        return view('exhibition.index', compact('exhibitions'));
    }

    public function show($id)
    {
        $exhibition = Exhibition::with(['categories', 'condition'])->findOrFail($id);

        return view('exhibition.show', compact('exhibition'));
    }

    public function comment(CommentRequest $request, $id)
    {
        Comment::create([
            'content' => $request->content,
            'user_id' => Auth::id(),
            'exhibition_id' => $id,
        ]);

        return back();
    }
}
