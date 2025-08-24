<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function address()
    {
        return $this->hasOne(UserAddress::class);
    }

    // 自分が送った取引メッセージ
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // 自分が既読基準を持つ取引（未読件数の集計などで使用）
    public function messageReads()
    {
        return $this->hasMany(MessageRead::class);
    }

    // 自分が購入した取引
    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'user_id');
    }

    // 自分が出品した商品（Exhibition 側の user_id は seller）
    public function exhibitions()
    {
        return $this->hasMany(Exhibition::class, 'user_id');
    }

    // 自分が与えたレビュー（reviewer_id = 自分）
    public function reviewsGiven()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    // 自分が受け取ったレビュー（reviewee_id = 自分）→ プロフィールの平均表示に使用
    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }
}
