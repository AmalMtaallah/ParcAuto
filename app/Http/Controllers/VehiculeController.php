<?php

namespace App\Http\Controllers;

use App\vehicule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VehiculeController extends Controller
{
    public function add(Request $request){
        $validator=Validator::make($request->all(),[
            'marque'=>'required|string|max:255',
            'matricule' =>'required|string|max:255|unique:vehicules',
            'modele'=>'required|string|max:255',
            'couleur'=>'required|string|max:255',           
            'puissance'=>'required|string|max:255', 
            'dateentre' =>'required|string|max:255',  
            'datepremiere' =>'required|string|max:255',         
            'energie'=>'required|string|max:255',
            'maxreservoire'=>'required',
            'consomation_moy'=>'required',
            'dernierVisiteTechnique'=>'required',
            'dernierAssurace'=>'required',

            

        ]);

       
        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'message'=>$validator->errors()->first()
            ]);
        }
        else{
       $v=new vehicule();
        $v->marque=$request->marque;
        $v->modele=$request->modele;
        $v->matricule=$request->matricule;
        $v->couleur=$request->couleur;
        $v->puissance=$request->puissance;
        $v->datepremiere=$request->datepremiere;
        $v->dateentre=$request->dateentre;
        $v->energie=$request->energie;
        $v->maxreservoire=$request->maxreservoire;
        $v->consomation_moy=$request->consomation_moy;
        $v->dernierVisiteTechnique=$request->dernierVisiteTechnique;
        $v->dernierAssurace=$request->dernierAssurace;
        $v->save();
        return response()->json($v);
    }
}
public function delete(Request $req,$id){

    $vehicule=vehicule::find($id);
    if(is_null($vehicule)){
        return response()->json( ['message'=>"vehicule non trouvee"],404);
    }
    $vehicule->delete();
    return response(null,200);
}
public function getvehiculeById(Request $request){
    $result = vehicule::find($request->id);
    if(is_null($result)){
        
        return response()->json(['Result' => 'Chauffeur not found']);
       }
       else
       return Response()->json($result);
 }
 public function selectall(Request $request){

    $result = DB::table('vehicules')
    ->orderBy('id', 'DESC')
    ->get();
     if(count($result)){
      return Response()->json($result);
     }
     else
     return response()->json(['Result' => 'No Data not found']);
 }

 public function cherchervehicule($recherche){
    $missionsrecherchee= DB::table('vehicules')
    ->where(function($query) use ($recherche)
    {
        $query->where('marque','LIKE', '%'. $recherche. '%');
        $query->orWhere('matricule','LIKE', '%'. $recherche. '%');

        $query->orWhere('modele','LIKE', '%'. $recherche. '%')  ;        
    

    } )
    ->get();
   

return $missionsrecherchee;
}
 public function vehiculesdispo(){

    $vehidispo = vehicule::where('dispovehi', 'LIKE', '%'. '0'. '%')
    ->get();
    
     if(count($vehidispo)){
      return Response()->json($vehidispo);
     }
     else
     return response()->json(['Result' => 'No Data not found']);
 }
 public function update(Request $request,$id) {
    $vehicule = vehicule::find($id);
     if(is_null($vehicule)){
         
         return response()->json(['Result' => 'Vehicule not found']);
        }
        else
       $vehicule->marque=$request->marque;
       $vehicule->modele=$request->modele;
       $vehicule->matricule=$request->matricule;
       $vehicule->couleur=$request->couleur;
       $vehicule->nbr=$request->nbr;
        $vehicule->puissance=$request->puissance;
        $vehicule->datepremiere=$request->datepremiere;
        $vehicule->dateentre=$request->dateentre;
        $vehicule->boite=$request->boite;
      $vehicule->save();
     return response()->json($vehicule);

 }

}
