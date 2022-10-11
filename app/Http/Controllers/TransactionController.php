<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
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
        if($request->payment_status=="COMPLETED"){
            $order = Order::where('id',$request->orderid)->first();
            if($order->payment_status=="COMPLETED"){
                $transaction = Transaction::create([
                    'order_id'=>$order->id,
                    'transaction_id'=>$request->transaction_id,
                    'payment_status'=>$request->payment_status,
                    'payment_method'=>$request->payment_method,
                    'order_status'=>$order->order_status,
                    'order_date'=>date('Y-m-d', strtotime($order->created_at)),
                    'total_amount'=>$order->total_amount,
                    'status'=>$request->payment_status,
                ]);
                if($transaction){
                    return response()->json([
                        'success'=>true,
                        'msg'=>'Transaction successfull!',
                        'severity'=>'success'
                    ]);
                }else{
                    return response()->json([
                        'success'=>false,
                        'msg'=>'Transaction pending some reason!',
                        'severity'=>'danger'
                    ]);
                }
            }   
        }elseif($request->payment_status=="PENDING"){
            $order = Order::where('id',$request->orderid)->first();
            if($order->payment_status=="PENDING"){
                $transaction = Transaction::create([
                    'order_id'=>$order->id,
                    'transaction_id'=>$request->transaction_id,
                    'payment_status'=>$request->payment_status,
                    'payment_method'=>$request->payment_method,
                    'order_status'=>$order->order_status,
                    'order_date'=>date('Y-m-d', strtotime($order->created_at)),
                    'total_amount'=>$order->total_amount,
                    'status'=>$request->payment_status,
                ]);
                if($transaction){
                    return response()->json([
                        'success'=>false,
                        'msg'=>'Transaction pending some reason!',
                        'severity'=>'danger'
                    ]);
                }else{
                    return response()->json([
                        'success'=>false,
                        'msg'=>'Transaction pending some reason!',
                        'severity'=>'danger'
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
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return $request->id? Order::where('user_id',$request->id)->join('transactions','orders.id','=','transactions.order_id')->paginate(5) : Order::join('transactions','orders.id','=','transactions.order_id')->paginate(5);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
