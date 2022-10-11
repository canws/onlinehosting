<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlogCategoryController extends Controller
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
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:blog_categories',
            'status' => 'required|string'
        ]);

        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'msg'=>$validator->errors()->all(),
                'severity'=>'danger',
                'summary'=>'Error Message'
            ]);
        }else{
            $category = new BlogCategory;  
                $category->name = $request->name;
                $category->status = $request->status;
            $category->save();
            return response()->json([
                'success'=>true,
                'msg'=>['Catetory added successfully'],
                'severity'=>'success',
                'summary'=>'Success Message'
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
     * @param  \App\Models\BlogCategory  $blogCategory
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id=null){
        return $request->id ? BlogCategory::find($request->id): BlogCategory::all();
    }

    public function all(Request $request){
        return BlogCategory::orderBy('id','desc')->paginate(5);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BlogCategory  $blogCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(BlogCategory $blogCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BlogCategory  $blogCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'status' => 'required|string'
        ]);
        
        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'msg'=>$validator->errors()->all(),
                'severity'=>'danger'
            ]);
        }else{
            $updateBlog = BlogCategory::where('id',$request->id)->update([
                'name'=>$request->name,
                'status'=>$request->status
            ]);
            if($updateBlog){
                return response()->json([
                    'success'=>true,
                    'msg'=>['Blog Category updated successfully!'],
                    'severity'=>'success'
                ]);
            }else{
                return response()->json([
                    'success'=>false,
                    'msg'=>['Unable to update blog category!'],
                    'severity'=>'danger'
                ]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BlogCategory  $blogCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $blog = Blog::where('cats_ids','like','%'.$request->id.'%')->get();
        if(count($blog)>0){
            return response()->json([
                'success'=>false,
                'msg'=>'This category used in blogs!',
                'severity'=>'danger'
            ]);
        }else{
            $blogcat = BlogCategory::where('id',$request->id)->delete();
            if($blogcat){
                return response()->json([
                    'success'=>true,
                    'msg'=>'category deleted successfully!',
                    'severity'=>'success'
                ]);
            }else{
                return response()->json([
                    'success'=>false,
                    'msg'=>'unable to deleted!',
                    'severity'=>'danger'
                ]);
            }
        }
    }
}
