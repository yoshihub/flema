<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'purchase_id',
        'user_id',
        'content',
        'image_path',
    ];

    // 紐づく取引
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    // 送信者（購入者 or 出品者）
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
