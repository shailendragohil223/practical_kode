<?php

namespace App\Http\Controllers\Admin\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use Spatie\Permission\Models\Role;

use Response;
use Illuminate\Support\Facades\Hash;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Permission;
class Apiuserauthenticate extends Controller
{

    public $successStatus = 200;
    public $success='successful';

    public function checkUSERISValid(Request $request)
    {
  
        $attr = $request->validate([
            'email' => 'required|string|email|',
            'password' => 'required|string|min:6'
        ]);
       
       
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){

            $user = Auth::User();
            // $success['token'] =  $user->createToken('MyApp')->accessToken;
            $success['token'] =  $user->createToken('token-name', ['server:update'])->plainTextToken;
            if( $success['token'] != " ")
            {
        
                $user =User::find($user->id);
                $user->token =  $success['token'];
                $user->save();
                $success['statusCode']= 200;
                $success['errorMsg']= "";
                $success['successMsg']= $this->success;
                $success['data']= ['userID'=>Auth::user()->id];
                
            }
           
            return response()->json( $success);

        }
        else{


            $success['statusCode']= 401;
            $success['errorMsg']= "These credentials do not match our records.";
            $success['successMsg']=" ";
            $success['data']= [];

          
            return response()->json($success);
        }
 
    }

    public function forgetPassword(Request $request)
    {
        if(isset($request->username))
        {
           
            $user['data'] =  User::where('email','=', $request->username)->first();
            if( $user['data'] === null) // not found so
            {   
                $success['userAvailable']=false;
                $success['statusCode']= 404;
                $success['errorMsg']= "User Not found";
                $success['successMsg']=""; 
            }
            else
            {
               

                $success['userAvailable']=true;
                $success['statusCode']= 200;
                $success['errorMsg']= "";
                $success['successMsg']=$this->success; 
                // $success['userID']= $user['data']->id;
                $success['data']= [];

            }

            return response()->json($success);

        }
        else
        {
            return response()->json(['error'=>'User Not found'], 401); // id not found 
        }
    }

    public function userRegister(Request $request)
    {

        $request->validate([
            'email' => 'required|string|email|',
            'password' => 'required|string|min:6',
            'mobile' => 'required',
        ]);
        
      $users = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' =>Hash::make($request->password),
        'mobile' => $request->mobile,
        ]);

      return [
          // "status" => 1,
          "data" => $users,
          "message" => "Role Created Successfuly",
      ];
    }

    public function resetPassword(Request $request)
    {
        $user_id =$request->userID;
        $email =$request->email;
        $password =$request->password;

        $attr = $request->validate([
            'userID' => 'required',
            'email' => 'required',
            'password' => 'required'
        ]);

        if(isset($user_id) && isset($email) && isset($password) )
        {   
             $user = User::where('id','=',$user_id)->where('email','=',$email)->first();

            if(!empty($user))
            {
                 $user->password =Hash::make($password);    
                $user->save();
                $success['statusCode']= 200;
                $success['errorMsg']= "";
                $success['successMsg']=$this->success; 
                $success['data']= ['userID'=>$user->id];
            }
            else
            {  
                $success['statusCode']= 404;
                $success['errorMsg']= "User not found";
                $success['successMsg']= "";
                $success['data']= [];
            }
              
        }
        else{
                $success['statusCode']= 404;
                $success['errorMsg']= "User not found";
                $success['successMsg']= "";
                $success['data']= [];
        }  
        return response()->json($success);


    }

    public function getUserDetails(Request $request)
    {
        $user_id= $request->userID;

        if(isset($user_id))
        {
             try {
                    //   $user = User::where('id','=',$user_id)->first(['token','email','userType as userType']);
                      $user = User::where('id','=',$user_id)->first(['token','email']);

                    if(isset($user))
                    {
                        if($user->token !=" " && isset($user->token))
                        {
                            $success['token']=$user->token;
                            $success['statusCode']= 200;
                            $success['errorMsg']= "";
                            $success['successMsg']=$this->success;
                            $success['data']=[

                                                
                                                'email'=>$user->email,
                                                // 'userType'=>checkRole($user->userType),
                                                'userID'=> $user_id,
                                            ]; 
                   
                        }
                        else
                        {
                            $success['statusCode']=204;
                            $success['errorMsg']= "User is Logout ";
                            $success['successMsg']= "";
                            $success['data']=[];
                        }
                            
                    }
                    else
                    {  
                        $success['statusCode']= 404;
                        $success['errorMsg']= "User not found";
                        $success['successMsg']= "";
                        $success['data']=[];
                    }
                      

                } 
                catch (ModelNotFoundException $exception) {


  
                    $success['statusCode']= 404;
                    $success['errorMsg']= "User not found";
                    $success['successMsg']="";
                    $success['data']=[];
                    
                }
                return response()->json($success);
        }
        else{
            return response()->json(['error'=>'User Not found'], 401); // id not found 
        }   
    }
    public function getUserMenu(Request $request)
    {
         $user_id= $request->userID;
        if(isset($user_id))
        {
            try {

                
                 $user = User::where('id','=',$user_id)->first();
                    if(isset($user))
                    {
                        if($user->token !=" " && isset($user->token))
                        {
                    
                            $centeral=[];
                            $parent_menu=[];
                            $id=$user_id;
                                    $parent_child=[];
                                            $userMenu =DB::table('users as t1')->
                                            join('role_has_permissions as t2','t2.role_id','=','t1.Role')->
                                            join('permissions as t3','t3.id','=','t2.permission_id')->get(['t3.*']);
                                    foreach( $userMenu as $key=>$val)
                                    { 
                                        if($val->moduleName != $id)
                                        {
                                        $perant[]=["id"=>$val->id,"Name"=>$val->moduleName];
                                        $id = $val->moduleName;
                                        }
                                    }
                                    $collection = new Collection();
                                    $collection =(isset($perant))?(object)$perant:$perant=[];
                                    $parent = $collection;   

                                    $permisson =  Permission::all();
                                    $parent_collect=[];
                                    $childs=[];
                                    foreach($parent as $per)
                                    {   
                                        $parent_collect[]=$per['Name'];
                                    
                                        foreach($permisson as $child)
                                        {
                                            $parent_collect[]=$per['Name'];
                                            if($child->moduleName == $per['Name'])
                                            {   
                                                $get_childname= explode("-",$child->name);
                                            
                                                $childs[$per['Name']][]= ucfirst( str_replace("_"," ",$get_childname[1]));
                                            }
                                            // $parent_collect[$per['Name']]=$childs;
                                        }
                                
                                    
                                    }
                                    $collection1 = new Collection();
                                    $UserMenu =(isset($childs))?(object)$childs:$childs=[];
                        
                                $user = User::where('id','=',$user_id)->first(['email','role as userType']);
                                $success['statusCode']= 200;
                                $success['errorMsg']= "";
                                $success['successMsg']=$this->success;;
                                
                                $success['data']=$UserMenu;
                                $success['userID']=  $user_id;
                        }
                        else
                        {
                            $success['statusCode']=204;
                            $success['errorMsg']= "User is Logout ";
                            $success['successMsg']= "";
                
                        }
                    } 
                    else
                    {
                        $success['statusCode']=404;
                        $success['errorMsg']= "User Not found ";
                        $success['successMsg']= "";
            
                    }
                }
                catch (ModelNotFoundException $exception) 
                {
                            $success['statusCode']= 404;
                            $success['errorMsg']= "Could'nt fetch User menu please contact to Admin.";
                            $success['successMsg']= "";
                            $success['userID']=  $user_id;
                }
                        
              
                return response()->json($success);
            return response()->json($success); 
         }
        else{
            return response()->json(['error'=>'User Not found'], 401); // id not found 
        }  
    }
    
    public function LogoutApiUser(Request $request)
    {
          $token = $request->token;
          dd($token);
         if(isset($token) && $token !=" ")
         {
      
            try {
                $user = User::where('token','=',$token)->first();

                if(isset($user) && !empty($user))
                {
                    $user->token=null;
                    $user->save();
                    $success['statusCode']= 200;
                    $success['errorMsg']=" ";
                    $success['successMsg']="User Logout";
                    $success['userID']="";
                }
                else
                {  
                    $success['statusCode']= 404;
                    $success['errorMsg']= "User not found";
                    $success['successMsg']= "";
                    $success['userID']=  $user_id;
                }
                  

            } 
            catch (ModelNotFoundException $exception) {



                $success['statusCode']= 404;
                $success['errorMsg']= "User not found";
                $success['successMsg']= "";
                $success['userID']=  $user_id;
                
            }
            return response()->json($success);
         }
         else{
             return response()->json(['error'=>'User Not found'], 401); // id not found 
         }  
    }
  
}
