<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryTransaction extends Model
{
    use HasFactory;
    protected $table = "history_transactions";
    protected $fillable = [
        'uid',
        'user_email',
        'status',
        'send_amt',
        'action_nature',
        'tid_code',
    ];
}