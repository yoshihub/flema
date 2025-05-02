@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell/index.css') }}">
@endsection

@section('content')
<div class="form-container">
    <form action="/sell" method="post" enctype="multipart/form-data">
        @csrf
        <h2>商品の出品</h2>

        <div class="form-group image-upload">
            <label>商品画像</label>
            <input type="file" name="exhibition_image">
        </div>

        <div class="form-group">
            <label>カテゴリー</label>
            <div class="category-tags">
                @foreach ($categories as $index => $category)
                <label>
                    <input type="checkbox" name="categories[]" value="{{ $category->id }}">
                    {{ $category->content }}
                </label>
                @endforeach
            </div>
        </div>

        <div class="form-group">
            <label for="condition">商品の状態</label>
            <select name="condition" id="condition">
                @foreach($conditions as $condition)
                <option value="{{ $condition->id }}">{{ $condition->content }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>商品名</label>
            <input type="text" name="name">
        </div>
        <div class="form-group">
            <label>ブランド名</label>
            <input type="text" name="brand">
        </div>
        <div class="form-group">
            <label>商品の説明</label>
            <textarea name="explanation"></textarea>
        </div>

        <div class="form-group">
            <label>販売価格</label>
            <input type="number" name="price" placeholder="¥">
        </div>

        <div class="form-group">
            <button type="submit" class="submit-button">出品する</button>
        </div>
    </form>
</div>
@endsection
