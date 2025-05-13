<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Http\Requests\ProfileRequest;
use App\Models\Exhibition;
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
        } elseif ($tab === 'sell') {
            $exhibitions = Exhibition::where('user_id', '=', Auth::id())->get();
        } else {
            $exhibitions = collect();
        }

        return view('mypage.index', compact('exhibitions'));
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

        return view('mypage.index', compact('exhibitions'));
    }
}
