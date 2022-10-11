<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
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
            'name' => 'required',
            'phone' => 'required',
            'houseno' => 'required',
            'landmark' => 'required',
            'city' => 'required',
            'pincode' => 'required',
            'state' => 'required',
            'address_type' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'msg'=>$validator->errors()->all(),
                'severity'=>'danger',
                'summary'=>'Error Message'
            ]);
        }else{
            $address = Address::create([
                'user_id'=>$request->id,
                'name'=>$request->name,
                'phone'=>$request->phone,
                'houseno'=>$request->houseno,
                'landmark'=>$request->landmark,
                'city'=>$request->city,
                'pincode'=>$request->pincode,
                'state'=>$request->state,
                'address_type'=>$request->address_type,
            ]);
            if($address){
                $order = Order::latest()->first();
                $orderUpdate = Order::where('id',$order->id)->update([
                    'address_id'=>$address->id
                ]);
                if($orderUpdate){
                    return response()->json([
                        'success'=>true,
                        'msg'=>$order,
                        'severity'=>'success'
                    ]);
                }
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
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return $request->id ? Address::find($request->id) :  Address::where('user_id',$request->user_id)->get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function edit(Address $address)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Address $address)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function destroy(Address $address)
    {
        //
    }
}
