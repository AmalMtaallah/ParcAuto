<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ChauffeurController extends Controller
{
    public function add(Request $request){
        $validator=Validator::make($request->all(),[
            'name'=>'required|string|max:255',
            'email'=>'required|string|email|max:255|unique:users',
            'tel'=>'required|string|max:255',
            'adress'=>'required|string|max:255',
            'prenom'=>'required|string|max:255',
            'cin'=>'required|string|max:255|unique:users',
            'numpermis'=>'required|string|max:255',
            'datesortie'=>'required',
            'datefin'=>'required|after:datesortie',
            'datenaiss'=>'required',
           
            




           
           
        ]);

       
        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'message'=>$validator->errors()->first()
            ]);
        }
        $password = Str::random(8);
        $email=$request->email;
       // $p=Product::find($request->id);
       $p=new User();
        $p->name=$request->name;
        $p->tel=$request->tel;
        $p->email=$request->email;
        $p->adress=$request->adress;
        $p->prenom=$request->prenom;
        $p->cin=$request->cin;
        $p->datenaiss=$request->datenaiss;
        $p->datesortie=$request->datesortie;
        $p->datefin=$request->datefin;
        $p->numpermis=$request->numpermis;

        $p->password=bcrypt($password);
        $p->save();
        Mail::send('mail.send_password_chauffeur', ['password'=>$password,'email'=>$email], function ($message) use($email){
            $message->to($email);
            $message->subject('Your Password');
        });
   
       if($request->hasFile('image')){
          
            $file =    $request->file('image');
        $uploadPath ="public/image";
        $originaleImage =$file->getClientOriginalName();
        $file->move($uploadPath,$originaleImage);
        $p->image =$originaleImage;
        $p->save();
        }

        //send email
      
    }
    public function delete(Request $req,$id){

        $user=User::find($id);
        if(is_null($user)){
            return response()->json( ['message'=>"produit non trouvee"],404);
        }
        $user->delete();
        return response(null,200);
    }
    
    public function update(Request $request,$id) {
        $user = User::find($id);
         if(is_null($user)){
             
             return response()->json(['Result' => 'Chauffeur not found']);
            }
            else
           $user->name=$request->name;
           $user->tel=$request->tel;
           $user->email=$request->email;
           $user->adress=$request->adress;
           $user->cin=$request->cin;
           $user->prenom=$request->prenom;


          $user->save();
        // $url="http://localhost:8000/storage/";
          if($request->hasFile('image')){
            $file =    $request->file('image');
            $uploadPath ="public/image";
            $originaleImage =$file->getClientOriginalName();
            $file->move($uploadPath,$originaleImage);
            $user->image =$originaleImage;
            $user->save();
                    return response()->json([
               'success'=>true,
                        'message'=>"You have successfully created "
                    ]);
           }
         return response()->json($user);
 
     }
    public function show(Request $request){

        $result = User::where('usertype', 'LIKE', '%'. '0'. '%')
        ->orderBy('id', 'DESC')
        ->get();
         if(count($result)){
          return Response()->json($result);
         }
         else
         return response()->json(['Result' => 'No Data not found']);
     }
     

     public function chercher($recherche){
        $missionsrecherchee= DB::table('users')
        ->where('users.usertype','0')
        ->where(function($query) use ($recherche)
        {
            $query->where('adress','LIKE', '%'. $recherche. '%');
            $query->orWhere('name','LIKE', '%'. $recherche. '%');

            $query->orWhere('prenom','LIKE', '%'. $recherche. '%')  ;        
            $query->orWhere('email','LIKE', '%'. $recherche. '%');
            $query->orWhere('cin','LIKE', '%'. $recherche. '%');
            $query->orWhere('numpermis','LIKE', '%'. $recherche. '%');

        } )
        ->get();
       

    return $missionsrecherchee;
    }
     public function chauffeursdispo(){

        $chaufdispo = User::where('usertype', 'LIKE', '%'. '0'. '%')
        ->where('dispochau', 'LIKE', '%'. '0'. '%')
        ->get();
        
         if(count($chaufdispo)){
          return Response()->json($chaufdispo);
         }
         else
         return response()->json(['Result' => 'No Data not found']);
     }
     
     public function getChauffById(Request $request){
        $result = User::find($request->id);
        if(is_null($result)){
            
            return response()->json(['Result' => 'Chauffeur not found']);
           }
           else
           return Response()->json($result);
     }

     public function modifierpasswordprofile(Request $request ,$id)
{
    $validated =$request->validate([

        'password' => [
            'required',
        ],
        'password_confirmation' => 'required|same:password',
    ]);
   $ancmdp= $request->ancienPassword;
    $user = User::find($id);
     $trouve = Hash::check($ancmdp,$user->password);

     if($trouve)
     {
         $newpass=Hash::make($request->password);
       $user->update(['password' =>$newpass]);
        return true;

     }
     else{
        return false;

     }
    }
    
}
