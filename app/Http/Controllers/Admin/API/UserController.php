<?php

namespace App\Http\Controllers\Admin\API;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Hash;
use Auth;
use DB;
class UserController extends Controller
{
   
    public $successStatus = 200;
    public $success='Successful';

    public function index(Request $request)
    {

        $token = $request->token;
        $user = User::where('token','=',$token)->first();
        if(isset($user))
        {
            
            // $data = User::get();
            $data1 = User::select('users.*');
            $data = collect($data1->get())->map(function($d){
                $d->image = 'asset_admin/user_img/'.$d->image;
                return $d;
            });
           
            $success['token']=$token;
            $success['statusCode']= 200;
            $success['errorMsg']  = "";
            $success['successMsg']= "User retrived succssfully";
            $success['data']= $data;     
            
        }
        else
        {
            $success['statusCode']=404;
            $success['errorMsg']= "User Not found ";
            $success['successMsg']= "";
        }
        return response()->json($success);
    }

    public function store(Request $request)
    {

        $token = $request->token;
        $user = User::where('token','=',$token)->first();
        if(isset($user))
        {
            $q=$users = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->Phone,
                'password' => bcrypt($request->password),
                'user_status' => $request->user_status,
            ]);
            $id = $q->id;
                        
            if(isset($_FILES['image']['tmp_name']))
            {
                $img =$request->file('image');
                if($_FILES['image']['tmp_name'] != ''){
                    $img =$request->file('image');
                    $imageName = time().'.'.$request->file->extension();
        
                    $target = public_path('asset_admin/user_img/').$imageName;
                    move_uploaded_file($_FILES['image']['tmp_name'],$target);
        
                    User::where('id',$id)->update([
                        'image' => $imageName
                    ]);
                }
            }

            $success['token']=$token;
            $success['statusCode']= 200;
            $success['errorMsg']  = "";
            $success['successMsg']= "user Created Successfuly";
            $success['data']= $user;     
        
        }
        else
        {
            $success['statusCode']=404;
            $success['errorMsg']= "User Not found ";
            $success['successMsg']= "";
        }
        return response()->json($success);
    }

    public function show(Request $request)
    {
        
        $token = $request->token;
        $user = User::where('token','=',$token)->first();
        $user->image = 'asset_admin/user_img/'.$user->image;

        if(!empty($user))
        {

            $edit_users =User::where('users.id',$request->id)->first();       
            
            $success['token']=$token;
            $success['statusCode']= 200;
            $success['errorMsg']  = "";
            $success['successMsg']= $this->success;
            $success['data']= $edit_users;     
          
        }
        else
        {
            $success['statusCode']=404;
            $success['errorMsg']= "User Not found ";
            $success['successMsg']= "";
        }
        return response()->json($success);
       
    }

    public function update(Request $request)
    {
       
        $token = $request->token;
        $user = User::where('token','=',$token)->first();
        if(isset($user))
        {
          
            $edit_users = User::where('id',$id)->update(['id'=>$request->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->Phone,
                'password' => bcrypt($request->password),
                'user_status' => $request->user_status,
            ]);
            
            if(isset($_FILES['image']['tmp_name']))
            {
                $img =$request->file('image');
                if($_FILES['image']['tmp_name'] != ''){
                    $img =$request->file('image');
                    $imageName = time().'.'.$request->file->extension();

                    $target = public_path('asset_admin/user_img/').$imageName;
                    move_uploaded_file($_FILES['image']['tmp_name'],$target);

                    User::where('id',$id)->update([
                    'image' => $imageName
                ]);
                }
            }
    
            $success['token']=$token;
            $success['statusCode']= 200;
            $success['errorMsg']  = "";
            $success['successMsg']= $this->success;
            $success['data']= ['Dashboard'=>$DashboardCounts,'userID'=> $user->id];     
           
        }
        else
        {
            $success['statusCode']=404;
            $success['errorMsg']= "User Not found ";
            $success['successMsg']= "";
        }
        return response()->json($success);
       
    }

    public function destroy(Request $request)
    {

        $token = $request->token;
        $user = User::where('token','=',$token)->first();
        if(isset($user))
        {
            
             $User = User::where('id',$id)->delete();
            
            $success['token']=$token;
            $success['statusCode']= 200;
            $success['errorMsg']  = "";
            $success['successMsg']= $this->success;
            $success['data']= [];     
           
        }
        else
        {
            $success['statusCode']=404;
            $success['errorMsg']= "User Not found ";
            $success['successMsg']= "";
        }
        return response()->json($success);
    }

}
