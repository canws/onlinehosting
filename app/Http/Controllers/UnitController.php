<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Product;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
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
            'name' => 'required|string|unique:units',
        ]);

        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'msg'=>$validator->errors()->all(),
                'severity'=>'danger',
                'summary'=>'Error Message'
            ]);
        }else{
            $unit = new Unit;  
                $unit->name = $request->name;
                $unit->status = $request->status;
            $unit->save();
            if($unit){
                Activity::create([
                    'user_id'=>$request->user_id,
                    'activity'=>'added',
                    'message'=>$request->name. ' unit',
                ]);
                return response()->json([
                    'success'=>true,
                    'msg'=>['Unit added successfully'],
                    'severity'=>'success',
                    'summary'=>'Success Message'
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id=null)
    {
        return $request->id ? Unit::find($request->id): Unit::orderBy('id','desc')->paginate(10);
    }

    public function all(Request $request, $id=null)
    {
        return Unit::all();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function edit(Unit $unit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Unit $unit)
    {
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
            $update = Unit::where('id',$request->id)->update(['name'=>$request->name]);
            if($update){
                Activity::create([
                    'user_id'=>$request->user_id,
                    'activity'=>'updated',
                    'message'=>$request->name. ' unit',
                ]);
                return response()->json([
                    'success'=>true,
                    'msg'=>['Unit updated successfully'],
                    'severity'=>'success',
                ]);
            }else{
                return response()->json([
                    'success'=>false,
                    'msg'=>['Unable to update unite!'],
                    'severity'=>'danger',
                ]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $unitProduct = Product::where('unit_id',$request->id)->first();
        if($unitProduct){
            $uu = Unit::find($request->id);
            Activity::create([
                'user_id'=>$request->user_id,
                'activity'=>'try to delete',
                'message'=>$uu->name. ' unit',
            ]);
            return response()->json([
                'success'=>false,
                'msg'=>'Somewhere used this unit !',
                'severity'=>'danger'
            ]);
        }else{
            $uu = Unit::find($request->id);
            $unit = Unit::where('id',$request->id)->delete();
            if($unit){
                Activity::create([
                    'user_id'=>$request->user_id,
                    'activity'=>'deleted',
                    'message'=>$uu->name. ' unit',
                ]);
                return response()->json([
                    'success'=>true,
                    'msg'=>'Unit deleted Successfully!',
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
