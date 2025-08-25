<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function show(Purchase $purchase)
    {
        // 認証されたユーザーがこの取引の関係者（購入者または出品者）であることを確認
        $user = Auth::user();
        if ($purchase->user_id !== $user->id && $purchase->exhibition->user_id !== $user->id) {
            abort(403, 'この取引にアクセスする権限がありません。');
        }

        // 取引に関連するメッセージを取得
        $messages = $purchase->messages()->with('user')->get();

        // 相手のユーザー情報を取得
        $otherUser = $purchase->user_id === $user->id
            ? $purchase->exhibition->user  // 購入者の場合は出品者
            : $purchase->buyer;            // 出品者の場合は購入者

        // サイドバー用：自分が関係者かつ未完了の他の取引（最新メッセージ順）
        $otherPurchases = Purchase::where('is_completed', false)
            ->where('id', '!=', $purchase->id)
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
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

        return view('chat.show', compact('purchase', 'messages', 'otherUser', 'otherPurchases'));
    }

    public function store(Request $request, Purchase $purchase)
    {
        $request->validate([
            'content' => 'required|string|max:400',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = Auth::user();

        // 権限確認
        if ($purchase->user_id !== $user->id && $purchase->exhibition->user_id !== $user->id) {
            abort(403, 'この取引にアクセスする権限がありません。');
        }

        $message = new Message();
        $message->purchase_id = $purchase->id;
        $message->user_id = $user->id;
        $message->content = $request->content;

        // 画像がアップロードされた場合
        if ($request->hasFile('image')) {
            $filename = time() . '.' . $request->image->getClientOriginalExtension();
            $request->image->storeAs('public/chat_images', $filename);
            $message->image_path = $filename;
        }

        $message->save();

        return redirect()->route('purchase.chat', $purchase);
    }
}
