<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageRead extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'user_id',
        'last_read_at',
    ];

    // 紐づく取引
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    // 既読基準を持つユーザー（閲覧者）
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
