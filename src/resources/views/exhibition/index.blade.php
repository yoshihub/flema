@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/exhibition/index.css') }}">
@endsection

@section('content')
<div class="link-group">
    <a href="/" class="index-link">おすすめ</a>
    <a href="#" class="mylist-link">マイリスト</a>
</div>
<hr class="hr-line">
<div class="card-list">
    @foreach($exhibitions as $exhibition)
    <div class="card">
        <a href="{{ route('exhibition.show', $exhibition->id) }}" class="card-link">
            @if ($exhibition->exhibition_image)
            <img src="{{ asset('storage/exhibition_images/' . $exhibition->exhibition_image) }}" alt="商品画像">
            @else
            <img src="{{ asset('images/default-icon.png') }}" alt="デフォルト画像">
            @endif
            <p class="product-name">{{$exhibition->name}}</p>
        </a>
    </div>
    @endforeach
</div>

@endsection
