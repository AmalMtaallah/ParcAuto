<?php

namespace App\Http\Controllers;

use App\notification;
use App\User;
use App\vehicule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    public function selectnotificationbyid($id) {
        
        $result = DB::table('notifications')
        ->where('notifications.receiver_id',$id)
        ->where('notifications.status','0')
        ->where('notifications.etat','0')
        ->orderBy('id', 'DESC')
        ->select('notifications.*','notifications.id as notifications_id')
        ->get();
        return Response()->json($result);
    }
   
    // public function AllNotification() {
    //     $result = DB::table('notifications')->orderBy('id', 'DESC')->get();
    //  if(count($result)){
    //   return Response()->json($result);
    //  }
    //  else
    //  return response()->json(['Result' => 'No Data  found']);
    // }

    public function AllNotification() {
        $result = DB::table('notifications')
        ->where('notifications.status','0')
        ->orderBy('id', 'DESC')
        ->get();
     if(count($result)){
      return Response()->json($result);
     }
     else
     return response()->json(['Result' => 'No Data  found']);
    }

    // public function AllAlertes() {
    //     $result = DB::table('notifications')
    //     ->where('notifications.status','alerte')
    //     ->orderBy('id', 'DESC')
    //     ->get();
    //  if(count($result)){
    //   return Response()->json($result);
    //  }
    //  else
    //  return response()->json(['Result' => 'No Data  found']);
    // }

    public function nbrnotification($id){
        $result = DB::table('notifications')
        ->where('notifications.receiver_id',$id)
        ->where('notifications.status','0')
        ->where('notifications.etat','0')
        ->count();
        return Response()->json($result);
    }

    public function openNotif(){
        $notif= DB::table('notifications')
        ->where('notifications.status','en cours de livraison')
        ->orWhere('notifications.status','termineé')
        ->where('notifications.etat','0')
        ->orderBy('notifications.id', 'DESC')
        ->get();
        if(is_null($notif)){
            return response()->json(['Result' => 'Notification not found']);
           }
           else{
            foreach ($notif as $row) {
                $id = $row->id;
                $n=notification::find($id);
                $n->etat="1";
                $n->save();
            }
 
              
           }
    }
    public function openalertes(){
        $alert= DB::table('notifications')
        ->where('notifications.status','alerte')
        ->where('notifications.etat','0')
        ->orderBy('id', 'DESC')
        ->get();
        if(is_null($alert)){
            return response()->json(['Result' => 'Notification not found']);
           }
           else{
            foreach ($alert as $row) {
                $id = $row->id;
                $n=notification::find($id);
                $n->etat="1";
                $n->save();
            }
 
              
           }
    }

    public function openrappel(){
        $alert= DB::table('notifications')
        ->where('notifications.status','rappel')
        ->where('notifications.etat','0')
        ->orderBy('notifications.id', 'DESC')
        ->get();
        if(is_null($alert)){
            return response()->json(['Result' => 'Notification not found']);
           }
           else{
            foreach ($alert as $row) {
                $id = $row->id;
                $n=notification::find($id);
                $n->etat="1";
                $n->save();
            }
 
              
           }
    }
   
   
    public function suppNotif($id){
        $notif=notification::find($id);
        if(is_null($notif)){
            return response()->json( ['message'=>"notification  not  found"],404);
        }
        $notif->delete();
        return response(null,200);
    }
    public function SuppArchive(){
        $year=Carbon::now()->format('Y');
        $year=$year-2;
        //echo($year);
        $notif= DB::table('notifications')
        ->whereYear('created_at', '<=', $year)
        ->get();
        if(is_null($notif)){
             
            return response()->json(['Result' => 'Data not found']);
           }
           else{
               //echo($notif);
           foreach ($notif as $row) {
               $n=notification::find($row->id);
               //echo($n);
               $n->delete();
            }
           }
    }
    public function sendSmsNotificaition($mat,$chauf)
    {
        $basic  = new \Vonage\Client\Credentials\Basic('ec3e9bdf', 'oJljGuJ7GTdU9P7R');
        $client = new \Vonage\Client($basic);
 
        $response = $client->sms()->send(
            new \Vonage\SMS\Message\SMS("21626734350", "BRAND_NAME", 'A text message sent using the Nexmo SMS API')
        );
        
        $message = $response->current();
        
        if ($message->getStatus() == 0) {
            echo "The message was sent successfully\n";
        } else {
            echo "The message failed with status: " . $message->getStatus() . "\n";
        }
    }

    public function envoiealerte($mat,$chauf){
        $notification=new notification();
        $notification->ref=Str::random(5);
        $notification->message="Vehicule ".$mat." conduite par ".$chauf."à dépassé la vitesse maximale";
        $notification->receiver_id= 1;
        $notification->status="alerte";
        $notification->save();
    }

    public function envoiealertedeux($msg){
        $notification=new notification();
        $notification->ref=Str::random(5);
        $notification->message=$msg;
        $notification->receiver_id= 1;
        $notification->status="alerte";
        $notification->save();
    }
   
    public Function notifVidange($ids){
        $vehicule=DB::table('vehicules')
        ->select('vehicules.*')
        ->Where('id','=',$ids)
          ->Where('vidangeKM', ">=" ,10000.00)
          ->distinct()
          ->get();
      //  echo($vehicule);
        foreach ($vehicule as $row) {
 
            $mat = $row->matricule;
            $message='VIDANGE : '.$mat.' à despasse le 10.000 KM/Parcourue , ';
            $notification=new notification();
            $notification->ref=Str::random(5);
            $notification->message=$message;
            $notification->receiver_id=1;
            $notification->status="rappel";
            $notification->save();
          }
    }
    public Function notifChangementPneux($ids){
        $vehicule=DB::table('vehicules')
        ->select('vehicules.*')
        ->Where('id','=',$ids)
          ->Where('pneuxKM', ">=" ,45000.00)
          ->distinct()
          ->get();
      //  echo($vehicule);
        foreach ($vehicule as $row) {
 
            $mat = $row->matricule;
            $message='CHANGEMENT PNEUX : '.$mat.' à despasse le 45.000 KM/Parcourue , ';
            $notification=new notification();
            $notification->ref=Str::random(5);
            $notification->message=$message;
            $notification->receiver_id=1;
            $notification->status="rappel";
            $notification->save();
          }
    }
    public function notifAssurance(){
        $vehicules=DB::table('vehicules')
        /* ->select('vehicules.id')
         ->Where('dernierAssurace', ">" ,'dernierAssurace'->addMonths(5))
         ->distinct()*/
         ->get();
        
         foreach ($vehicules as $row) {
     
          $id = $row->id;
          //echo($id);
          
                $expires =Carbon::createFromFormat('Y-m-d', $row->dernierAssurace);
                    //echo($expires.'****'); 
                    // $n =Carbon::createFromFormat('Y-m-d', $row->dernierAssurace);
                $newDate= $expires->addMonths(12);
                
            
                if( Carbon::now()>= $newDate->subWeek()){
                
                $vehiculeAssurance=vehicule::find($id);
            
                    $vehiculeAssurance->dateexpirationassurance=$newDate->addWeek();

                    $vehiculeAssurance->save();
                   // echo($row->matricule);
                   // echo($expires);
                    $title='ASSURANCE: '.$row->matricule.'avant le'. $ex=$expires->format('Y-m-d');
                
                $notif=DB::table('notifications')
                    ->select('notifications.id')
                ->Where('message','=',$title)
                    //->Where(strpos($row->matricule, 'message'))
                    ->distinct()
                    ->get();
                    if(count($notif)){
                       // echo('no');
                    }else{
                        $notification=new notification();
                        $notification->ref=Str::random(5);
                        $notification->message=$title;
                        $notification->receiver_id=1;
                        $notification->status="rappel";
                        $notification->save();
                      
                    }
                
                
                    
                }
                
  }
                    
 }
 public function VisiteTechNotif(){
    $vehicules=DB::table('vehicules')->get();  
    foreach ($vehicules as $row) {
        $id = $row->id;
        $a=strtotime($row->datepremiere);
        $a=date('Y', $a);
        $a1=(string)$a;
        $a2=(string)Carbon::now()->format('Y');
        if( $a2-$a1>=10){
            $expires =Carbon::createFromFormat('Y-m-d', $row->dernierVisiteTechnique);
            $newDate= $expires->addMonths(6);
            if( Carbon::now()>= $newDate->subMonth()){
                //echo($newDate);
                $veh=vehicule::find($id);
                $veh->dateexpirationvisitetech=$newDate->addMonth();
                $veh->save();
                $title='VISITE TECHNIQUE: Le véhicule: '.$row->matricule.'avant le '. $newDate->format('Y-m-d');
                $notif=DB::table('notifications')
                ->select('notifications.id')
                ->Where('message','=',$title)
                ->distinct()
                ->get(); 
                if(count($notif)){
                    //echo('no');
                }else{
                    $notification=new notification();
                    $notification->ref=Str::random(5);
                    $notification->message=$title;
                    $notification->receiver_id=1;
                    $notification->status="rappel";
                    $notification->save();
               
                    }
            }
            
    
        }else{
            
            $expires =Carbon::createFromFormat('Y-m-d', $row->dernierVisiteTechnique);
            $newDate= $expires->addMonths(12);
            if( Carbon::now()>= $newDate->subMonth()){
                $veh=vehicule::find($id);
                $veh->dateexpirationvisitetech=$newDate->addMonth();
                $veh->save();
                $title='VISITE TECHNIQUE:'.$row->matricule.'avant le'. $newDate->format('Y-m-d');
                $notif=DB::table('notifications')
                ->select('notifications.id')
                ->Where('message','=',$title)
                ->distinct()
                ->get();
                if(count($notif)){
                    // echo('no');
                    }else{
                            $notification=new notification();
                            $notification->ref=Str::random(5);
                            $notification->message=$title;
                            $notification->receiver_id=1;
                            $notification->status="rappel";
                            $notification->save();
                        
                        }
               
            }
        }
    }
 }

 public function nbalertes(){
    $result = DB::table('notifications')
    ->where('notifications.status','alerte')
    ->where('notifications.etat','0')
    ->count();
    return Response()->json($result);

}
public function getalertes(){
    $result = DB::table('notifications')
    ->where('notifications.status','alerte')
    ->orderBy('id', 'DESC')
    ->select('notifications.*')
    ->get();
    return Response()->json($result);
}

public function getnotificationadmin(){
    $result = DB::table('notifications')
    ->where('notifications.status','en cours de livraison')
    ->orwhere('notifications.status','termineé')
    ->orderBy('id', 'DESC')
    ->select('notifications.*')
    ->get();
    return Response()->json($result);
}
public function nbnotfadmin(){
    $result = DB::table('notifications')
    // ->where('notifications.status','en cours de livraison')
    // ->where('notifications.status','termineé')
    ->where('notifications.etat','0')
    ->where(function($q) {
        $q->where('notifications.status','termineé')
          ->orWhere('notifications.status','en cours de livraison');
    })
    ->count();
    return Response()->json($result);
}

public function getrappel() {
        
    $result = DB::table('notifications')
    ->where('notifications.status','rappel')
    ->orderBy('notifications.id', 'DESC')

    ->select('notifications.*','notifications.id as notifications_id')
    ->get();
    return Response()->json($result);
}

public function getnbrappel() {
        
    $result = DB::table('notifications')
    ->where('notifications.status','rappel')
    ->where('notifications.etat','0')
    ->count();
    return Response()->json($result);
}
 
}

