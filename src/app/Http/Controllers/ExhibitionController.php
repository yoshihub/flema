<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exhibition;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ExhibitionController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->query('page');

        if ($page === 'mylist') {
            $exhibitions = Exhibition::where('is_favorite', true)->get();
        } else {
            $exhibitions = Exhibition::all();
        }

        return view('exhibition.index', compact('exhibitions'));
    }

    public function show($id)
    {
        $exhibition = Exhibition::with(['categories', 'condition'])->findOrFail($id);

        return view('exhibition.show', compact('exhibition'));
    }
}
