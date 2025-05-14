@extends('layouts.app')

@section('content')
<div class="container">
    <div class="verification-box">
        <h2>メールアドレスの確認</h2>

        <div class="card-body">
            @if (session('resent'))
            <div class="success-message">
                新しい確認リンクがあなたのメールアドレスに送信されました。
            </div>
            @endif

            <p>
                メールアドレスの確認が必要です。<br>
                確認用メールをご確認ください。
            </p>
            <p>
                もし確認メールが届いていない場合、
            </p>

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="resend-button">
                    確認メールを再送信
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
    .verification-box {
        max-width: 600px;
        margin: 50px auto;
        padding: 30px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    }

    .success-message {
        background-color: #d4edda;
        color: #155724;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .resend-button {
        background-color: #3490dc;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        margin-top: 10px;
    }

    .resend-button:hover {
        background-color: #227dc7;
    }
</style>
@endsection
