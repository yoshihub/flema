@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase/index.css') }}">
@endsection

@section('content')
<form action="{{ route('purchase.store') }}" method="post" class="purchase-select-container">
    @csrf
    <div class="left-container">
        <div class="exhibition-card">
            <div class="exhibition-img">
                @if ($exhibition->exhibition_image)
                <img src="{{ asset('storage/exhibition_images/' . $exhibition->exhibition_image) }}" alt="プロフィール画像" class="image">
                @else
                <img src="{{ asset('images/default-icon.png') }}" alt="デフォルト画像" class="image">
                @endif
            </div>
            <div>
                <p class="exhibition-name">{{ $exhibition->name }}</p>
                <p class="exhibition-price">¥{{ number_format($exhibition->price) }}</p>
            </div>
        </div>

        <hr class="hr-design">
        <p class="item-title">支払い方法</p>
        <select name="payment" class="payment-select" id="payment-select">
            <option value="">選択してください</option>
            <option value="コンビニ払い">コンビニ払い</option>
            <option value="カード払い">カード払い</option>
        </select>
        @error('payment')
        <p class="error-message">{{ $message }}</p>
        @enderror

        <hr class="hr-design">
        <div class="shipping-address">
            <p class="item-title">配送先</p>
            <a href="/purchaseAddress">変更する</a>
        </div>
        <div class="purchase-address">
            <span>〒</span><input type="text" name="postCode" value="{{ $userAddress->postCode }}" style="display:inline-block;">
            @error('postCode')
            <p class="error-message">{{ $message }}</p>
            @enderror
            <input type="text" name="address" value="{{ $userAddress->address }}">
            @error('address')
            <p class="error-message">{{ $message }}</p>
            @enderror
            <input type="text" name="building" value="{{ $userAddress->building }}">
            @error('building')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>
        <hr class="hr-design">
    </div>
    <div class="right-container">
        <div class="payment-box">
            <div class="payment-row">
                <div class="label">商品代金</div>
                <div class="value">¥{{ number_format($exhibition->price) }}</div>
            </div>
            <div class="payment-row">
                <div class="label">支払い方法</div>
                <div class="value" id="payment-display"></div>
            </div>
        </div>
        <button type="submit" class="purchase-button">購入する</button>
    </div>
    <input type="hidden" name="exhibition_id" value="{{ $exhibition->id }}">
</form>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentSelect = document.getElementById('payment-select');
        const paymentDisplay = document.getElementById('payment-display');

        paymentSelect.addEventListener('change', function() {
            paymentDisplay.textContent = this.value;
        });
    });
</script>
@endsection
