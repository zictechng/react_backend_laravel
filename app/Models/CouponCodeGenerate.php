<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponCodeGenerate extends Model
{
    use HasFactory;
    protected $table = "coupon_code_generates";
    protected $fillable = [
        'partner_id',
        'generate_code',
        'code_amount',
        'partner_status',
        'code_status',
        'user_id',
        'code_use_date',
        'partner_reg_date',
        'partner_confirm_date',
        'partner_pay_code',
        'partner_batch_code',
    ];
}