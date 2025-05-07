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
            @error('exhibition_image')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <p class="detail-text">商品の詳細</p>
        <hr>

        <div class="form-group">
            <label>カテゴリー</label>
            <div class="category-tags">
                @foreach ($categories as $category)
                <input type="checkbox" id="category-{{ $category->id }}" name="categories[]" value="{{ $category->id }}" class="category-checkbox" {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                <label for="category-{{ $category->id }}" class="category-label">
                    {{ $category->content }}
                </label>
                @endforeach
            </div>
            @error('categories')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="condition">商品の状態</label>
            <select name="condition" id="condition">
                @foreach($conditions as $condition)
                <option value="{{ $condition->id }}" {{ old('condition') == $condition->id ? 'selected' : '' }}>{{ $condition->content }}</option>
                @endforeach
            </select>
            @error('condition')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label>商品名</label>
            <input type="text" name="name" value="{{ old('name') }}">
            @error('name')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-group">
            <label>ブランド名</label>
            <input type="text" name="brand" value="{{ old('brand') }}">
            @error('brand')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-group">
            <label>商品の説明</label>
            <textarea name="explanation">{{ old('explanation') }}</textarea>
            @error('explanation')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label>販売価格</label>
            <input type="number" name="price" placeholder="¥" value="{{ old('price') }}">
            @error('price')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <button type="submit" class="submit-button">出品する</button>
        </div>
    </form>
</div>
@endsection
