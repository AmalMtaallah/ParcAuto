<?php

namespace App\Http\Controllers;

use App\Mission;
use App\notification;
use App\User;
use App\vehicule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MissionController extends Controller
{
    //rod bellek y dali
    public function add($id,Request $request){
            $validator=Validator::make($request->all(),[
                //'destination'=>'required|string|max:255',
                'adress'=>'required',   
                'chargement'=>'required',   
                'tel_Dest'=>'required',   
                //'longitude'=>'required',
                //'latitude'=>'required', 
                'date'=>'required',    
                'departTime'=>'required', 
                'duration'=>'required', 
                'user_id'=>'required', 
                'vehicule_id'=>'required',     
            ]);
    
           
            if($validator->fails()){
                return response()->json([
                    'success'=>false,
                    'message'=>$validator->errors()->first()
                ]);
         }
            $mission=new Mission();
            $mission->chargement=$request->chargement;
            $mission->adress=$request->adress;
            $mission->tel_Dest=$request->tel_Dest;
            $mission->description=$request->description;
            //$mission->distanceparcouru=$request->distanceparcouru;
            $mission->date=Carbon::createFromFormat('Y/m/d', $request->date)->format('Y/m/d');
            $mission->departTime=Carbon::createFromFormat('H:i', $request->departTime)->format('H:i:s');
    
            $mission->arriveTime=Carbon::parse($mission->departTime)->addSeconds($request->duration);
       
           $user=User::find($request->user_id);
           
            if(is_null($user)){
                return response()->json( ['message'=>"chauff non trouvee"],404);
            }
            $vehicule=vehicule::find($request->vehicule_id);
            if(is_null($vehicule)){
                return response()->json( ['message'=>"vehicule non trouvee"],404);
            }
            $mission->user()->associate($user);
            $mission->vehicule()->associate($vehicule);
            $mission->save();
        

            $user->save();
            $vehicule->save();  
            $notification=new notification();
            $notification->ref=Str::random(5);
            $notification->message="Vous avez une mission vers ".$request->adress." Le ".$request->date." à ".$request->departTime;
            $notification->sender_id=$id;
            $notification->receiver_id=$request->user_id;
            $notification->save();      
            return response()->json($mission);

        }
    
    public function selectall(Request $request){

        $result = Mission::with('user')
        ->with('vehicule')
        ->orderBy('id', 'DESC')
        ->get();
         if(count($result)){
          return Response()->json($result);
         }
         else
         return response()->json(['Result' => 'No Data not found']);
     }

        public function delete(Request $req,$id){
            $mission=Mission::find($id);
            if(is_null($mission)){
                return response()->json( ['message'=>"Mission non trouvee"],404);
            }
            $mission->delete();
            return response(null,200);
        
    }
    public function getmissionchauffeur($id){
        $result = DB::table('missions')
        ->where('missions.user_id',$id)
        ->join('vehicules','missions.vehicule_id','vehicules.id')
        ->select('missions.*','missions.id as missions_id','vehicules.marque')
        ->get();
        return Response()->json($result);
    
    }
    public function DateMissionValidation(Request $request){
        $date=Carbon::createFromFormat('Y-m-d', $request->d)->format('Y-m-d');
        $depT=Carbon::createFromFormat('H:i', $request->td)->format('H:i:s');
        $arrT=Carbon::parse($depT)->addSeconds($request->dur);
     
        $depT= Carbon::parse($depT)->format('H:i:s');
        
      
           $mission=Mission::with('user')->with('vehicule')->get();
         
           $users_test = User::join('missions', 'missions.user_id', '=', 'users.id')
           ->select('users.id')
           ->where('date', 'LIKE', '%'. $date. '%')->where('departTime', "<" , Carbon::parse($arrT))
           ->Where('arriveTime', ">" , Carbon::parse($depT))
           ->distinct()
           ->get();

          $vehicules_test = vehicule::join('missions', 'missions.vehicule_id', '=', 'vehicules.id')
           ->select('vehicules.id')
           ->where('date', 'LIKE', '%'. $date. '%')->where('departTime', "<" , Carbon::parse($arrT))
           ->Where('arriveTime', ">" , Carbon::parse($depT))
           ->distinct()
           ->get();
           if(count($users_test)){

          $list_user=DB::table('users')                 
           ->select('users.*')
           ->where('usertype', 0)
           ->whereNotIn('id',$users_test)->get();
        
        $list_vehicules=DB::table('vehicules')                 
           ->select('vehicules.*')
           ->whereNotIn('id',$vehicules_test)->get();
           return response()->json([$list_user,$list_vehicules]);
    }else{
        $Allchauffeurs = (new ChauffeurController)->chauffeursdispo();
        $Allvehicules = (new VehiculeController)->vehiculesdispo();
   
    return Response()->json([$Allchauffeurs->original->toArray(),$Allvehicules->original->toArray()]);
    }        
    }

        public function getdateactuelle(){
            $date=Date::now();
            $dateformat= Carbon::parse($date)->format('Y-m-d');
            return Response()->json([$dateformat]);

            }

        public function getmissionbydateactuelle($id){
            $date=Date::now();
            $dateformat= Carbon::parse($date)->format('Y-m-d');
            $result = DB::table('missions')
            ->where('missions.date',$dateformat)
            ->where('missions.user_id',$id)
            ->where('missions.etat','En attente')
            ->join('vehicules','missions.vehicule_id','vehicules.id')
            ->select('missions.*','missions.id as missions_id','vehicules.marque','vehicules.matricule')
            ->get();
            return Response()->json($result);
        }    
        
        public function getmissionencours($id){
            $result = DB::table('missions')
            ->where('missions.user_id',$id)
            ->where('missions.etat','en cours de livraison')
            ->join('vehicules','missions.vehicule_id','vehicules.id')
            ->select('missions.*','missions.id as missions_id','vehicules.marque','vehicules.matricule')
            ->get();
            return Response()->json($result);
        }  
       // mrigula
        public function getmissionsdelasemaineprochaine($id){
            $date=Date::now();
            $dateformat= Carbon::parse($date)->format('Y-m-d');
            $start = Carbon::create($dateformat);
            $end2=Carbon::parse($start)->addDays(7);
            $end1 = Carbon::parse($start)->addDays(6);
            $end5 = Carbon::parse($start)->addDays(5);
            $end6 = Carbon::parse($start)->addDays(4);
            $end3=Carbon::parse($start)->addDays(2);
            $end4=Carbon::parse($start)->addDays(3);
            $ref=Carbon::parse($start)->addDays(13);
            $result = DB::table('missions')
            ->where('date', "<",$ref)
            ->where('date', ">",$dateformat)

            ->where('missions.user_id',$id)
            ->where('missions.etat','En attente')
            ->where(function($query) use ($end1,$end2,$end3,$end4,$end5,$end6)
        {
            $query->where('date', ">",$end1);
            $query->orWhere('date', ">",$end2);
            $query->orWhere('date', ">",$end3);        
            $query->orWhere('date', ">",$end4);
            $query->orWhere('date', ">",$end5);
            $query->orWhere('date', ">",$end6);
        } )
            ->join('vehicules','missions.vehicule_id','vehicules.id')
            ->select('missions.*','missions.id as missions_id','vehicules.marque','vehicules.matricule')
            ->get();
            return Response()->json($result);
        }
        //mrigula
        public function getmissionsdecetsemaine($id){
            $date=Date::now();
           $dateformat= Carbon::parse($date)->format('Y-m-d');

           $start = Carbon::create($dateformat);
           $end1 = Carbon::parse($start)->addDays(5);
           $end2=Carbon::parse($start)->addDays(2);
           $end3=Carbon::parse($start)->addDays(4);
           $end4=Carbon::parse($start)->addDays(3);
           
           $result = DB::table('missions')
           ->where('date', ">",$dateformat)
           ->where('missions.user_id',$id)
           ->where('missions.etat','En attente')
           ->where(function($query) use ($end1,$end2,$end3,$end4)
           {
               $query->where('date', "<",$end1);
               $query->orWhere('date', "<",$end2);
               $query->orWhere('date', "<",$end3);        
               $query->orWhere('date', "<",$end4);
               
           } )
           
           ->join('vehicules','missions.vehicule_id','vehicules.id')
           ->select('missions.*','missions.id as missions_id','vehicules.marque','vehicules.matricule')
           ->get();
           return Response()->json($result);
       }
      // mrigula
       public function getmissionsdeuxsemainesprochaine($id){
        $date=Date::now();
        $dateformat= Carbon::parse($date)->format('Y-m-d');
        $start = Carbon::create($dateformat);
        $end1 = Carbon::parse($start)->addDays(12);
        $end2=Carbon::parse($start)->addDays(13);
        $end3=Carbon::parse($start)->addDays(11);
        $end4=Carbon::parse($start)->addDays(10);
        $end5=Carbon::parse($start)->addDays(9);
        $end6=Carbon::parse($start)->addDays(8);
        $ref=Carbon::parse($start)->addWeeks(3);
        
        $result = DB::table('missions')
        ->where('date', ">",$dateformat)
        ->where('missions.user_id',$id)
        ->where('missions.etat','En attente')
        ->where('date', "<",$ref)
        ->where(function($query) use ($end1,$end2,$end3,$end4,$end5,$end6)
        {
            $query->where('date', ">",$end1);
            $query->orWhere('date', ">",$end2);
            $query->orWhere('date', ">",$end3);        
            $query->orWhere('date', ">",$end4);
            $query->orWhere('date', ">",$end5);
            $query->orWhere('date', ">",$end6);
        } )
         ->join('vehicules','missions.vehicule_id','vehicules.id')
        ->select('missions.*','missions.id as missions_id','vehicules.marque','vehicules.matricule')
        ->get();
        return Response()->json($result);
       }

   
        public function getmissionbydatechoisi($id,$date){
            $date=Carbon::parse($date)->format('Y-m-d');
            $result = DB::table('missions')
            ->where('missions.date',$date)
            ->where('missions.user_id',$id)
            ->select('missions.*')
            ->get();
            return Response()->json($result);
        }


        public function getmissionadminbydatechoisi($date){
            $date=Carbon::parse($date)->format('Y-m-d');
            $result = DB::table('missions')
            ->where('missions.date',$date)
            ->join('vehicules','missions.vehicule_id','vehicules.id')
            ->join('users','missions.user_id','users.id')

            ->select('missions.*','missions.id as missions_id','vehicules.marque','vehicules.matricule','users.name')
            ->get();
            return Response()->json($result);
        }

     
        
        public function chercher($recherche,$id){
            $missionsrecherchee= DB::table('missions')
            ->where('missions.user_id',$id)
            ->where(function($query) use ($recherche)
            {
                $query->where('adress','LIKE', '%'. $recherche. '%');
                $query->orWhere('date','LIKE', '%'. $recherche. '%');

                $query->orWhere('etat','LIKE', '%'. $recherche. '%')  ;        
                $query->orWhere('departTime','LIKE', '%'. $recherche. '%');
               
            } )
            ->get();
           

        return $missionsrecherchee;
        }


        

        public function lancermission($id){
            $mission=Mission::find($id);
            $user=User::find($mission->user_id);
            $vehicule=vehicule::find($mission->vehicule_id);
            $mission->etat="en cours de livraison";
            $notification=new notification();
            $notification->ref=Str::random(5);
            $notification->message="Mission en cours de livraison par  ".$user->name." avec ".$vehicule->matricule;
            //$notification->sender_id=0;
            $notification->receiver_id=1;
            $notification->status="en cours de livraison";

            $notification->save(); 
            $mission->save();

        }

        public function vehiculelivecart(){
            $result = DB::table('missions')
            ->where('missions.etat','en cours de livraison')
            ->join('vehicules','missions.vehicule_id','vehicules.id')
            ->join('users','missions.user_id','users.id')
            ->select('vehicules.marque','vehicules.matricule','users.name')
            ->get();
            return Response()->json($result);
        }
        public function getMissionById(Request $request){
            $result = Mission::find($request->id);
            if(is_null($result)){
                
                return response()->json(['Result' => 'Livraison not found']);
               }
               else
               return Response()->json($result);
         }
           
         public function update(Request $request,$id) {
            $mission = Mission::find($id);
             if(is_null($mission)){
                 
                 return response()->json(['Result' => 'Livraison not found']);
                }
                else{
                $validator=Validator::make($request->all(),[
                    //'destination'=>'required|string|max:255',
                    'adress'=>'required',   
                    'chargement'=>'required',   
                    'tel_Dest'=>'required',   
                    //'longitude'=>'required',
                    //'latitude'=>'required', 
                    'date'=>'required',    
                    'departTime'=>'required', 
                    'duration'=>'required', 
                    'user_id'=>'required', 
                    'vehicule_id'=>'required',     
                ]);
        
               
                if($validator->fails()){
                    return response()->json([
                        'success'=>false,
                        'message'=>$validator->errors()->first()
                    ]);
                }
               
                $mission->chargement=$request->chargement;
                $mission->adress=$request->adress;
                $mission->tel_Dest=$request->tel_Dest;
                $mission->description=$request->description;
              
                $mission->date=Carbon::createFromFormat('Y/m/d', $request->date)->format('Y/m/d');
                $mission->departTime=Carbon::createFromFormat('H:i:s', $request->departTime)->format('H:i:s');
        
                $mission->arriveTime=Carbon::parse($mission->departTime)->addSeconds($request->duration);
           
               $user=User::find($request->user_id);
               
                if(is_null($user)){
                    return response()->json( ['message'=>"chauff non trouvee"],404);
                }
                $vehicule=vehicule::find($request->vehicule_id);
                if(is_null($vehicule)){
                    return response()->json( ['message'=>"vehicule non trouvee"],404);
                }
                $mission->user()->associate($user);
                $mission->vehicule()->associate($vehicule);
                $mission->save();
                return response()->json($mission);
     
                 }
     
            }
            
            public function missiontermine($id){
                $missions= DB::table('missions')
                ->where('missions.user_id',$id)
                ->where('missions.etat','termineé')
                ->join('vehicules','missions.vehicule_id','vehicules.id')
                ->select('missions.*','missions.id as missions_id','vehicules.marque','vehicules.matricule')
                ->get();
                return Response()->json($missions);
            }
            public Function AllTerminatedMission(){
                $missions= DB::table('missions')
               
                ->where('missions.etat','termineé')
                ->join('vehicules','missions.vehicule_id','vehicules.id')
                ->join('users','missions.user_id','users.id')
                ->select('missions.*','missions.id as missions_id','vehicules.marque','vehicules.matricule','users.name')
                ->get();
                return Response()->json($missions);
            }

            public function setTerminatedMission($distance,$id,Request $request){
                $mission = Mission::find($id);
                $mission->etat='termineé';
                $vehicule=vehicule::find($mission->vehicule_id);
                $vehicule->pneuxKM=$vehicule->pneuxKM+$distance*2;
                //$vehicule->kmparcouru=152.2;
                $mission->distanceparcouru=$distance*2;
                $vehicule->vidangeKM=$vehicule->vidangeKM+$distance*2;
                $vehicule->kmparcouru=$vehicule->kmparcouru+$distance*2;
                $vehicule->save();
                $mission->save();
                $notification=new notification();
                $notification->ref=Str::random(5);
                $notification->message="Mission vers ".$mission->adress." est termineé";
                $notification->receiver_id=1;
                $notification->status="termineé";
                $notification->save(); 
            }

            public function missionEncours(){
                $result =Mission::with('vehicule')
                ->with('user')
                ->where('etat','en cours de livraison')
               
                ->get();
                return Response()->json($result);
            }
            public function vehiculeTraking($id){
                $result =  Mission::with('user')->with('vehicule')
            ->where('id','=',$id)
            //->where('etat','en cours de livraison')
                
                
                ->get();
                return Response()->json($result);
            }
public function sendSmsNotificaition($msg)
{
   /* Nexmo::message()->send([
        'to' => '51076117',
        'from' => 'John Doe',
        'text' => 'A simple hello message sent from Vonage SMS API'
    ]);*/

    $basic  = new \Vonage\Client\Credentials\Basic("70a7985d", "WiV7G7i29uPmtVcf");
    $client = new \Vonage\Client($basic);

    $response = $client->sms()->send(
        new \Vonage\SMS\Message\SMS("21651076117" ,  "BRAND_NAME" , $msg)
    );
    
    $message = $response->current();
    
    if ($message->getStatus() == 0) {
        echo "The message was sent successfully\n";
    } else {
        echo "The message failed with status: " . $message->getStatus() . "\n";
    }

    dd('SMS message has been delivered.');

    
}

public function missionCalender($id){ 
    $ar=[]; $result = DB::table('missions') ->where('missions.user_id',$id) 
    ->join('vehicules','missions.vehicule_id','vehicules.id') 
    ->select('missions.*','missions.id as missions_id','vehicules.marque') ->get(); 
    foreach ($result as $row) { 
        //'id'=>$row->id,
     $event='Livraison à '.$row->adress.' : '.$row->departTime. ' h';
      array_push($ar, array('id'=>$row->id,'title' => 'Livraison à '.$row->adress.' : '.\Carbon\Carbon::createFromFormat('H:i:s',$row->departTime)->format('h:i'). ' h', 'start' => $row->date.'T'.$row->departTime,'end'=>$row->date.'T'.$row->arriveTime)); 
    }
       return Response()->json($ar); }
    }
