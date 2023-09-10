<?php

namespace App\Http\Controllers\Admin\API;

use App\Http\Controllers\Controller;
use App\Models\Admin\API\Blog;
use App\Models\User;
use Illuminate\Http\Request;
use DB;

class BlogController extends Controller
{
    public $successStatus = 200;
    public $success='Successful';

    public function index(Request $request)
    {
        // $id = $request->userID;

        $token = $request->token;
        $user = User::where('token','=',$token)->first();
        if(isset($user))
        {

            $blog = Blog::orderByDESC('id')->paginate(5);

            $blog->map(function ($blog) use ($user) {
              
                if($blog->image != NULL)
                {
                    $blog->image = 'asset_admin/blog_image/'.$blog->image;
                }
                
                $like = DB::table('blog_like')->where('blog_id',$blog->id)->where('user_id',$user->id)->count();
                if($like > 0)
                {
                    $blog->is_liked = true;
                }
                else
                {
                    $blog->is_liked = false;
                }
                return $blog;

            });

            
            $success['token']=$user->token;
            $success['statusCode']= 200;
            $success['errorMsg']  = "";
            $success['successMsg']= "Blog Retrived succssfully";
            $success['data']= $blog;  
        
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
        // return $request->file('company_logo');
        $token = $request->token;
        $user = User::where('token','=',$token)->first();
        if(isset($user))
        {
            $q= Blog::create([
                'title' => $request->title,
                'description' => $request->description,
            ]);
            $id = $q->id;

            if(isset($_FILES['image']['tmp_name']))
            {
                if($_FILES['image']['tmp_name'] != ''){
                    // $img =$request->file('image');
                    $imageName = time().'.'.$request->image->extension();
        
                    $target = public_path('asset_admin/blog_image/').$imageName;
                    move_uploaded_file($_FILES['image']['tmp_name'],$target);
        
                    Blog::where('id',$id)->update([
                        'image' => $imageName
                    ]);
                }
            }

            $success['token']=$user->token;
            $success['statusCode']= 200;
            $success['errorMsg']  = "";
            $success['successMsg']= "Blog Created succssfully";
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

    public function show(Request $request)
    {
        $token = $request->token;
        $user = User::where('token','=',$token)->first();
        if(isset($user))
        {
            $blog = Blog::where('id',$request->id)->first();

            $blog->image = 'asset_admin/blog_image/'.$blog->image;

            $like = DB::table('blog_like')->where('blog_id',$request->id)->where('user_id',$user->id)->count();
            if($like > 0)
            {
                $blog->is_liked = true;
            }
            else
            {
                $blog->is_liked = false;
            }
                
            $success['token']=$token;
            $success['statusCode']= 200;
            $success['errorMsg']  = "";
            $success['successMsg']= "Blog Retrived succssfully";
            $success['data']= $blog;     
           
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
           $blog = Blog::where('id',$request->id)->update([
                'title' => $request->title,
                'description' => $request->description,
                ]);
                

            if(isset($_FILES['image']['tmp_name']))
            {
                if($_FILES['image']['tmp_name'] != ''){
                    // $img =$request->file('image');
                    $imageName = time().'.'.$request->image->extension();
        
                    $target = public_path('asset_admin/blog_image/').$imageName;
                    move_uploaded_file($_FILES['image']['tmp_name'],$target);
        
                    Blog::where('id',$id)->update([
                        'image' => $imageName
                    ]);
                }
            }

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

    public function destroy(Request $request)
    {
        //
    }

    public function blog_like_toggle(Request $request)
    {
        $token = $request->token;
        $user = User::where('token','=',$token)->first();
        if(isset($user))
        {
            $blog =  DB::table('blog_like')->where('blog_id',$request->id)->where('user_id',$user->id)->count();
            if($blog == 0)
            {
                $blog = DB::table('blog_like')->insert([
                    'blog_id' => $request->id,
                    'user_id' => $user->id,
                    ]);
                    $success['successMsg']= "Successfully Liked Blog";
            }
            else
            {
                $blog =  DB::table('blog_like')->where('blog_id',$request->id)->where('user_id',$user->id)->delete();
                $success['successMsg']= "Successfully Unliked Blog";
            }
           
            $success['token']=$token;
            $success['statusCode']= 200;
            $success['errorMsg']  = "";
            // $success['successMsg']= $this->success;
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
