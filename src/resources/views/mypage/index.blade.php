@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage/index.css') }}">
@endsection

@section('content')
<div class="profile-group">
    @if (Auth::user()->profile_image)
    <img src="{{ asset('storage/profile_images/' . Auth::user()->profile_image) }}" alt="プロフィール画像" class="image">
    @else
    <img src="{{ asset('images/default-icon.png') }}" alt="デフォルト画像" class="image">
    @endif

    <div class="user-info">
        <p>{{ Auth::user()->name }}</p>
    </div>

    <a href="/mypage/profile" class="edit-button">プロフィールを編集</a>
</div>

<div class="link-group">
    <a href="/mypage?tab=sell" class="sell-link {{ request('tab') === 'sell' ? 'active' : '' }}">出品した商品</a>
    <a href="/mypage?tab=buy" class="purchase-link {{ request('tab') === 'buy' ? 'active' : '' }}">購入した商品</a>
</div>
<hr class="hr-line">
<div class="card-list">
    @foreach($exhibitions as $exhibition)
    <div class="card">
        @if ($exhibition->exhibition_image)
        <img src="{{ asset('storage/exhibition_images/' . $exhibition->exhibition_image) }}" alt="商品画像">
        @else
        <img src="{{ asset('images/default-icon.png') }}" alt="デフォルト画像">
        @endif
        <p class="product-name">{{$exhibition->name}}</p>
    </div>
    @endforeach
</div>
@endsection
