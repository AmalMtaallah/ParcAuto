<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistoriqueMaintenance extends Model
{
    //
    public function vehicule()
    {
        return $this->belongsTo(vehicule::class);
    }
}
