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
}
