<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'provider',
        'user_id'
    ];
}
