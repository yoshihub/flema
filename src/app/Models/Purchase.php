<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment',
        'postCode',
        'address',
        'building',
        'exhibition_id',
        'user_id',
    ];

    // 取引に紐づくチャットメッセージ（時系列に並べることが多い）
    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    // 取引×ユーザーごとの既読基準（未読件数算出・新着ソートに使用）
    public function messageReads()
    {
        return $this->hasMany(MessageRead::class);
    }

    // この取引に紐づくレビュー（購入者→出品者、出品者→購入者の最大2件を想定）
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // この取引が紐づく出品（商品）
    public function exhibition()
    {
        return $this->belongsTo(Exhibition::class);
    }

    // 購入者（purchases.user_id を buyer として扱う想定）
    public function buyer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 出品者は exhibition->user から辿る（直接のbelongsToは張らない想定）
    // $purchase->exhibition->user で参照
}
