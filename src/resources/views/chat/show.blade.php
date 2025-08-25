@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/chat/show.css') }}">
@endsection

@section('content')
<div class="chat-layout">
    <!-- 左側サイドバー -->
    <div class="sidebar">
        <div class="sidebar-header">
            <span class="sidebar-title">その他の取引</span>
        </div>
        @if(isset($otherPurchases) && $otherPurchases->count())
        <div class="sidebar-list">
            @foreach($otherPurchases as $op)
            <a class="sidebar-item" href="{{ route('purchase.chat', ['purchase' => $op->id]) }}">
                {{ $op->exhibition ? $op->exhibition->name : '取引' }}
            </a>
            @endforeach
        </div>
        @else
        <div class="sidebar-empty">他の取引はありません</div>
        @endif
    </div>

    <!-- メインチャットエリア -->
    <div class="chat-container">
        <!-- ヘッダー部分 -->
        <div class="chat-header">
            <div class="transaction-title">
                @if ($otherUser->profile_image)
                <img src="{{ asset('storage/profile_images/' . $otherUser->profile_image) }}" alt="プロフィール画像" class="user-avatar">
                @else
                <img src="{{ asset('images/default-icon.png') }}" alt="デフォルト画像" class="user-avatar">
                @endif
                <span class="username">「{{ $otherUser->name }}」さんとの取引画面</span>
            </div>
            @if(Auth::id() === $purchase->user_id && !$purchase->is_completed)
            <form action="{{ route('purchase.complete', $purchase) }}" method="POST">
                @csrf
                <button type="submit" class="complete-button">取引を完了する</button>
            </form>
            @endif
        </div>

        <!-- 商品情報部分 -->
        <div class="product-info">
            <div class="product-image">
                @if ($purchase->exhibition->exhibition_image)
                <img src="{{ asset('storage/exhibition_images/' . $purchase->exhibition->exhibition_image) }}" alt="商品画像">
                @else
                <div class="placeholder-image">商品画像</div>
                @endif
            </div>
            <div class="product-details">
                <h3 class="product-name">{{ $purchase->exhibition->name }}</h3>
                <p class="product-price">￥{{ number_format($purchase->exhibition->price) }}</p>
            </div>
        </div>

        <!-- チャットメッセージ部分 -->
        <div class="chat-messages">
            @foreach($messages as $message)
            <div class="message {{ $message->user_id === Auth::id() ? 'sent' : 'received' }}">
                @if($message->user_id !== Auth::id())
                <div class="message-header">
                    @if ($message->user->profile_image)
                    <img src="{{ asset('storage/profile_images/' . $message->user->profile_image) }}" alt="プロフィール画像" class="message-avatar">
                    @else
                    <img src="{{ asset('images/default-icon.png') }}" alt="デフォルト画像" class="message-avatar">
                    @endif
                    <span class="message-username">{{ $message->user->name }}</span>
                </div>
                @endif

                <div class="message-content">
                    @if($message->image_path)
                    <img src="{{ asset('storage/chat_images/' . $message->image_path) }}" alt="送信画像" class="message-image">
                    @endif
                    @if($message->content)
                    <p class="message-text">{{ $message->content }}</p>
                    @endif
                </div>

                @if($message->user_id === Auth::id())
                <div class="message-header sent-header">
                    @if (Auth::user()->profile_image)
                    <img src="{{ asset('storage/profile_images/' . Auth::user()->profile_image) }}" alt="プロフィール画像" class="message-avatar">
                    @else
                    <img src="{{ asset('images/default-icon.png') }}" alt="デフォルト画像" class="message-avatar">
                    @endif
                    <span class="message-username">{{ Auth::user()->name }}</span>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <!-- メッセージ入力部分 -->
        <div class="message-input-container">
            <form action="{{ route('messages.store', $purchase) }}" method="POST" enctype="multipart/form-data" class="message-form">
                @csrf
                <div class="input-group">
                    <input type="text" name="content" placeholder="取引メッセージを記入してください" class="message-input" required>
                    <label for="image" class="image-upload-button">画像を追加</label>
                    <input type="file" id="image" name="image" accept="image/*" style="display: none;">
                    <button type="submit" class="send-button">
                        <svg viewBox="0 0 24 24" width="24" height="24">
                            <path fill="currentColor" d="M2,21L23,12L2,3V10L17,12L2,14V21Z" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
