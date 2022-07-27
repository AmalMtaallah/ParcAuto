<?php

namespace App\Http\Controllers;
use App\presence;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PresenceController extends Controller
{
    //

    public function add(Request $request,$id){
        $d= Carbon::parse(Carbon::now())->format('H:i:s');
        $presence=new presence();
         $presence->dateJour= Carbon::now();
         $presence->user_id=$id;
         $presence->retardTime=$d;
     
        // $presence->save();
         return response()->json($d);
     }
 
}

