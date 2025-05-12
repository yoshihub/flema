@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchaseAddress/index.css') }}">
@endsection

@section('content')
<div class="address-form-container">
    <h2>住所の変更</h2>
    <form action="" method="POST" class="address-form">
        @csrf

        <div class="form-group">
            <label for="postCode">郵便番号</label>
            <input type="text" id="postCode" name="postCode">
        </div>

        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" id="address" name="address">
        </div>

        <div class="form-group">
            <label for="building">建物名</label>
            <input type="text" id="building" name="building">
        </div>

        <button type="submit" class="submit-button">更新する</button>
    </form>
</div>
@endsection
