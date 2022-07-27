<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class geofence extends Model
{
    public function mission()
    {
        return $this->belongsTo(Mission::class);
    }

 

}
