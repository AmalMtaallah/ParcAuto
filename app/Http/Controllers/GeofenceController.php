<?php

namespace App\Http\Controllers;

use App\geofence;
use App\Mission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GeofenceController extends Controller
{
    //
    public function add(Request $request){
        $mission=Mission::find($request->mission_id);
        if(is_null($mission)){
            return response()->json( ['message'=>"chauff non trouvee"],404);
        }
        
     
        $geofence=new geofence();
        $geofence->mission()->associate($mission);
        //echo($geofence->mission()->associate($mission));
        //$geofence->mission_id=$request->mission_id;
        $geofence->GeonfenceLat=$request->GeonfenceLat;
        $geofence->GeonfenceLong=$request->GeonfenceLong;
        $geofence->save();
        $mission->save();
    }

    public function getGeogence($id){
        $geofences=DB::table('geofences')
             ->select('geofences.*')
             ->Where('mission_id','=', $id)
             ->distinct()
             ->get();
             
       
        if(is_null($geofences)){
            return response()->json( ['message'=>"Geofence non trouvee"],404);
        }else
        return Response()->json($geofences);
    }
public function deleteGeo($id){
    $geofences=DB::table('geofences')
  
    ->select('geofences.*')
    ->Where('mission_id','=', $id)
    ->distinct()
    ->get();

    /*if(is_null($geofences)){
        return response()->json( ['message'=>"Geofence non trouvee"],404);
    }*/
    //$geofences=DB::table('geofences')->get();
 //$geofences->delete();
   foreach($geofences as $row){
       $r=geofence::find($row->id);
       $r->delete();
     // echo($r);
       
   }
  //return Response()->json($geofences);
}


}
