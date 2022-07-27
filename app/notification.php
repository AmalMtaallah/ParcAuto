<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class notification extends Model
{
    protected $fillable= [
        'ref',
        'message',
        'sender_id',
        'receiver_id',
        'status'
        
    ];
}
