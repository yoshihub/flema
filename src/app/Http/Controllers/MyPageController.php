<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Http\Requests\ProfileRequest;
use App\Models\Exhibition;
use App\Models\Message;
use App\Models\Purchase;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Container\Container;
use Illuminate\Http\Request;

class MyPageController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $tab = $request->query('tab');

        if ($tab === 'buy') {
            $exhibitions = Exhibition::whereIn('id', function ($query) use ($user) {
                $query->select('exhibition_id')
                    ->from('purchases')
                    ->where('user_id', $user->id);
            })->get();
            $purchases = collect();
        } elseif ($tab === 'sell') {
            $exhibitions = Exhibition::where('user_id', '=', Auth::id())->get();
            $purchases = collect();
        } elseif ($tab === 'trade') {
            // 自分が購入者または出品者で、かつ取引が未完了のものを、最新メッセージ順にソート
            $exhibitions = collect();
            $purchases = Purchase::where('is_completed', false)
                ->where(function ($q) use ($user) {
                    // 購入者として
                    $q->where('user_id', $user->id)
                        // または出品者として（出品の所有者）
                        ->orWhereHas('exhibition', function ($qq) use ($user) {
                            $qq->where('user_id', $user->id);
                        });
                })
                ->with('exhibition')
                ->orderByDesc(
                    Message::select('created_at')
                        ->whereColumn('messages.purchase_id', 'purchases.id')
                        ->orderBy('created_at', 'desc')
                        ->limit(1)
                )
                ->get();
        } else {
            $exhibitions = collect();
            $purchases = collect();
        }

        return view('mypage.index', compact('exhibitions', 'purchases'));
    }

    public function profile()
    {
        $user = Auth::user();
        $address = $user->address;

        return view('mypage.profile', compact('user', 'address'));
    }

    public function update(Request $request)
    {
        $profileRequest = Container::getInstance()->make(ProfileRequest::class);
        $profileRequest->merge($request->only(['profile_image']));
        $profileRequest->setMethod('POST');
        $profileRequest->validateResolved();

        $addressRequest = Container::getInstance()->make(AddressRequest::class);
        $addressRequest->merge($request->only(['name', 'postCode', 'address', 'building']));
        $addressRequest->setMethod('POST');
        $addressRequest->validateResolved();

        $user = Auth::user();
        $user->name = $request->name;

        if ($request->hasFile('profile_image')) {
            $filename = time() . '.' . $request->profile_image->getClientOriginalExtension();
            $request->profile_image->storeAs('public/profile_images', $filename);
            $user->profile_image = $filename;
        }

        $user->save();

        if ($user->address == null) {
            UserAddress::create([
                'user_id' => $user->id,
                'postCode' => $request->postCode,
                'address' => $request->address,
                'building' => $request->building
            ]);
        } else {
            $userAddress = $user->address;
            $userAddress->update([
                'postCode' => $request->postCode,
                'address' => $request->address,
                'building' => $request->building
            ]);
        }

        $exhibitions = collect();

        return redirect('/mypage')->with('message', 'プロフィールを編集しました');
    }
}
