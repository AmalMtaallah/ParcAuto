<?php

namespace App;
use App\User;
use Illuminate\Database\Eloquent\Model;

class presence extends Model
{
    //

    public function user2()
    {
        return $this->belongsTo(User::class);
    }
}
