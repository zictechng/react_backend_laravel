<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferFunds extends Model
{
    use HasFactory;
    protected $table = "transfer_funds";
    protected $fillable = [
        'user_id',
        'receiver_id',
        'sender_email',
        'sender_name',
        'receiver_email',
        'receiver_name',
        'status',
        'amt_send',
        'tran_code',
        'reciever_acct_status',
        'sender_acct_status2',
        'note_purpose',
    ];
}