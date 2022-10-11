<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
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
        // return $request->all();
        $oldcart = Cart::where('user_id',$request->user_id)
        ->where('product_id',$request->product_id)
        ->first();
        if($oldcart){
            return response()->json([
                'success'=>false,
                'msg'=>'Product already added in to cart!'
            ]);
        }else{
            $cart = new Cart();
                $cart->user_id = $request->user_id;
                $cart->product_id = $request->product_id;
                $cart->size = $request->size;
                $cart->color = $request->color;
                $cart->price = $request->price;
                $cart->discount = $request->discount;
                $cart->discount_type = $request->discount_type;
                $cart->image_id = implode(" ",$request->image_id);
            $cart->save();
            return response()->json([
                'success'=>true,
                'msg'=>'Product added successfully in to cart!'
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
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return Product::join('carts','products.id','=','carts.product_id')->where('user_id',$request->user_id)->get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function edit(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // return $request->all();
        Cart::where([
            'user_id'=>$request->user_id,
            'product_id'=>$request->product_id
        ])->update([
            'size'=>$request->size,
            'color'=>$request->color,
            'price'=>$request->price,
            'image_id'=>implode(" ",$request->image_id)
        ]);

        return response()->json([
            'success'=>true,
            'msg'=>'Cart item updated successfully !'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $delete = Cart::where('id',$request->id)->delete();
        if($delete){
            return response()->json([
                'success'=>true,
                'msg'=>'Product removed successfully from cart!'
            ]);
        }else{
            return response()->json([
                'success'=>false,
                'msg'=>'Unable to removed product from cart!'
            ]);
        }
    }

    public function destroyAll(Request $request){
        return Cart::where('id',$request->id)->delete();
    }

    public function updateCeckoutItems(Request $request){
        // return $request->all();
        Cart::where('id',$request->id)
        ->update(['quantity'=>$request->quantity]);

        return response()->json([
            'success'=>true,
            'msg'=>'Cart item updated successfully !'
        ]);
    }

    public function checkCartItem(Request $request){
        return Cart::where([
            'user_id'=>$request->user_id,
            'product_id'=>$request->product_id
            ])->first();
    }
}
