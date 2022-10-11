<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
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
        $order = Order::create([
            'user_id'=>$request->user_id,
            'total_amount'=>$request->total_amount,
            'payment_status'=>null,
            'order_status'=>'pending',
            'status'=>'pending',
        ]);
        
        if($order){
            foreach($request->items as $item){
                OrderItem::create([
                    'order_id'=>$order->id,
                    'product_id'=>$item['product_id'],
                    'product_name'=>Product::where('id',$item['product_id'])->first()->product_name,
                    'user_id'=>$item['user_id'],
                    'size'=>$item['size'],
                    'color'=>$item['color'],
                    'quantity'=>$item['quantity'],
                    'price'=>$item['price'],
                    'discount'=>$item['discount'],
                    'discount_type'=>$item['discount_type']
                ]);
            }
            return response()->json([
                'success'=>true,
                'msg'=>'order in process'
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
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $user = User::find($request->id);
        if($user->role=='user'||$user->role=='vendor'){
            $order = Order::join('order_items','order_id','=','orders.id')
            // ->join('products','order_items.product_id','=','products.id')
            ->where('orders.user_id',$request->id)->orderBy('orders.created_at','desc')->paginate(5);
            return response()->json([
                'success'=>true,
                'result'=>$order
            ]);
        }else if($user->role=='admin'){
            $order = Order::join('order_items','order_id','=','orders.id')->orderBy('orders.created_at','desc')->paginate(5);
            return response()->json([
                'success'=>true,
                'result'=>$order
            ]);
        }else{
            return response()->json([
                'success'=>false,
                'result'=>['You are not able to see orders']
            ]);
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }

    public function latestOrder(Request $request){
        if(!is_null($request->address_id)){
            $order = Order::latest()->first();
            $orderUpdate = Order::where(['id'=>$order->id])
            ->update([
                'address_id'=>$request->address_id
            ]);
            if($orderUpdate){
                return response()->json([
                    'success'=>true,
                    'item'=>Order::latest()->first(),
                    'id'=>$request->address_id
                ]);
            }
        }else{
            return response()->json([
                'success'=>true,
                'item'=>Order::latest()->first()
            ]);
        }
          
    }

    public function orderComplete(Request $request){
        $status="";
        $order_status="";
        if($request->payment_status=='PENDING'){
            $status = "draft";
            $order_status = "pending";
        }elseif($request->payment_status=='COMPLETED'){
            $status = "processing";
            $order_status = "processing";
        }
        
        $orderUpdate = Order::where(['id'=>$request->order_id])->update([
            'payment_status'=>$request->payment_status,
            'payment_method'=>$request->payment_method,
            'order_status'=>$order_status,
            'status'=>$status
        ]);
        if($orderUpdate){
            return response()->json([
                'success'=>true
            ]);
        } 
    }

    
}
