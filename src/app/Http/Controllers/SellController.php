<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Exhibition;
use Illuminate\Support\Facades\Auth;

class SellController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $conditions = Condition::all();

        return view('sell.index', compact('categories', 'conditions'));
    }

    public function store(ExhibitionRequest $request)
    {
        $user = Auth::user();
        $exhibition = new Exhibition();

        $filename = time() . '.' . $request->exhibition_image->getClientOriginalExtension();
        $request->exhibition_image->storeAs('public/exhibition_images', $filename);
        $exhibition->exhibition_image = $filename;

        $exhibition->name = $request->name;
        $exhibition->brand = $request->brand;
        $exhibition->explanation = $request->explanation;
        $exhibition->price = $request->price;
        $exhibition->user_id = $user->id;
        $exhibition->condition_id = $request->condition;
        $exhibition->save();

        $exhibition->categories()->attach($request->categories);

        return back()->with('message', '出品しました');
    }
}
