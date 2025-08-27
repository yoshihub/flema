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
                <div class="message-content">
                    @if($message->image_path)
                    <img src="{{ asset('storage/chat_images/' . $message->image_path) }}" alt="送信画像" class="message-image">
                    @endif
                    @if($message->content)
                    <p class="message-text">{{ $message->content }}</p>
                    @endif
                </div>
                @endif

                @if($message->user_id === Auth::id())
                <div class="message-header sent-header">
                    <span class="message-username">{{ Auth::user()->name }}</span>
                    @if (Auth::user()->profile_image)
                    <img src="{{ asset('storage/profile_images/' . Auth::user()->profile_image) }}" alt="プロフィール画像" class="message-avatar">
                    @else
                    <img src="{{ asset('images/default-icon.png') }}" alt="デフォルト画像" class="message-avatar">
                    @endif
                </div>
                <div class="message-content">
                    @if($message->image_path)
                    <img src="{{ asset('storage/chat_images/' . $message->image_path) }}" alt="送信画像" class="message-image">
                    @endif
                    @if($message->content)
                    <p class="message-text">{{ $message->content }}</p>
                    @endif
                </div>
                <div class="message-actions">
                    <a class="action-link" href="{{ route('messages.edit', ['purchase' => $purchase->id, 'message' => $message->id]) }}">編集</a>
                    <form action="{{ route('messages.destroy', ['purchase' => $purchase->id, 'message' => $message->id]) }}" method="POST" class="inline-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-link danger">削除</button>
                    </form>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <!-- メッセージ入力部分 -->
        <div class="message-input-container">
            <form action="{{ route('messages.store', $purchase) }}" method="POST" enctype="multipart/form-data" class="message-form">
                @csrf
                @if ($errors->has('content') || $errors->has('image'))
                <div class="validation-errors" style="color: #d93025; margin-bottom: 8px;">
                    @foreach ($errors->get('content') as $error)
                    <div class="error-item">{{ $error }}</div>
                    @endforeach
                    @foreach ($errors->get('image') as $error)
                    <div class="error-item">{{ $error }}</div>
                    @endforeach
                </div>
                @endif
                <div class="input-group">
                    <input type="text" name="content" value="{{ old('content') }}" placeholder="取引メッセージを記入してください" class="message-input">
                    <label for="image" class="image-upload-button">画像を追加</label>
                    <input type="file" id="image" name="image" accept=".png,.jpeg" style="display: none;">
                    <button type="submit" class="send-button">
                        <svg viewBox="0 0 24 24" width="24" height="24">
                            <path fill="currentColor" d="M2,21L23,12L2,3V10L17,12L2,14V21Z" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <!-- 取引完了モーダル -->
        <div id="review-modal" class="modal-overlay" style="display: none;">
            <div class="modal-card">
                <div class="modal-header">取引が完了しました。</div>
                <div class="modal-body">
                    <div class="modal-sub">今回の取引相手はいかがでしたか？</div>
                    <form id="review-form" action="{{ route('purchase.rate', $purchase) }}" method="POST">
                        @csrf
                        <input type="hidden" name="rating" id="rating-input" value="">
                        <div class="star-group" aria-label="rating">
                            <button type="button" class="star" data-value="1">★</button>
                            <button type="button" class="star" data-value="2">★</button>
                            <button type="button" class="star" data-value="3">★</button>
                            <button type="button" class="star" data-value="4">★</button>
                            <button type="button" class="star" data-value="5">★</button>
                        </div>
                        <div class="modal-actions">
                            <button id="submit-rating" type="submit" class="modal-submit" disabled>送信する</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- モーダル表示設定（BladeをJSに直接埋め込まない） -->
        <div id="review-config" data-should-show="{{ isset($shouldShowReviewModal) && $shouldShowReviewModal ? '1' : '0' }}" style="display:none;"></div>
    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var input = document.querySelector('.message-input');
        var form = document.querySelector('.message-form');
        if (!input) return;

        // purchase x user 毎にキーを分ける（別ユーザーで干渉しないように）
        var storageKey = 'chat_draft_{{ $purchase->id }}_{{ Auth::id() }}';

        // 初期復元（oldがあればold優先）
        if (!input.value) {
            var saved = sessionStorage.getItem(storageKey);
            if (saved) {
                input.value = saved;
            }
        }

        // 入力の都度保存（高頻度でも軽量）
        input.addEventListener('input', function() {
            try {
                sessionStorage.setItem(storageKey, input.value);
            } catch (e) {}
        });

        // 送信時クリア
        if (form) {
            form.addEventListener('submit', function() {
                try {
                    sessionStorage.removeItem(storageKey);
                } catch (e) {}
            });
        }
    });

    // 評価モーダル表示制御
    (function() {
        var cfg = document.getElementById('review-config');
        var shouldShow = cfg && cfg.dataset && cfg.dataset.shouldShow === '1';
        var overlay = document.getElementById('review-modal');
        if (!overlay) return;
        if (shouldShow) {
            overlay.style.display = 'flex';
        }

        var stars = overlay.querySelectorAll('.star');
        var input = document.getElementById('rating-input');
        var submit = document.getElementById('submit-rating');
        var current = 0;

        function render(val) {
            stars.forEach(function(el, i) {
                var idx = i + 1;
                el.classList.toggle('active', idx <= val);
            });
            submit.disabled = val === 0;
        }

        stars.forEach(function(el) {
            el.addEventListener('click', function() {
                current = parseInt(el.getAttribute('data-value'));
                input.value = current;
                render(current);
            });
            el.addEventListener('mouseenter', function() {
                render(parseInt(el.getAttribute('data-value')));
            });
        });
        overlay.addEventListener('mouseleave', function() {
            render(current);
        });
    })();
</script>
@endsection
