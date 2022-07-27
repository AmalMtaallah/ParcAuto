<?php

namespace App\Http\Controllers;

use App\Mission;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function nbchauffeur(){
        $result = DB::table('users')
        ->where('users.usertype', 'LIKE', '%'. '0'. '%')
        ->count();
        return Response()->json($result);  
    }

    public function nbvehicules(){
        $result = DB::table('vehicules')
        ->count();
        return Response()->json($result);  
    }

    public function nbvehiculesencourslivr(){
        $date=Date::now();
        $dateformat= Carbon::parse($date)->format('Y-m-d');
        $result = DB::table('missions')
        ->where('missions.etat','en cours de livraison')
        ->where('missions.date',$dateformat)
        ->count();
        return Response()->json($result);
    }
    public function dernierschauffeur(){
        $users= DB::table('users')
            ->select('*')
            ->where('users.usertype','0')
            ->take(5)
            ->latest()
            ->get();
            return $users;
          }

     public function nbmissionchaquechauf(){

       $users= Mission::select(DB::raw('user_id as uid'), DB::raw('count(missions.user_id) as total'))
        ->groupBy('uid')
        ->get();
       return   $users;
     }  
     
     public function dernieresvehicules(){
     }

     public function nbdestination(){
        $result = DB::table('missions')
        ->select('adress', DB::raw('count(*) as total'))
        ->groupBy('adress')
        ->get();        
        return Response()->json($result);
     }

     public function totalmission(){
        $result = DB::table('missions')
       
        ->count();
        return Response()->json($result);
     }

     public function totaldestination(){
        $count = DB::table('missions')->count(DB::raw('DISTINCT adress'));
        return Response()->json($count);
     }

     public function getchaufpermis(){
        $date=Date::now();
        $annee=$date->format('Y');
        $result = DB::table('users')
        ->select('*')
        ->where('datefin', 'LIKE', '%'. $annee. '%')
        ->get();
        return Response()->json($result);
     }
     

     public function nbmissionparmois()
{   $date=Date::now();
    $annee=$date->format('Y');
    // $result = DB::table('missions')
    // ->select('(MONTH(date) ) as  month', DB::raw('count(*) as total') )
    // ->groupBy('month')
    // ->get(); 
    // return Response()->json($result);
    $result = DB::table('missions')
            ->select(DB::raw('count(*) as total'),DB::raw('YEAR(date) year, MONTH(date) month'))
            ->where('etat','terminee')
            ->groupby('year','month')
            ->havingRaw('year = ?', [$annee])
            ->get();
    return Response()->json($result);

    // return Mission::where('etat','terminee')->addSelect(DB::raw('(YEAR(date ) )  as year'))->addSelect(DB::raw('(MONTH(date) ) as  month'))->havingRaw('year = ?', [$annee])->distinct('month')->get();
}


}