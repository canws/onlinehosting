<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Blog;
use App\Models\Media;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\VendorAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MediaController extends Controller
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
        // return $request->all();
        $validator = Validator::make($request->all(), [
            'images' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'msg'=>$validator->errors()->all(),
                'severity'=>'danger'
            ]);
        }else{
            if($request->hasfile('images')){
                foreach($request->file('images') as $image){
                    // $img_name = time()."-.".$image->extension();
                    $img_name = time()."_".$image->getClientOriginalName();
                    $image->move(public_path('media'), $img_name);
                    Media::create([
                        'user_id'=>$request->user_id,
                        'name'=>$img_name
                    ]); 
                }
                return response()->json([
                    'success'=>true,
                    'msg'=>['images uploades successfully'],
                    'severity'=>'success'
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
     * @param  \App\Models\Media  $media
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return $request->id ? Media::where('user_id',$request->id)->orderBy('id','desc')->get() : Media::orderBy('id','desc')->get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Media  $media
     * @return \Illuminate\Http\Response
     */
    public function edit(Media $media)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Media  $media
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Media $media)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Media  $media
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request){
        $attribute = Attribute::where('image_id','like','%'.$request->id.'%')->get();
        $product = Product::where(['image_id'=>$request->id])->get();
        $cart = Cart::where('image_id','like','%'.$request->id.'%')->get();
        $blog = Blog::where('image',$request->id)->get();
        $category = Category::where('image',$request->id)->get();
        $vendorAttr = VendorAttribute::where('image_id',$request->id)->get();
        if(count($product)>0){
            return response()->json([
                'success'=>true,
                'msg'=>["This image already used in products!"],
                'severity'=>'danger'
            ]);
        }

        if(count($attribute)>0){
            return response()->json([
                'success'=>true,
                'msg'=>["This image already used in products!"],
                'severity'=>'danger'
            ]);
        }elseif(count($cart)>0){
            return response()->json([
                'success'=>true,
                'msg'=>["This image already used in products carts!"],
                'severity'=>'danger'
            ]);
        }elseif(count($blog)>0){
            return response()->json([
                'success'=>true,
                'msg'=>["This image already used in blog!"],
                'severity'=>'danger'
            ]);
        }elseif(count($category)>0){
            return response()->json([
                'success'=>true,
                'msg'=>["This image already used in category!"],
                'severity'=>'danger'
            ]);
        }elseif(count($vendorAttr)>0){
            return response()->json([
                'success'=>true,
                'msg'=>["This image already used in vendor profile!"],
                'severity'=>'danger'
            ]);
        }else{
            $find = Media::where([
                'id'=>$request->id,
                'user_id'=>$request->user_id
            ])->first();
            if($find){
                unlink('./media/'.$find->name);
                Media::where([
                    'id'=>$request->id,
                    'user_id'=>$request->user_id
                ])->delete();
                return response()->json([
                    'success'=>true,
                    'msg'=>[$find->name. " removed successfully!"],
                    'severity'=>'success'
                ]);
            }else{
                return response()->json([
                    'success'=>false,
                    'msg'=>["Unable removed image!"],
                    'severity'=>'danger'
                ]); 
            }
        }
        
    }

    public function productMedia(Request $request){
        // return $request->all();
        return Media::find($request->id);
    }

    public function singleProductImg(Request $request){
        return Media::whereIn('id',$request->id)->get();
    }
}
