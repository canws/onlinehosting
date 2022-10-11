<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Product;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:tags',
        ]);

        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'msg'=>$validator->errors()->all(),
                'severity'=>'danger',
                'summary'=>'Error Message'
            ]);
        }else{
            $tag = new Tag;  
                $tag->name = $request->name;
                $tag->status = $request->status;
            $tag->save();
            if($tag){
                Activity::create([
                    'user_id'=>$request->user_id,
                    'activity'=>'added',
                    'message'=>'a tag',
                ]);
                return response()->json([
                    'success'=>true,
                    'msg'=>['Tag added successfully'],
                    'severity'=>'success',
                    'summary'=>'Success Message'
                ]);
            }
            
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return $request->id ? Tag::find($request->id): Tag::orderBy('id','desc')->paginate(5);
    }

    public function all(Request $request)
    {
        return Tag::all();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function edit(Tag $tag)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'msg'=>$validator->errors()->all(),
                'severity'=>'danger'
            ]);
        }else{
            $update = Tag::where('id',$request->id)->update([
                'name'=>$request->name
            ]);
            if($update){
                Activity::create([
                    'user_id'=>$request->user_id,
                    'activity'=>'updated',
                    'message'=>$request->name. ' tag',
                ]);
                return response()->json([
                    'success'=>true,
                    'msg'=>['Tag update successfully!'],
                    'severity'=>'success'
                ]);
            }else{
                return response()->json([
                    'success'=>false,
                    'msg'=>['Unable to udpate tag!'],
                    'severity'=>'success'
                ]);
            }
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $tagproduct = Product::where('tags_id','like','%'.$request->id.'%')->first();
        if($tagproduct){
            $tt = Tag::find($request->id);
            Activity::create([
                'user_id'=>$request->user_id,
                'activity'=>'try to delete',
                'message'=>$tt->name. ' tag',
            ]);
            return response()->json([
                'success'=>false,
                'msg'=>'Somewhere used this tag !',
                'severity'=>'danger'
            ]);
        }else{
            $tt = Tag::find($request->id);
            $tag = Tag::where('id',$request->id)->delete();
            if($tag){
                Activity::create([
                    'user_id'=>$request->user_id,
                    'activity'=>'updated',
                    'message'=>$tt->name. ' tag',
                ]);
                return response()->json([
                    'success'=>true,
                    'msg'=>'Tag deleted Successfully!',
                    'severity'=>'success'
                ]);
            }else{
                return response()->json([
                    'success'=>false,
                    'msg'=>'unable to delete!',
                    'severity'=>'danger'
                ]);
            }
            
        }
    }

    public function getTags(Request $request){
        return $request->id ? Tag::find(explode(",", $request->id)): Tag::all();
    }
}
