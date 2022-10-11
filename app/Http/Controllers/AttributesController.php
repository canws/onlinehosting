<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttributesController extends Controller
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Attribute  $attributes
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return Attribute::where(['products_id'=>$request->id])->get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Attributes  $attributes
     * @return \Illuminate\Http\Response
     */
    public function edit(Attribute $attributes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Attribute  $attributes
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attribute $attributes)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Attribute  $attributes
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $delete = Attribute::where(['id'=>$request->id])->delete();

        if($delete){
            return response()->json([
                'success'=>true,
                'msg'=>['Attribute has been removed from this product!'],
                'severity'=>'success',
                'summary'=>'Success Message'
            ]);
        }else{
            return response()->json([
                'success'=>false,
                'msg'=>['Unable to delete attribute from this product!'],
                'severity'=>'danger',
            ]);
        }
    }

    public function sizeAttribute(Request $request){
        return Attribute::where([
            'products_id'=>$request->products_id,
            'size'=>$request->size
        ])->get();
    }

    public function maxPriceAttribute(Request $request){
        return Media::join('attributes','attributes.image_id','=','media.id')->where(['products_id'=>$request->id])->orderBy('price', 'desc')->first();
    }

    public function editProductAttribute(Request $request){
        // Attribute::where(['products_id'=>$request->id])->get()

        return Media::join('attributes','attributes.image_id','=','media.id')->where(['products_id'=>$request->id])->get();
    }
}
