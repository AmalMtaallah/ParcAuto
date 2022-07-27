<?php

use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\ChauffeurController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GeofenceController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\MissionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehiculeController;
use App\Http\Controllers\PresenceController;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Routing\RouteGroup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return Auth::user();
});

Route::get('users', function(){
    return User::all();
});

Route::group(['namespace'=>'Api\Auth'], function(){
    //authentification
    Route::post('/login', 'AuthenticationController@login');

    Route::post('/logout', 'AuthenticationController@logout')->middleware('auth:api');
    Route::post('/register', 'RegisterController@register');
    Route::post('/forgot', 'ForgotPasswordController@forgot');
    Route::post('/reset', 'ForgotPasswordController@reset');
    //chauffeur
    Route::get('show',[ChauffeurController::class,'show']);
    Route::post('add',[chauffeurController::class,'add']);
    Route::delete('delete/{id}',[chauffeurController::class,'delete']);
    Route::get('index',[UserController::class,'index']);
    Route::get('getChauffeur/{id}',[chauffeurController::class,'getChauffById']);
    Route::post('UpdateChauffeur/{id}',[chauffeurController::class,'update']);
    Route::get('getpassword/{id}',[chauffeurController::class,'getpassword']);
    Route::get('chauffeursdispo',[ChauffeurController::class,'chauffeursdispo']);

    //vehicule 
    Route::any('addvehicule/',[VehiculeController::class,'add']);
    Route::delete('deletevehicule/{id}',[VehiculeController::class,'delete']);
    Route::get('getvehicule/{id}',[VehiculeController::class,'getvehiculeById']);
    Route::get('selectall',[VehiculeController::class,'selectall']);
    Route::post('updatevehicule/{id}',[VehiculeController::class,'update']);
    Route::get('vehiculesdispo',[VehiculeController::class,'vehiculesdispo']);

    //Mission
    Route::post('addMission/{id}',[MissionController::class,'add']); 
    Route::get('AllMission',[MissionController::class,'selectall']); 
    Route::delete('DeleteMission/{id}',[MissionController::class,'delete']); 
    Route::get('getmissionchauffeur/{id}',[MissionController::class,'getmissionchauffeur']); 
    Route::get('getMission/{id}',[MissionController::class,'getMissionById']); 
    Route::post('updateMission/{id}',[MissionController::class,'update']);
    
    
    
    //Notification
    Route::get('selectnotificationbyid/{id}',[NotificationController::class,'selectnotificationbyid']); 
    Route::get('nbrnotification/{id}',[NotificationController::class,'nbrnotification']); 

    
    //Selectionner la mission pour chaque chauffeur selon la date  
    Route::get('getdateactuelle',[MissionController::class,'getdateactuelle']); 
    Route::get('getmissionbydateactuelle/{id}',[MissionController::class,'getmissionbydateactuelle']); 
    Route::get('getmissionbydatechoisi/{id}/{date}',[MissionController::class,'getmissionbydatechoisi']); 
    Route::get('chercher/{cher}/{id}',[MissionController::class,'chercher']); 
    Route::get('chercherchauffeur/{cher}',[ChauffeurController::class,'chercher']);
    Route::get('cherchervehicule/{cher}',[VehiculeController::class,'cherchervehicule']); 
    

    //valider la disponibilité des véhicules et chauffeurs selon la date et le temps
    Route::post('DateMissionValidation/',[MissionController::class,'DateMissionValidation']); 

    //Lancer Mission
    
    Route::post('lancermission/{id}',[MissionController::class,'lancermission']); 
    //Vehicule in live Cart 
    Route::get('vehiculelivecart',[MissionController::class,'vehiculelivecart']); 

    Route::post('addGeogence',[GeofenceController::class,'add']);

    //Dashboard
    Route::get('nbchauffeur',[DashboardController::class,'nbchauffeur']); 
    Route::get('nbvehicules',[DashboardController::class,'nbvehicules']); 

    //envoie des alertes
    //Route::get('send-sms-notification/', [NotificationController::class,'sendSmsNotificaition']);
    Route::get('send-sms-notification/{mat}/{chauf}', [NotificationController::class,'sendSmsNotificaition']);
    Route::post('envoiealerte/{a}/{b}', [NotificationController::class,'envoiealerte']);
    Route::post('envoiealertedeux/{msg}', [NotificationController::class,'envoiealertedeux']);

    
    //historique des missions
    Route::get('missiontermine/{id}',[MissionController::class,'missiontermine']); 
    Route::get('AllTerminatedMission',[MissionController::class,'AllTerminatedMission']); 
    Route::post('setTerminatedMission/{distance}/{id}',[MissionController::class,'setTerminatedMission']); 
    Route::get('getmissionencours/{id}',[MissionController::class,'getmissionencours']); 

    //Maintenancettt 
    Route::get('assurance',[MaintenanceController::class,'assurances']);
    Route::get('visiteTechnique',[MaintenanceController::class,'visiteTechnique']);
    Route::get('getallvisitetechnique',[MaintenanceController::class,'getallvisitetechnique']);
    Route::get('pneux',[MaintenanceController::class,'pneux']);
    Route::get('allpneux',[MaintenanceController::class,'allpneux']);
    Route::get('allvidange',[MaintenanceController::class,'allvidange']);
    Route::get('vidange',[MaintenanceController::class,'vidange']);
    Route::post('updatepneux/',[MaintenanceController::class,'updatepneux']);
    Route::post('updatevidange/',[MaintenanceController::class,'updatevidange']);
    Route::post('updateAssurance/',[MaintenanceController::class,'updateAssurance']);
    Route::post('updateVisiteTechnique/',[MaintenanceController::class,'updateVisiteTechnique']);
    Route::get('getallassurance',[MaintenanceController::class,'getallassurance']);
    Route::get('gethistoriquevidange',[MaintenanceController::class,'gethistoriquevidange']);
    Route::get('gethistoriquepneu',[MaintenanceController::class,'gethistoriquepneu']);

    
    
    //les alertes admin
    Route::get('getalertes/', [NotificationController::class,'getalertes']);
    Route::get('nbalertes/', [NotificationController::class,'nbalertes']);

    //les notification
    
    Route::get('getnotificationadmin/', [NotificationController::class,'getnotificationadmin']);
    Route::get('nbnotfadmin/', [NotificationController::class,'nbnotfadmin']);
    Route::get('nbvehiculesencourslivr/', [DashboardController::class,'nbvehiculesencourslivr']);
    Route::get('dernierschauffeur/', [DashboardController::class,'dernierschauffeur']);
    
    Route::get('nbmissionchaquechauf/', [DashboardController::class,'nbmissionchaquechauf']);
    Route::get('nbdestination/', [DashboardController::class,'nbdestination']);
    Route::get('totalmission/', [DashboardController::class,'totalmission']);
    Route::get('totaldestination/', [DashboardController::class,'totaldestination']);
    Route::get('getchaufpermis/', [DashboardController::class,'getchaufpermis']);
    Route::get('nbmissionparmois/', [DashboardController::class,'nbmissionparmois']);
    Route::get('notifVidange/{ids}', [NotificationController::class,'notifVidange']);
    Route::get('notifChangementPneux/{ids}', [NotificationController::class,'notifChangementPneux']);
    Route::get('notifAssurance', [NotificationController::class,'notifAssurance']);
    Route::get('VisiteTechNotif', [NotificationController::class,'VisiteTechNotif']);
    Route::get('openNotif', [NotificationController::class,'openNotif']);
    Route::get('AllNotification', [NotificationController::class,'AllNotification']);
    Route::delete('suppNotif/{id}', [NotificationController::class,'suppNotif']);
    Route::get('SuppArchive', [NotificationController::class,'SuppArchive']);
    
    
    //get rappels admin
    Route::get('getrappel', [NotificationController::class,'getrappel']);
    Route::get('getnbrappel', [NotificationController::class,'getnbrappel']);

    //Route::get('AllAlertes', [NotificationController::class,'AllAlertes']);
    Route::get('openalertes', [NotificationController::class,'openalertes']);
    Route::get('openrappel', [NotificationController::class,'openrappel']);

    
    //changement password chauffeur
    Route::post('modifierpasswordprofile/{id}',[ChauffeurController::class,'modifierpasswordprofile']);
    //recherche missions admin
    Route::get('getmissionadminbydatechoisi/{date}',[MissionController::class,'getmissionadminbydatechoisi']); 

    //get missions de la semaine prochaine
    
    Route::get('getmissionsdelasemaproch/{id}',[MissionController::class,'getmissionsdelasemaineprochaine']); 
    Route::get('getmissionsdecetsemaine/{id}',[MissionController::class,'getmissionsdecetsemaine']); 
    Route::get('getmissionsdeuxsemainesprochaine/{id}',[MissionController::class,'getmissionsdeuxsemainesprochaine']);
    

    Route::get('vehiculeTraking/{id}',[MissionController::class,'vehiculeTraking']); 
    Route::get('missionEncours',[MissionController::class,'missionEncours']); 
    Route::get('sendSmsNotificaition/{msg}',[MissionController::class,'sendSmsNotificaition']);  

    Route::get('getGeogence/{id}',[GeofenceController::class,'getGeogence']);


    Route::get('missionCalender/{id}',[MissionController::class,'missionCalender']);
    
    Route::delete('deleteGeofence/{id}',[GeofenceController::class,'deleteGeo']);

    Route::post('addPresence/{id}',[PresenceController::class,'add']);

    

});

