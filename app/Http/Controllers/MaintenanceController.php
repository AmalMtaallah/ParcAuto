<?php

namespace App\Http\Controllers;

use App\HistoriqueAssurance;
use App\HistoriqueChangementPneux;
use App\HistoriqueMaintenance;
use App\vehicule;
use App\HistoriqueVidanges;
use App\HistoriqueVisitetech;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaintenanceController extends Controller
{
    //
    
    public function assurances(){
        
        $assurance=DB::table('vehicules')
       /* ->select('vehicules.id')
        ->Where('dernierAssurace', ">" ,'dernierAssurace'->addMonths(5))
        ->distinct()*/
        ->get();
        $array = array();
       
        foreach ($assurance as $row) {
 
         $id = $row->id;
         
         $expires =Carbon::createFromFormat('Y-m-d', $row->dernierAssurace);
             
           // $n =Carbon::createFromFormat('Y-m-d', $row->dernierAssurace);
         $newDate= $expires->addMonths(12);
         if( Carbon::now()>= $newDate->subWeek()){
             array_push($array, $id );
            
         }
       
                 }
                 $arrayAssurance=DB::table('vehicules')
                 ->select('vehicules.*')
                 ->WhereIn('id', $array)
                 ->distinct()
                 ->get();
                 if(is_null($arrayAssurance)){
                     return response()->json( ['message'=>"Data not found"],404);
                 }else{ return Response()->json($arrayAssurance);}
                
  }
 
  public function getallassurance(){
    $assurance=DB::table('vehicules')
    ->select('vehicules.*')
     ->distinct()
     ->get();
     return Response()->json($assurance);
  }
  public function getallvisitetechnique(){
    $visite=DB::table('vehicules')
    ->select('vehicules.*')
     ->distinct()
     ->get();
     return Response()->json($visite);
  }
   public function visiteTechnique(){
         
     $assurance=DB::table('vehicules')
     ->get();
     $array = array();
     $i=0;
     foreach ($assurance as $row) {
 
      $id = $row->id;
      $a=strtotime($row->datepremiere);
        $a=date('Y', $a);
        $a1=(string)$a;
        $a2=(string)Carbon::now()->format('Y');
        if( $a2-$a1>=10){
         $expires =Carbon::createFromFormat('Y-m-d', $row->dernierVisiteTechnique);
         
        // $n =Carbon::createFromFormat('Y-m-d', $row->dernierAssurace);
        $newDate= $expires->addMonths(6);
    
        if( Carbon::now()>= $newDate->subMonth()){
          array_push($array, $id );
          //$i++;
          
      }}else{
        $expires =Carbon::createFromFormat('Y-m-d', $row->dernierVisiteTechnique);
         
        // $n =Carbon::createFromFormat('Y-m-d', $row->dernierAssurace);
        $newDate= $expires->addMonths(12);
    
        if( Carbon::now()>= $newDate->subMonth()){
          array_push($array, $id );
          //$i++;
          
      }

      }
    
             }
             $arrayAssurance=DB::table('vehicules')
             ->select('vehicules.*')
             ->WhereIn('id', $array)
             ->distinct()
             ->get();
             return Response()->json($arrayAssurance);
 }
     public function pneux(){
         $pneux=DB::table('vehicules')
         ->select('vehicules.*')
          ->Where('pneuxKM', ">=" ,45000)
          ->distinct()
          ->get();
          return Response()->json($pneux);
     }
     
     public function allpneux(){
        $pneux=DB::table('vehicules')
        ->select('vehicules.*')
         ->distinct()
         ->get();
         return Response()->json($pneux);
    }
    public function allvidange(){
        $vidange=DB::table('vehicules')
        ->select('vehicules.*')
         ->distinct()
         ->get();
         return Response()->json($vidange);
    }
     public function vidange(){
         $vidange=DB::table('vehicules')
         ->select('vehicules.*')
          ->Where('vidangeKM', ">=" ,10000)
          ->distinct()
          ->get();
         
          return Response()->json($vidange);
         
     }
     public function updatepneux(Request $req){
         $vehicule = vehicule::find($req->id);
           $vehicule->pneuxKM=0;
           $histo=new HistoriqueMaintenance();
           $histo->vehicule_id=$vehicule->id;
           $histo->type="pneu";
           $histo->datemaintenance=Carbon::now()->format('Y-m-d');
           $histo->save();
          $vehicule->save();
          if($req->hasFile('file')){
            $file=$req->file('file');
            $extension=$file->getClientOriginalName();
           // $path=$req->file('file')->storeAs('public/vidange',$histo->id.'.'.$extension);
            $file->move('public/vidange',$extension);
            $histo->file=$extension;
            $histo->save();
     }
     }
     public function updatevidange(Request $req){
         $vehicule = vehicule::find($req->id);
           $vehicule->vidangeKM=0;
           $histo=new HistoriqueMaintenance();
           $histo->vehicule_id=$vehicule->id;
           $histo->type="vidange";
           $histo->datemaintenance=Carbon::now()->format('Y-m-d');
           $histo->save();
          $vehicule->save();
          if($req->hasFile('file')){
            $file=$req->file('file');
            $extension=$file->getClientOriginalName();
            $file->move('public/vidange',$extension);
            $histo->file=$extension;
            $histo->save();
     }
     }

     public function gethistoriquevidange(){
        $result = DB::table('vehicules')
        ->join('historique_maintenances','vehicules.id','historique_maintenances.vehicule_id')
        ->where('historique_maintenances.type','vidange')
        ->select('vehicules.*','historique_maintenances.datemaintenance')
        ->get();
        return Response()->json($result);
     }
     public function gethistoriquepneu(){
        $result = DB::table('vehicules')
        ->join('historique_maintenances','vehicules.id','historique_maintenances.vehicule_id')
        ->where('historique_maintenances.type','pneu')
        ->select('vehicules.*','historique_maintenances.datemaintenance')
        ->get();
        return Response()->json($result);
     }
 
     public function updateAssurance(Request $req){
         $vehicule = vehicule::find($req->id);
       
           $vehicule->dernierAssurace=Carbon::parse(Carbon::now())->format('Y-m-d');
           $vehicule->dateexpirationassurance=Carbon::parse($vehicule->dernierAssurace)->addMonths(12);
           $histo=new HistoriqueMaintenance();
           $histo->vehicule_id=$vehicule->id;
           $histo->type="assurance";
           $histo->datemaintenance=Carbon::now()->format('Y-m-d');
           $histo->save();
           $vehicule->save();
           if($req->hasFile('file')){
            $file=$req->file('file');
            $extension=$file->getClientOriginalName();
           // $path=$req->file('file')->storeAs('public/vidange',$histo->id.'.'.$extension);
            $file->move('public/vidange',$extension);
            $histo->file=$extension;
            $histo->save();

     }
    }
     public function updateVisiteTechnique(Request $req){
         $vehicule = vehicule::find($req->id);
         $a=strtotime($vehicule->datepremiere);
        $a=date('Y', $a);
        $a1=(string)$a;
        $a2=(string)Carbon::now()->format('Y');
        if( $a2-$a1>=10){
           $vehicule->dernierVisiteTechnique=Carbon::parse(Carbon::now())->format('Y-m-d');
           $vehicule->dateexpirationvisitetech=Carbon::parse($vehicule->dernierVisiteTechnique)->addMonths(6);
        }
        if( $a2-$a1<10){
            $vehicule->dernierVisiteTechnique=Carbon::parse(Carbon::now())->format('Y-m-d');
            $vehicule->dateexpirationvisitetech=Carbon::parse($vehicule->dernierVisiteTechnique)->addMonths(12);
         }
         $histo=new HistoriqueMaintenance();
         $histo->vehicule_id=$vehicule->id;
         $histo->type="visitetech";
         $histo->datemaintenance=Carbon::now()->format('Y-m-d');
         $histo->save();
         $vehicule->save();
         if($req->hasFile('file')){
            $file=$req->file('file');
            $extension=$file->getClientOriginalName();
           // $path=$req->file('file')->storeAs('public/vidange',$histo->id.'.'.$extension);
            $file->move('public/vidange',$extension);
            $histo->file=$extension;
            $histo->save();
     }
        }
 
}
