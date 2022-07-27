<?php

namespace App;
use App\Mission;
use App\HistoriqueAssurance;
use App\HistoriqueVidanges;
use App\HistoriqueVisitetech;
use App\HistoriqueChangementPneux;



use Illuminate\Database\Eloquent\Model;

class vehicule extends Model
{
    protected $fillable = [
        'marque', 'modele', 'matricule','couleur','puissance','datepremiere','dateentre','energie','maxreservoire','consomation_moy','dernierVisiteTechnique','dernierAssurace'
    ];
    public function missionVehicule()
    {
        return $this->hasMany(Mission::class);
    }

    public function vidangevehi()
    {
        return $this->hasMany(HistoriqueVidanges::class);
    }
    public function changementpneu()
    {
        return $this->hasMany(HistoriqueChangementPneux::class);
    }
    public function assurance()
    {
        return $this->hasMany(HistoriqueAssurance::class);
    }
    public function visitetech()
    {
        return $this->hasMany(HistoriqueVisitetech::class);
    }
    public function maintenance()
    {
        return $this->hasMany(HistoriqueMaintenance::class);
    }
}
