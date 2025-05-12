<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Exhibition;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function index($id)
    {
        $exhibition = Exhibition::find($id);
        $userAddress = Auth::user()->address;
        return view('purchase.index', compact('exhibition', 'userAddress'));
    }

    public function store(PurchaseRequest $request)
    {
        Purchase::create([
            'payment'       => $request->payment,
            'postCode'      => $request->postCode,
            'address'       => $request->address,
            'building'      => $request->building,
            'exhibition_id' => $request->exhibition_id,
        ]);

        $exhibition = Exhibition::find($request->exhibition_id);
        $exhibition->sold = true;
        $exhibition->save();

        return back();
    }
}
