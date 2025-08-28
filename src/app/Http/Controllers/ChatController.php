<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Message;
use App\Models\MessageRead;
use App\Http\Requests\StoreMessageRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function show(Purchase $purchase)
    {
        // 認証されたユーザーがこの取引の関係者（購入者または出品者）であることを確認
        $user = Auth::user();
        if ($purchase->user_id !== $user->id && $purchase->exhibition->user_id !== $user->id) {
            abort(403, 'この取引にアクセスする権限がありません。');
        }

        // 既読基準を更新（この時点で閲覧したとみなす）
        MessageRead::updateOrCreate(
            [
                'purchase_id' => $purchase->id,
                'user_id' => $user->id,
            ],
            [
                'last_read_at' => now(),
            ]
        );

        // 取引に関連するメッセージを取得
        $messages = $purchase->messages()->with('user')->get();

        // 相手のユーザー情報を取得
        $otherUser = $purchase->user_id === $user->id
            ? $purchase->exhibition->user  // 購入者の場合は出品者
            : $purchase->buyer;            // 出品者の場合は購入者

        // サイドバー用：自分が関係者かつ、未完了 または 完了済み未評価の他の取引（最新メッセージ順）
        $otherPurchases = Purchase::where(function ($q) use ($user) {
            $q->where('is_completed', false)
                ->orWhere(function ($qq) use ($user) {
                    $qq->where('is_completed', true)
                        ->whereDoesntHave('reviews', function ($r) use ($user) {
                            $r->where('reviewer_id', $user->id);
                        });
                });
        })
            ->where('id', '!=', $purchase->id)
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('exhibition', function ($qq) use ($user) {
                        $qq->where('user_id', $user->id);
                    });
            })
            ->with(['exhibition', 'reviews'])
            ->orderByDesc(
                Message::select('created_at')
                    ->whereColumn('messages.purchase_id', 'purchases.id')
                    ->orderBy('created_at', 'desc')
                    ->limit(1)
            )
            ->get();

        // レビュー表示判定
        $hasReviewed = $purchase->reviews()->where('reviewer_id', $user->id)->exists();
        $isBuyer = $purchase->user_id === $user->id;
        $isSeller = $purchase->exhibition->user_id === $user->id;

        $shouldShowReviewModal = false;
        if ($purchase->is_completed && !$hasReviewed) {
            if ($isSeller) {
                $shouldShowReviewModal = true; // 出品者は完了後に開いたら表示
            }
            if ($isBuyer && session('show_review_modal')) {
                $shouldShowReviewModal = true; // 購入者は完了直後に表示
            }
        }

        return view('chat.show', compact('purchase', 'messages', 'otherUser', 'otherPurchases', 'shouldShowReviewModal'));
    }

    public function store(StoreMessageRequest $request, Purchase $purchase)
    {
        // バリデーションは StoreMessageRequest で実施

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

    public function edit(Purchase $purchase, Message $message)
    {
        $user = Auth::user();

        // 取引関係者か確認
        if ($purchase->user_id !== $user->id && $purchase->exhibition->user_id !== $user->id) {
            abort(403, 'この取引にアクセスする権限がありません。');
        }

        // メッセージがこの取引に属しているか確認
        if ($message->purchase_id !== $purchase->id) {
            abort(404);
        }

        // 自分のメッセージのみ編集可
        if ($message->user_id !== $user->id) {
            abort(403, 'このメッセージを編集する権限がありません。');
        }

        return view('chat.edit', compact('purchase', 'message'));
    }

    public function update(StoreMessageRequest $request, Purchase $purchase, Message $message)
    {
        $user = Auth::user();

        // 取引関係者か確認
        if ($purchase->user_id !== $user->id && $purchase->exhibition->user_id !== $user->id) {
            abort(403, 'この取引にアクセスする権限がありません。');
        }

        // メッセージがこの取引に属しているか確認
        if ($message->purchase_id !== $purchase->id) {
            abort(404);
        }

        // 自分のメッセージのみ更新可
        if ($message->user_id !== $user->id) {
            abort(403, 'このメッセージを更新する権限がありません。');
        }

        // 本文
        $message->content = $request->content;

        // 画像の削除指定がある場合
        if ($request->boolean('remove_image')) {
            if ($message->image_path) {
                Storage::delete('public/chat_images/' . $message->image_path);
            }
            $message->image_path = null;
        }

        // 新しい画像がアップロードされた場合（差し替え）
        if ($request->hasFile('image')) {
            if ($message->image_path) {
                Storage::delete('public/chat_images/' . $message->image_path);
            }
            $filename = time() . '.' . $request->image->getClientOriginalExtension();
            $request->image->storeAs('public/chat_images', $filename);
            $message->image_path = $filename;
        }

        $message->save();

        return redirect()->route('purchase.chat', $purchase);
    }

    public function destroy(Purchase $purchase, Message $message)
    {
        $user = Auth::user();

        // 取引関係者か確認
        if ($purchase->user_id !== $user->id && $purchase->exhibition->user_id !== $user->id) {
            abort(403, 'この取引にアクセスする権限がありません。');
        }

        // メッセージがこの取引に属しているか確認
        if ($message->purchase_id !== $purchase->id) {
            abort(404);
        }

        // 自分のメッセージのみ削除可
        if ($message->user_id !== $user->id) {
            abort(403, 'このメッセージを削除する権限がありません。');
        }

        if ($message->image_path) {
            Storage::delete('public/chat_images/' . $message->image_path);
        }

        $message->delete();

        return redirect()->route('purchase.chat', $purchase);
    }
}
