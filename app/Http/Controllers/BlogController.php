<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Media;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'blog_name' => 'required|string|unique:blogs',
            'category' => 'required|string',
            'slug' => 'required|string',
            'status' => 'required|string',
            'description' => 'required|string',
            'image' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'msg'=>$validator->errors()->all(),
                'severity'=>'danger'
            ]);
        }else{
            $blog = new Blog;  
            $blog->blog_name = $request->blog_name;
            $blog->cats_ids = $request->category;
            $blog->category_id = 1;
            $blog->slug = $request->slug;
            $blog->status = $request->status;
            $blog->description = $request->description;
            $blog->image = $request->image;
            $blog->save();
            return response()->json([
                'success'=>true,
                'msg'=>['Blog added successfully'],
                'severity'=>'success',
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request){
        return $request->id ? Blog::find($request->id): Media::join('blogs','blogs.image','=','media.id')->orderBy('blogs.created_at', 'desc')->paginate(5);
    }

    public function relateBlog(Request $request){
        return $request->cats_id ? Media::join('blogs','blogs.image','=','media.id')->where('cats_ids','like','%'.$request->cats_id.'%')->get(): Blog::all();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function edit(Blog $blog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // return $request->all();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'category' => 'required|string',
            'slug' => 'required|string',
            'status' => 'required|string',
            'description' => 'required|string',
            'image' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'msg'=>$validator->errors()->all(),
                'severity'=>'danger'
            ]);
        }else{
            $blog = Blog::where('id',$request->id)->update([
                'blog_name'=>$request->name,
                'cats_ids'=>$request->category,
                'category_id'=>1,
                'slug'=>$request->slug,
                'status'=>$request->status,
                'description'=>$request->description,
                'image'=>$request->image
            ]); 
            if($blog){
                return response()->json([
                    'success'=>true,
                    'msg'=>['Blog updated successfully'],
                    'severity'=>'success',
                ]);
            }else{
                return response()->json([
                    'success'=>false,
                    'msg'=>['Unable to update blog'],
                    'severity'=>'danger',
                ]);
            }
            
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $blog = Blog::where('id',$request->id)->first();
        if($blog){
            $delete = Blog::where('id',$request->id)->delete();
            if($delete){
                return response()->json([
                    'success'=>true,
                    'msg'=>'Blog deleted successfully',
                    'severity'=>'success',
                ]);
            }else{
                return response()->json([
                    'success'=>false,
                    'msg'=>'Unable to remove blog!',
                    'severity'=>'danger',
                ]);
            }
        }else{
            return response()->json([
                'success'=>false,
                'msg'=>'Unable to find this blog!',
                'severity'=>'danger',
            ]);
        }
    }

    public function latestBlog(){
        return Media::join('blogs','blogs.image','=','media.id')->orderBy('blogs.created_at', 'desc')->limit(2)->get(); 
    }

    public function BlogCats(Request $request){
        // return $request->id;
        return BlogCategory::whereIn('id',$request->id)->get();
    }
}
