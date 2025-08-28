@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/exhibition/show.css') }}">
@endsection

@section('content')
<div class="product-detail">
    <div class="product-main">
        <div class="product-image">
            @if($exhibition->exhibition_image)
            <img src="{{ asset('storage/exhibition_images/' . $exhibition->exhibition_image) }}" alt="{{ $exhibition->name }}">
            @else
            <div class="no-image">商品画像</div>
            @endif
        </div>

        <div class="product-info">
            <h1 class="product-name">{{ $exhibition->name }}</h1>
            @if($exhibition->brand)<p class="product-brand">{{ $exhibition->brand }}</p>@endif

            <p class="product-price">¥{{ number_format($exhibition->price) }}<span class="tax">(税込)</span></p>
            <div class="action-buttons">

                @if(Auth::check())

                @if(Auth::user()->exhibitions->contains($exhibition->id))
                <form action="{{ route('unfavorite', $exhibition->id) }}" method="POST">
                    @csrf
                    <div class="action-item">
                        <button type="submit" class="circle-btn" aria-label="お気に入り解除">
                            <i class="fa-solid fa-star" style="color: #FFD700;"></i>
                        </button>
                        <span class="action-count">{{ $exhibition->users->count() }}</span>
                    </div>
                </form>
                @else
                <form action="{{ route('favorite', $exhibition->id) }}" method="POST">
                    @csrf
                    <div class="action-item">
                        <button type="submit" class="circle-btn" aria-label="お気に入り登録">
                            <i class="fa-regular fa-star"></i>
                        </button>
                        <span class="action-count">{{ $exhibition->users->count() }}</span>
                    </div>
                </form>
                @endif

                @else
                <div class="action-item">
                    <button class="circle-btn-disable" aria-label="お気に入り登録">
                        <i class="fa-regular fa-star"></i>
                    </button>
                    <span class="action-count">{{ $exhibition->users->count() }}</span>
                </div>
                @endif

                <div class="action-item">
                    <button class="circle-btn-disable" aria-label="コメント">
                        <i class="fa-regular fa-comment"></i>
                    </button>
                    <span class="action-count">{{ $exhibition->comments->count() }}</span>
                </div>
            </div>

            @if($exhibition->is_sold)
            <div class="purchase-btn sold">売り切れ</div>
            @elseif(Auth::check())
            <a href="{{ route('purchase.index', $exhibition->id) }}" class="purchase-btn">購入手続きへ</a>
            @endif

            <div class="product-description">
                <h2>商品説明</h2>
                <div class="description-content">
                    <p>{{ $exhibition->explanation }}</p>
                </div>
            </div>

            <div class="product-details">
                <h2>商品の情報</h2>
                <div class="details-table">
                    <div class="detail-row">
                        <div class="detail-label">カテゴリー</div>
                        <div class="detail-value">
                            @foreach($exhibition->categories as $category)
                            <span class="category-tag">{{ $category->content }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">商品の状態</div>
                        <div class="detail-value">{{ $exhibition->condition->content }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="comment-section">
        <h2>コメント({{ $exhibition->comments->count() }})</h2>
        @foreach($exhibition->comments as $comment)
        <hr>
        <div>
            <p>
                @if ($comment->user && $comment->user->profile_image)
                <img src="{{ asset('storage/profile_images/' . $comment->user->profile_image) }}" alt="プロフィール画像" class="image">
                @else
                <img src="{{ asset('images/default-icon.png') }}" alt="デフォルト画像" class="image">
                @endif
                {{ $comment->user->name }}
            </p>
            <p>{{ $comment->content }}</p>
        </div>
        @endforeach
        <hr>
        @if(Auth::check())
        <form class="comment-box" method="POST" action="{{ route('comments.store', ['id' => $exhibition->id]) }}">
            @csrf
            @error('content')
            <p class="error-message">{{ $message }}</p>
            @enderror
            <textarea name="content" placeholder="商品へのコメント" class="comment-input"></textarea>
            <button type="submit" class="comment-submit">コメントを送信する</button>
        </form>
        @else
        <div class="comment-box">
            <textarea disabled placeholder="コメントするにはログインが必要です" class="comment-input"></textarea>
            <button disabled class="comment-submit">コメントを送信する</button>
        </div>
        @endif
    </div>
</div>
@endsection
