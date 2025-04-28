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
        <img src="{{$exhibition->image}}" alt="商品画像">
        <p>{{$exhibition->name}}</p>
    </div>
    @endforeach
</div>

@endsection
