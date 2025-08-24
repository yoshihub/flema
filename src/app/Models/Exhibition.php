<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exhibition extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'exhibition_image',
        'brand',
        'explanation',
        'price',
        'is_sold',
        'condition_id',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'integer',
        'is_sold' => 'boolean',
    ];

    /**
     * 出品者のリレーション
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    // 出品者
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'exhibition_user');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // この商品に紐づく取引（1商品=1取引を想定するなら hasOne）
    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }
}
