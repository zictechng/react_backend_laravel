<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashOut extends Model
{
    use HasFactory;
    protected $table = "cash_outs";
    protected $fillable = [
        'user_id',
        'name',
        'user_email',
        'user_phone',
        'amount_send',
        'note_send',
        'tid_code',
        'request_status',
    ];
}