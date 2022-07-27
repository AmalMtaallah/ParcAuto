<?php

namespace App;
use App\User;
use App\vehicule;

use Illuminate\Database\Eloquent\Model;

class Mission extends Model
{
    //
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicule()
    {
        return $this->belongsTo(vehicule::class);
    }
    public function geofence()
    {
        return $this->hasMany(Geofence::class);
    } 
    
}
