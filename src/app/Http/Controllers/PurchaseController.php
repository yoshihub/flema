<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Http\Requests\PurchaseRequest;
use App\Models\Exhibition;
use App\Models\Purchase;
use App\Models\UserAddress;
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
            'user_id'       => Auth::id(),
        ]);

        $exhibition = Exhibition::find($request->exhibition_id);
        $exhibition->is_sold = true;
        $exhibition->save();

        return back()->with('message', '購入しました');
    }

    public function purchaseAddress($id)
    {
        $exhibition = Exhibition::find($id);
        return view('purchaseAddress.index', compact('exhibition'));
    }

    public function purchaseAddressStore(AddressRequest $request)
    {
        $user = Auth::user();

        if ($user->address) {
            $userAddress = $user->address;
            $userAddress->update([
                'postCode' => $request->postCode,
                'address' => $request->address,
                'building' => $request->building
            ]);
        } else {
            UserAddress::create([
                'user_id' => $user->id,
                'postCode' => $request->postCode,
                'address' => $request->address,
                'building' => $request->building
            ]);
        }

        return redirect()->route('purchase.index', $request->exhibition_id)
            ->with('message', '住所を更新しました');
    }

    public function complete(Purchase $purchase)
    {
        $user = Auth::user();

        // 購入者のみ実行可能
        if ($purchase->user_id !== $user->id) {
            abort(403, '購入者のみが取引を完了できます。');
        }

        // すでに完了済みならそのままチャットに戻る
        if ($purchase->is_completed) {
            return redirect()->route('purchase.chat', $purchase)
                ->with('message', 'すでに取引は完了しています。');
        }

        $purchase->is_completed = true;
        $purchase->completed_at = now();
        $purchase->save();

        return redirect()->route('purchase.chat', $purchase)
            ->with('message', '取引を完了しました。');
    }
}
