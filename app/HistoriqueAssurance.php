<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistoriqueAssurance extends Model
{
    //
    public function vehicule()
    {
        return $this->belongsTo(vehicule::class);
    }
}
