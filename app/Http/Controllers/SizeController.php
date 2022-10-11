<?php

namespace App\Http\Controllers;

use App\Models\Size;
use App\Models\Attribute;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SizeController extends Controller
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
            'name' => 'required|unique:sizes',
        ]);
        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'msg'=>$validator->errors()->all(),
                'severity'=>'danger',
                'summary'=>'Error Message'
            ]);
        }else{
            $size = new Size();
                $size->name = $request->name;
            $size->save();
            if($size){
                $ss = Size::find($size->id);
                Activity::create([
                    'user_id'=>$request->user_id,
                    'activity'=>'add',
                    'message'=>$ss->name. ' size',
                ]);
                return response()->json([
                    'success'=>false,
                    'msg'=>['Size added successfully!'],
                    'severity'=>'success',
                    'summary'=>'Success Message'
                ]);
            }
            
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
     * @param  \App\Models\Size  $size
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return $request->id ? Size::find($request->id) : Size::orderBy('id','desc')->paginate(3);
    }

    public function all(Request $request)
    {
        return Size::all();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Size  $size
     * @return \Illuminate\Http\Response
     */
    public function edit(Size $size)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Size  $size
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Size $size){
        // return $request->all();
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'msg'=>$validator->errors()->all(),
                'severity'=>'danger'
            ]);
        }else{
            $oldsize = Size::where('id',$request->id)->first();
            $update = Size::where('id',$request->id)->update(['name'=>$request->name]);
            if($update){
                $attr = Attribute::where('size',$oldsize->name)->update(['size'=>$request->name]);
                Activity::create([
                    'user_id'=>$request->user_id,
                    'activity'=>'update',
                    'message'=>$request->name. ' size',
                ]);
                return response()->json([
                    'success'=>true,
                    'msg'=>['Size updated successfully'],
                    'severity'=>'success',
                ]);
            }else{
                return response()->json([
                    'success'=>false,
                    'msg'=>['Unable to update!'],
                    'severity'=>'danger',
                ]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Size  $size
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $sizeOld = Size::where('id',$request->id)->first();
        $sizAttr = Attribute::where('size',$sizeOld->name)->first();
        if($sizAttr){
            Activity::create([
                'user_id'=>$request->user_id,
                'activity'=>'try to delete',
                'message'=>$sizeOld->name. ' size',
            ]);
            return response()->json([
                'success'=>false,
                'msg'=>'Somewhere used this size !',
                'severity'=>'danger'
            ]);
        }else{
            $size = Size::where('id',$request->id)->delete();
            if($size){
                Activity::create([
                    'user_id'=>$request->user_id,
                    'activity'=>'deleted',
                    'message'=>$sizeOld->name. ' size',
                ]);
                return response()->json([
                    'success'=>true,
                    'msg'=>'Size deleted Successfully!',
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
}
