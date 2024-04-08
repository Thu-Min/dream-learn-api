<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VerificationCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'code', 'expire_at'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function generateVerificationCode($length, $user_id)
    {
        $min = pow(10, $length - 1);
        $max = pow(10, $length) - 1;
        $code = str_pad(mt_rand($min, $max), $length, '0', STR_PAD_LEFT);
        $hash_code = Hash::make($code);
        $expire_at = Carbon::now()->addMinutes(10);

        self::create([
            'user_id' => $user_id,
            'code' => $hash_code,
            'expire_at' => $expire_at,
        ]);

        return $code;
    }
}
