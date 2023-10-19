<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifyCode extends Model
{
    use HasFactory;

    protected $table = 'verify_codes';
    protected $fillable = ['users_id', 'otp', 'expire_at'];
}
