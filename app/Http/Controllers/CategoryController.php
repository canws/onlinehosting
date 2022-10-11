<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\Product;
use App\Models\Activity;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
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

    public function addProducttype(Request $request){
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|unique:categories',
            'status' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'msg'=>$validator->errors()->all(),
                'severity'=>'danger',
                'summary'=>'Error Message'
            ]);
        }else{
            $category = new Category;  
                $category->category_name = $request->category_name;
                $category->product_type = 0;
                $category->status = $request->status;
            $category->save();
            Activity::create([
                'user_id'=>$request->user_id,
                'activity'=>'added',
                'message'=>$category->category_name.' product type',
            ]);
            return response()->json([
                'success'=>true,
                'msg'=>['Product type added successfully'],
                'severity'=>'success',
                'summary'=>'Success Message'
            ]);
        }
    }


    public function create(Request $request){
        // return $request->all();
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|string|unique:categories',
            'product_type' => 'required',
            'image' => 'required',
            'description' => 'required|string',
            'status' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'msg'=>$validator->errors()->all(),
                'severity'=>'danger',
                'summary'=>'Error Message'
            ]);
        }else{
            $category = new Category;  
                $category->category_name = $request->category_name;
                $category->parent_category = $request->parent_category;
                $category->image = $request->image;
                $category->description = $request->description;
                $category->status = $request->status;
            $category->save();
            Activity::create([
                'user_id'=>$request->user_id,
                'activity'=>'added',
                'message'=>$category->category_name.' category',
            ]);
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
    public function store()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request){
        return $request->id ? Category::find($request->id) : Media::join('categories','categories.image','=','media.id')->whereNull('product_type')->orderBy('categories.id','desc')->paginate(5);
    }

    public function all(Request $request){
        return $request->id ? Category::find($request->id) : Media::join('categories','categories.image','=','media.id')->whereNull('product_type')->get();
    }

    public function allproductTypelist(Request $request){
        return Category::where('product_type','0')->get();
    }

    public function productTypelist(Request $request){
        return $request->id ? Category::find($request->id): Category::where('product_type','0')->orderBy('id','desc')->paginate(4);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|string',
            'product_type' => 'required|string',
            'image' => 'required',
            'description' => 'required|string',
            'status' => 'required|string'
        ]);

        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'msg'=>$validator->errors()->all(),
                'severity'=>'danger'
            ]);
        }else{
            $update = Category::where('id',$request->id)->update([
                'category_name'=>$request->category_name,
                'parent_category'=>$request->parent_category,
                'image'=>$request->image,
                'description'=>$request->description,
                'status'=>$request->status
            ]);
            if($update){
                Activity::create([
                    'user_id'=>$request->user_id,
                    'activity'=>'updated',
                    'message'=>$request->category_name.' category',
                ]);
                return response()->json([
                    'success'=>true,
                    'msg'=>['Catetory added successfully'],
                    'severity'=>'success'
                ]);
            }else{
                return response()->json([
                    'success'=>true,
                    'msg'=>['Catetory unable to update!'],
                    'severity'=>'danger'
                ]);
            }
        }
    }

    public function updateProductType(Request $request){
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
            $update = Category::where('id',$request->id)->update([
                'category_name'=>$request->name,
                'status'=>$request->status
            ]);
            if($update){
                Activity::create([
                    'user_id'=>$request->user_id,
                    'activity'=>'updated',
                    'message'=>$request->category_name.' product type',
                ]);
                return response()->json([
                    'success'=>true,
                    'msg'=>['category updated successfully!'],
                    'severity'=>'success',
                ]);
            }else{
                return response()->json([
                    'success'=>false,
                    'msg'=>['Unable to update category!'],
                    'severity'=>'danger',
                ]);
            }
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request){
        // return $request->all();
        $oldCategory = Category::find($request->id);
        $productCat = Product::where('product_type',$request->id)
        ->orWhere('category_id',$request->id)
        ->orWhere('subcategory_id',$request->id)->get();
        if(count($productCat)>0){
            Activity::create([
                'user_id'=>$request->user_id,
                'activity'=>'try to delete',
                'message'=>$oldCategory->category_name.' category',
            ]);
            return response()->json([
                'success'=>false,
                'msg'=>'Delte first product , related from this category!',
                'severity'=>'danger',
            ]);
        }else{
            $category = Category::where('parent_category',$request->id)->get();
            if(count($category)>0){
                return response()->json([
                    'success'=>false,
                    'msg'=>'Delte first child category!',
                    'severity'=>'danger',
                ]);
            }else{
                $delete = Category::where('id',$request->id)->delete();
                if($delete){
                    Activity::create([
                        'user_id'=>$request->user_id,
                        'activity'=>'deleted',
                        'message'=>$oldCategory->category_name.' category',
                    ]);
                    return response()->json([
                        'success'=>true,
                        'msg'=>'category delted successfully!',
                        'severity'=>'success',
                    ]);
                }else{
                    return response()->json([
                        'success'=>false,
                        'msg'=>'Unable to delete category!',
                        'severity'=>'danger',
                    ]);
                }
                
            }
        }
    }

    public function categoryDropown(Request $request){
        return Category::where('parent_category',$request->parent_category)->get();
    }
    
    public function shopCats(Request $request){
        return Category::where('parent_category','<>','')->get();
    }
}
