<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'reviewer_id',
        'reviewee_id',
        'rating',
    ];

    // 対象の取引
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    // レビューをした人
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    // レビューされた人（プロフィールの平均表示対象）
    public function reviewee()
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }
}
