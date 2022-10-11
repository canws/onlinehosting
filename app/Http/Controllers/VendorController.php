<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Media;
use App\Models\Vendor;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\VendorAttribute;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class VendorController extends Controller
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
        $validator = Validator::make($request->all(), [
            'vendor_name' => 'required|string|min:4',
            'vendortype' => 'required|string',
            'password' => 'required|string|min:8',
            'email' => 'required|email|unique:users',
            'phone' => 'required|integer|min:12',
            'image' => 'required',
            'ratingtype' => 'required|string',
            'description' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'msg'=>$validator->errors()->all(),
                'severity'=>'danger',
                'summary'=>'Error Message'
            ]);
        }else{
            $user = new User;  
                $user->first_name = $request->vendor_name;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->role = 'vendor';
            $user->save();
            if(!is_null($user->id)){
                $vendorattr = new VendorAttribute();
                    $vendorattr->user_id = $user->id;
                    $vendorattr->vendortype = $request->vendortype;
                    $vendorattr->phone = $request->phone;
                    $vendorattr->image_id = $request->image;
                    $vendorattr->ratingtype = $request->ratingtype;
                    $vendorattr->description = $request->description;
                $vendorattr->save();
                return response()->json([
                    'success'=>true,
                    'msg'=>['Vendor added successfully'],
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
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return $request->id ? VendorAttribute::where('user_id',$request->id)->join('users','users.id','=','vendor_attributes.user_id')->first() : VendorAttribute::Join('media','media.id','=','vendor_attributes.image_id')->join('users','users.id','=','vendor_attributes.user_id')->where('role','vendor')->orderBy('users.created_at','desc')->paginate(10); 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function edit(Vendor $vendor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vendor $vendor)
    {
        $validator = Validator::make($request->all(), [
            'vendor_name' => 'required',
            'vendortype' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|integer|min:12',
            'image' => 'required',
            'ratingtype' => 'required|string',
            'description' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'msg'=>$validator->errors()->all(),
                'severity'=>'danger',
                'summary'=>'Error Message'
            ]);
        }else{
            
            if($request->password!=""){
                $user = User::where('id',$request->id)->update([
                    'first_name'=>$request->vendor_name,
                    'email'=>$request->email,
                    'password'=>Hash::make($request->password),
                    'role'=>'vendor'
                ]);
            }else{
                $user = User::where('id',$request->id)->update([
                    'first_name'=>$request->vendor_name,
                    'email'=>$request->email,
                    'role'=>'vendor'
                ]);
            }
            if($user){
                $vendorattr = VendorAttribute::where('user_id',$request->id)->update([
                    'vendortype'=>$request->vendortype,
                    'phone'=>$request->phone,
                    'image_id'=>$request->image,
                    'ratingtype'=>$request->ratingtype,
                    'description'=>$request->description
                ]);
                if($vendorattr){
                    return response()->json([
                        'success'=>true,
                        'msg'=>['Vendor updated successfully'],
                        'severity'=>'success',
                        'summary'=>'Success Message'
                    ]);
                }else{
                    return response()->json([
                        'success'=>true,
                        'msg'=>['unable to update vendor'],
                        'severity'=>'success',
                        'summary'=>'Success Message'
                    ]);
                }
                
            }
        }
    }

    public function editVendorImage(Request $request){
        return Media::find($request->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $product = Product::where('userid',$request->id)->get();
        $media = Product::where('userid',$request->id)->get();
        if(count($product)>0 || count($media)>0){
            return response()->json([
                'success'=>false,
                'msg'=>'Unable to delete vendor , this vendor has products',
                'severity'=>'danger',
                'summary'=>'Error Message'
            ]);
        }else{
            $user = User::where('id',$request->id)->delete();
            if($user){
                $vendor = VendorAttribute::where('user_id',$request->id)->delete();
                return response()->json([
                    'success'=>false,
                    'msg'=>'Vendor deleted successfully!',
                    'severity'=>'success',
                    'summary'=>'Success Message'
                ]);
            }
        }
    }

    public function vendorProducts(Request $request){
        return Product::where('userid',$request->id)->join('media','media.id','=','products.image_id')->get();
    }
}
