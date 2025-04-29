@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage/index.css') }}">
@endsection

@section('content')
<div class="profile-group">
    @if (Auth::user()->profile_image)
    <img src="{{ asset('storage/profile_images/' . $user->profile_image) }}" alt="プロフィール画像" class="image">
    @else
    <img src="{{ asset('images/default-icon.png') }}" alt="デフォルト画像" class="image">
    @endif

    <div class="user-info">
        <p>{{ Auth::user()->name }}</p>
    </div>

    <a href="/mypage/profile" class="edit-button">プロフィールを編集</a>
</div>
@endsection
