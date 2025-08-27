@component('mail::message')
# 取引完了のご連絡

{{ $seller->name }} 様

以下の商品の取引が購入者により完了されました。

- 商品名: {{ $exhibition->name }}
- 価格: ￥{{ number_format($exhibition->price) }}
- 購入者: {{ $buyer->name }} 様
- 完了日時: {{ optional($purchase->completed_at)->format('Y-m-d H:i') }}

ご利用ありがとうございます。

{{ config('app.name') }}
@endcomponent
