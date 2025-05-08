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

        if ($page === 'mylist') {
            if (Auth::check()) {
                $exhibitions = Auth::user()->exhibitions;
            } else {
                $exhibitions = collect();
            }
        } else {
            $exhibitions = Exhibition::where('user_id', '!=', Auth::id())->get();
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
