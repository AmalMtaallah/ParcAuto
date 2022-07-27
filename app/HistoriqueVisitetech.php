<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistoriqueVisitetech extends Model
{
    //
    public function vehicule()
    {
        return $this->belongsTo(vehicule::class);
    }
}
