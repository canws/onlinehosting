<?php

namespace App\Http\Controllers;
use App\Models\Tag;
use App\Models\Media;
use App\Models\Product;
use App\Models\Activity;
use App\Models\Attribute;
use Illuminate\Http\Request;
use App\Models\FeaturedProduct;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        // return explode(",",$request->tags);
        // return $request->all();
        // $all=$request->all();
        $attr=json_decode($request->attributess);
        
        $attrlen=json_decode($request->attrlength);
        $orifinalAttr = [];
        $notNullAttr = [];
        for($i=0; $i<$attrlen; $i++){
            $orifinalAttr[]=array_pop($attr);
        }
        // return $orifinalAttr;
        foreach($orifinalAttr as $attrs){
            $notNullAttr[]= (object) array_filter((array) $attrs);
        }
        // return $notNullAttr;
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|unique:products',
            'slug' => 'required',
            'product_type' => 'required',
            'parent_category' => 'required',
            'sub_category' => 'required',
            'tags' => 'required',
            'unit' => 'required',
            'discount' => 'required',
            'discount_type' => 'required',
            'video_url' => 'required',
            'status' => 'required',
            'description' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'msg'=>$validator->errors()->all(),
                'severity'=>'danger',
                'summary'=>'Error Message'
            ]);
        }else{
            // return $request->all();
            $request->tags = Tag::tagExists(explode(",",$request->tags));
            $product = new Product; 
                $product->userid =  $request->user_id;
                $product->product_name = $request->product_name;
                $product->image_id = $request->image_id;
                $product->slug = $request->slug;
                $product->product_type = $request->product_type;
                $product->category_id = $request->parent_category;
                $product->subcategory_id = $request->sub_category;
                $product->tags_id = $request->tags;
                $product->unit_id = $request->unit;
                $product->discount = $request->discount;
                $product->discount_type = $request->discount_type;
                $product->video = $request->video_url;
                $product->status = $request->status;
                $product->description = $request->description;
            $product->save();
            
            foreach($notNullAttr as $atr){
                $Attributes = new Attribute();
                $Attributes->products_id = $product->id;
                $Attributes->size = $atr->size;
                $Attributes->color = $atr->color;
                $Attributes->quantity = $atr->quantity;
                $Attributes->price = $atr->price;
                $Attributes->image_id = implode(" ",$atr->imageID);

                $Attributes->save();
            }
            $att = Attribute::where(['products_id'=>$product->id])->orderBy('price', 'desc')->first();
            Product::where(['id'=>$product->id])->update([
                'price'=>$att->price,
                'image_id'=>explode(" ",$att->image_id)[0],
            ]);
            Activity::create([
                'user_id'=>$request->user_id,
                'activity'=>'added',
                'message'=>'a product',
            ]);
            if($request->featured==true){
                $featured_from = substr($request->featured_from,4,11);
                $featured_to = substr($request->featured_to,4,11);
                $featured = new FeaturedProduct;
                $featured->product_id = $product->id;
                $featured->featured_from = date('Y-m-d', strtotime($featured_from));
                $featured->featured_to = date('Y-m-d', strtotime($featured_to));
                $featured->save();
                return response()->json([
                    'success'=>true,
                    'msg'=>['Product has been inserted with featured!'],
                    'severity'=>'success',
                    'summary'=>'Success Message'
                ]);
            }else{
                return response()->json([
                    'success'=>true,
                    'msg'=>['Product has been inserted!'],
                    'severity'=>'success',
                    'summary'=>'Success Message'
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request){
        return $request->id ? Product::find($request->id) : Media::join('products','products.image_id','=','media.id')->paginate(3);
    }

    public function list(Request $request){
        return $request->id ? Media::join('products','products.image_id','=','media.id')->where('products.userid',$request->id)->paginate(3) : Media::join('products','products.image_id','=','media.id')->orderBy('products.id','desc')->paginate(5);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request){
        // return $request->all();
        return $request->id ? Product::find($request->id) : Product::all();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request){
        // return $request->all();
        $all=$request->all();
        $attr=json_decode($request->attributess);
        // return $attr;
        $attrlen=json_decode($request->attrlength);
        $orifinalAttr = [];
        $notNullAttr = [];
        for($i=0; $i<$attrlen; $i++){
            $orifinalAttr[]=array_pop($attr);
        }
        foreach($orifinalAttr as $attrs){
            $notNullAttr[]= (object) array_filter((array) $attrs);
        }
        $validator = Validator::make($request->all(), [
            'product_name' => 'required',
            'slug' => 'required',
            'product_type' => 'required',
            'parent_category' => 'required',
            'sub_category' => 'required',
            'tags' => 'required',
            'unit' => 'required',
            'discount' => 'required',
            'discount_type' => 'required',
            'video_url' => 'required',
            'status' => 'required',
            'description' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'msg'=>$validator->errors()->all(),
                'severity'=>'danger'
            ]);
        }else{
            $featuredRow = FeaturedProduct::where('product_id',$request->id)->first();
            $product = Product::where('id',$request->id)->update([
                'product_name'=>$request->product_name,
                'image_id'=>$request->image_id,
                'slug'=>$request->slug,
                'product_type'=>$request->product_type,
                'category_id'=>$request->parent_category,
                'subcategory_id'=>$request->sub_category,
                'tags_id'=>$request->tags,
                'unit_id'=>$request->unit,
                'discount'=>$request->discount,
                'discount_type'=>$request->discount_type,
                'video'=>$request->video_url,
                'status'=>$request->status,
                'description'=>$request->description
            ]);
            
            foreach($notNullAttr as $attr){
                if(isset($attr->id)){
                    Attribute::where('id',$attr->id)->update([
                        'size'=>$attr->size,
                        'color'=>$attr->color,
                        'quantity'=>$attr->quantity,
                        'price'=>$attr->price,
                        'image_id'=>implode(" ",$attr->imageID),
                    ]);
                }else{
                    $Attributes = new Attribute();
                    $Attributes->products_id = $request->id;
                    $Attributes->size = $attr->size;
                    $Attributes->color = $attr->color;
                    $Attributes->quantity = $attr->quantity;
                    $Attributes->price = $attr->price;
                    $Attributes->image_id = implode(" ",$attr->imageID);
                    $Attributes->save();
                }
            }
            $att = Attribute::where(['products_id'=>$request->id])->orderBy('price', 'desc')->first();
            Product::where(['id'=>$request->id])->update([
                'price'=>$att->price,
            ]);
            if($request->featured==true){
                if($featuredRow){
                    $featured_from = substr($request->featured_from,4,11);
                    $featured_to = substr($request->featured_to,4,11);
                    FeaturedProduct::where('product_id',$request->id)->update([
                        'product_id'=>$request->id,
                        'featured_from'=>date('Y-m-d', strtotime($featured_from)),
                        'featured_to'=>date('Y-m-d', strtotime($featured_to)),
                    ]);
                }else{
                    $featured_from = substr($request->featured_from,4,11);
                    $featured_to = substr($request->featured_to,4,11);
                    $featured = new FeaturedProduct;
                    $featured->product_id = $request->id;
                    $featured->featured_from = date('Y-m-d', strtotime($featured_from));
                    $featured->featured_to = date('Y-m-d', strtotime($featured_to));
                    $featured->save();
                }
                
                return response()->json([
                    'success'=>true,
                    'msg'=>['Product has been updated with featured!'],
                    'severity'=>'success',
                    'summary'=>'Success Message'
                ]);
            }else{
                return response()->json([
                    'success'=>true,
                    'msg'=>['Product has been updated!'],
                    'severity'=>'success',
                    'summary'=>'Success Message'
                ]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request){
        $delete = Product::where('id',$request->id)->first();
        if($delete){
            return response()->json([
                'success'=>true,
                'msg'=>'Product has been removed!',
                'severity'=>'success'
            ]);
        }else{
            return response()->json([
                'success'=>false,
                'msg'=>'Unable to remove product!',
                'severity'=>'danger'
            ]);
        }
    }

    public function updateRating(Request $request){
        $update = Product::where('id',$request->id)->update(['rating'=>$request->rating]);
    }

    public function featuredProduct(Request $request){
        // return $request->all();
        return Media::join('products','products.image_id','=','media.id')->whereIn('products.id',$request->featuredid)->paginate(5);
    }

    public function categoryProduct(Request $request){ 
        return Media::join('products','products.image_id','=','media.id')->where('product_type', $request->id)
        ->orWhere('category_id', $request->id)
        ->orWhere('subcategory_id', $request->id)->paginate(3);
    }

    public function searchProduct(Request $request){
        return Media::join('products','products.image_id','=','media.id')->where('product_name', $request->search)
        ->orWhere('slug', $request->search)->paginate(3);
    }

    public function dropdownProduct(Request $request){
        return Media::join('products','products.image_id','=','media.id')
        ->orderBy('products.id', $request->order)->paginate(3);
    }

    public function productWithAttribute(Request $request){
        return Product::join('attributes','products.id','=','attributes.products_id')->first();
    }

    public function countProduct(Request $request){
        return Product::count();
    }
}
