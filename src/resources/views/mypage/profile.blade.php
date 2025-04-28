@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage/profile.css') }}">
@endsection

@section('content')
<div class="profile-container">
    <p class="title">プロフィール設定</p>
    <form class="form" action="/mypage/profile" method="post" enctype="multipart/form-data">
        @csrf

        <div class="img-group">
            @if ($user->profile_image)
            <img src="{{ asset('storage/profile_images/' . $user->profile_image) }}" alt="プロフィール画像" class="image">
            @else
            <img src="{{ asset('images/default-icon.png') }}" alt="デフォルト画像" class="image">
            @endif

            <div class="file-group">
                <label for="profile_image" class="file-label">画像を選択する</label>
                <input type="file" name="profile_image" id="profile_image" class="file-input">
                @error('profile_image')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="name">ユーザ名</label>
            <input type="text" name="name" id="name" value="{{ old('name') ?? optional($user)->name }}" />
            @error('name')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-group">
            <label for="postCode">郵便番号</label> <input type="text" name="postCode" id="postCode" value="{{ old('postCode') ?? optional($address)->postCode }}" />
            @error('postCode')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" id="address" value="{{ old('address') ?? optional($address)->address }}" />
            @error('address')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-group">
            <label for="building">建物名</label><input type=" text" id="building" name="building" value="{{ old('building') ?? optional($address)->building }}" />
        </div>
        @error('building')
        <p class="error-message">{{ $message }}</p>
        @enderror
        <div class="form-group">
            <button type="submit" class="button">
                更新する
            </button>
        </div>
    </form>
</div>
@endsection
