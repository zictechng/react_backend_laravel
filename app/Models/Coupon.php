<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;
    protected $table = "coupons";
    protected $fillable = [
        'user_id',
        'coupon_code',
        'code_amt',
        'status_code',
        'batch_code',
    ];
}