@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/exhibition/index.css') }}">
@endsection

@section('content')
<div class="link-group">
    <a href="/" class="index-link {{ request('page') !== 'mylist' ? 'active' : '' }}">おすすめ</a>
    <a href="/?page=mylist" class="mylist-link {{ request('page') === 'mylist' ? 'active' : '' }}">マイリスト</a>
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
            <p class="product-name">
                {{$exhibition->name}}
                @if($exhibition->is_sold)
                <span class="sold">Sold</span>
                @endif
            </p>
        </a>
    </div>
    @endforeach
</div>

@endsection
