<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exhibition extends Model
{
    use HasFactory;

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
}
