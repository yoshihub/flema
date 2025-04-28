<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exhibition;

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
}
