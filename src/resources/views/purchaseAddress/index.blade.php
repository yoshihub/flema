@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchaseAddress/index.css') }}">
@endsection

@section('content')
<div class="address-form-container">
    <h2>住所の変更</h2>
    <form action="{{ route('purchase.address.store') }}" method="POST" class="address-form">
        @csrf

        <div class="form-group">
            <label for="postCode">郵便番号</label>
            <input type="text" id="postCode" name="postCode" value="{{ old('postCode', Auth::user()->address ? Auth::user()->address->postCode : '') }}">
            @error('postCode')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" id="address" name="address" value="{{ old('address', Auth::user()->address ? Auth::user()->address->address : '') }}">
            @error('address')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="building">建物名</label>
            <input type="text" id="building" name="building" value="{{ old('building', Auth::user()->address ? Auth::user()->address->building : '') }}">
            @error('building')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>
        <input type="hidden" name="name" value="{{ Auth::user()->name }}">
        <input type="hidden" name="exhibition_id" value="{{ $exhibition->id }}">

        <button type="submit" class="submit-button">更新する</button>
    </form>
</div>
@endsection
