@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/chat/show.css') }}">
<style>
    .edit-container {
        max-width: 720px;
        margin: 24px auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .06);
        padding: 20px;
    }

    .edit-title {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 16px;
    }

    .current-preview {
        margin-bottom: 16px;
    }

    .current-preview .message-image {
        max-width: 240px;
        border-radius: 8px;
        display: block;
        margin-top: 8px;
    }

    .form-row {
        display: flex;
        gap: 8px;
    }

    .form-row .message-input {
        flex: 1;
    }

    .actions {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 16px;
    }

    .back-link {
        color: #2563eb;
        text-decoration: none;
    }

    .danger {
        color: #d93025;
    }

    .checkbox {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .validation-errors {
        color: #d93025;
        margin-bottom: 8px;
    }
</style>
@endsection

@section('content')
<div class="edit-container">
    <div class="edit-title">メッセージを編集</div>

    @if ($errors->has('content') || $errors->has('image'))
    <div class="validation-errors">
        @foreach ($errors->get('content') as $error)
        <div class="error-item">{{ $error }}</div>
        @endforeach
        @foreach ($errors->get('image') as $error)
        <div class="error-item">{{ $error }}</div>
        @endforeach
    </div>
    @endif

    <form action="{{ route('messages.update', ['purchase' => $purchase->id, 'message' => $message->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="current-preview">
            @if($message->image_path)
            <div>現在の画像</div>
            <img src="{{ asset('storage/chat_images/' . $message->image_path) }}" class="message-image" alt="現在の画像">
            <label class="checkbox"><input type="checkbox" name="remove_image" value="1">画像を削除する</label>
            @endif
        </div>

        <div class="form-row">
            <input type="text" name="content" value="{{ old('content', $message->content) }}" class="message-input" placeholder="本文を編集">
            <label for="image" class="image-upload-button">画像を差し替え</label>
            <input type="file" id="image" name="image" accept=".png,.jpeg" style="display:none;">
            <button type="submit" class="send-button">更新</button>
        </div>
    </form>

    <div class="actions">
        <a href="{{ route('purchase.chat', ['purchase' => $purchase->id]) }}" class="back-link">チャットに戻る</a>
        <form action="{{ route('messages.destroy', ['purchase' => $purchase->id, 'message' => $message->id]) }}" method="POST" class="inline-form">
            @csrf
            @method('DELETE')
            <button type="submit" class="action-link danger">このメッセージを削除</button>
        </form>
    </div>
</div>
@endsection
