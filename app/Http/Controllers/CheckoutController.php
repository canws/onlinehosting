<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
class CheckoutController extends Controller
{
    public function getCeckoutItems(Request $request){
        return Product::find($request->id);
        // return $request->id; 
    }
}
