<?php

namespace App\Http\Controllers\API;

use App\Models\Post;
// use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['posts'] = Post::all();
        return $this->SendResponse($data,'All Post Data');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateUser = Validator::make(  
            $request->all(),
            [
                'title' => 'required',
                'description' => 'required',
                'image' => 'required|mimes:png,jpg,jpeg,gif'
            ]);
            if($validateUser->fails())
            {
                return $this->SendError('Validation Error', $validateUser->errors()->all()); 
            }
            else
            {
                $img = $request->image;
                $ext = $img->getClientOriginalExtension();
                $imageName = time().'.'.$ext;
                $img->move(public_path().'/uploads',$imageName);

                $post = Post::create([
                    'title' => $request->title,
                    'description' => $request->description,
                    'image' => $imageName
                ]);
            }
            return $this->SendResponse($post,'Post Created Sucessfully');  
        }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data['post'] = Post::select
            ('id','title','description','image')
            ->where(['id'=>$id])->get();
                            
        return $this->SendResponse($data,'Your Single Post');  

    }

    public function edit(string $id)
    {
        
    }

    public function update(Request $request, string $id)
    {
        $validateUser = Validator::make(  
            $request->all(),
            [
                'title' => 'required',
                'description' => 'required',
                'image' => 'required|mimes:png,jpg,jpeg,gif'
            ]);
            if($validateUser->fails())
            {
                // return response()->json([
                //      'status' => false,
                //      'message' => 'Validation Error',
                //      'error' => $validateUser->errors()->all()
                // ],401);
                return $this->SendError('Validation Error', $validateUser->errors()->all()); 
                
            }
            else
            {
                $postImage = Post::select('id','image')->where(['id'=>$id])->get();
                if($request->image !==' ')
                {
                    $path = public_path().'/uploads';
                    
                    if($postImage[0]->image !== ' ' && $postImage[0]->image !== null)
                    {
                        $old_file =  $path . $postImage[0]->image;
                        if(file_exists($old_file))
                        {
                            unlink($old_file);
                        }
                    } 

                    $img = $request->image;
                    $ext = $img->getClientOriginalExtension();
                    $imageName = time().'.'.$ext;
                    $img->move(public_path().'/uploads',$imageName);
                }
                else
                {
                    $imageName = $postImage->image;
                }

                $post = Post::where(['id'=>$id])->update([
                    'title' => $request->title,
                    'description' => $request->description,
                    'image' => $imageName
                ]);
            }
            return $this->SendResponse($post,'Updated Post Successfully');  

    }

    public function destroy(string $id)
    {

        $imagePath = Post::select('image')->where('id',$id)->get();
        $filepath = public_path().'/uploads/'. $imagePath[0]['image'];

        unlink($filepath);

        $post = Post::where('id',$id)->delete();
    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Your Post Deleted Successfully',
    //         'post' => $post
    //    ],status: 200);
       return $this->SendResponse($post,'Your Post Deleted Successfully');  
    }
}