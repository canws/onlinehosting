<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
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
        $oldwishlist = Wishlist::where('user_id',$request->user_id)
        ->where('product_id',$request->product_id)
        ->first();
        if($oldwishlist){
            Wishlist::where('id',$oldwishlist->id)->delete();
            return response()->json([
                'success'=>false,
                'msg'=>'Product removed from wishlist!',
                'severity'=>'danger'
            ]);
        }else{
            $wishlist = new Wishlist();
                $wishlist->user_id = $request->user_id;
                $wishlist->product_id = $request->product_id;
            $wishlist->save();
            return response()->json([
                'success'=>true,
                'msg'=>'Product added in to wishlist!',
                'severity'=>'success'
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
     * @param  \App\Models\Wishlist  $wishlist
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // return Wishlist::where('user_id',$request->user_id)->get();
        return Wishlist::join('products','products.id','=','wishlists.product_id')->get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Wishlist  $wishlist
     * @return \Illuminate\Http\Response
     */
    public function edit(Wishlist $wishlist)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Wishlist  $wishlist
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Wishlist $wishlist)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Wishlist  $wishlist
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $delete = Wishlist::where('user_id',$request->user_id)
        ->where('product_id',$request->product_id)
        ->delete();

        if($delete){
            return response()->json([
                'success'=>true,
                'msg'=>'Product removed wishlist!'
            ]);
        }else{
            return response()->json([
                'success'=>false,
                'msg'=>'Unable to removed product from wishlist!'
            ]);
        }
    }

    public function checkWishlistItem(Request $request){
        return Wishlist::where('user_id',$request->user_id)->get();
    }
}
