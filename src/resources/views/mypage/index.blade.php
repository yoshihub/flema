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
    <a href="/mypage" class="sell-link {{ request('page') !== 'mylist' ? 'active' : '' }}">出品した商品</a>
    <a href="/?page=mylist" class="purchase-link {{ request('page') === 'mylist' ? 'active' : '' }}">購入した商品</a>
</div>
<hr class="hr-line">
@endsection
