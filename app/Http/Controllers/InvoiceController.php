<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
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
        $validator = Validator::make($request->all(),[
            'date'=>'required',
            'due_date'=>'required',
            'invoice_to'=>'required',
            'country'=>'required',
            'iban'=>'required',
            'swiftcode'=>'required',
            'paymentvia'=>'required',
            'sales_person'=>'required',
            'sales_person'=>'required',
            'images'=>'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'msg'=>$validator->errors()->all(),
                'severity'=>'danger'
            ]);
        }else{
            // return $request->all();
            // return json_decode($request->invoice_items);
            $date = substr($request->date,4,11);
            $due_date = substr($request->due_date,4,11);
            if($request->hasfile('images')){
                $imgs = [];
                foreach($request->file('images') as $image){
                    $img_name = time()."_".$image->getClientOriginalName();
                    $image->move(public_path('invoice'), $img_name);
                    array_push($imgs,$img_name);
                }
                // return implode(" ",$imgs);
                $invoice = Invoice::create([
                    'invoice_date'=>date('Y-m-d', strtotime($date)),
                    'due_date'=>date('Y-m-d', strtotime($due_date)),
                    'invoice_to'=>$request->invoice_to,
                    'country'=>$request->country,
                    'iban'=>$request->iban,
                    'swiftcode'=>$request->swiftcode,
                    'paymentvia'=>$request->paymentvia,
                    'sales_person'=>$request->sales_person,
                    'sub_total'=>$request->sub_total,
                    'total_discount'=>$request->total_discount,
                    'tax'=>$request->tax,
                    'total'=>$request->total,
                    'images'=>implode(" ",$imgs)
                ]);
                $invoiceUpdate = Invoice::where('id',$invoice->id)->update([
                    'invoice_number'=>'#'.$invoice->id
                ]);
                $invoiceItems = json_decode($request->invoice_items);
                foreach($invoiceItems as $item){
                    $InvoiceItem = InvoiceItem::create([
                        'invoice_id'=>$invoice->id,
                        'size'=>$item->size,
                        'name'=>$item->name,
                        'cost'=>$item->cost,
                        'quantity'=>$item->quantity,
                        'message'=>$item->message,
                        'discount'=>$item->discount,
                        'discount_type'=>$item->discount_type,
                        'discount_cost'=>$item->discount_cost,
                        'price'=>$item->price,
                        'discount_price'=>$item->discount_price,
                    ]);
                }

                if($invoice){
                    return response()->json([
                        'success'=>true,
                        'msg'=>['Invoice added successfully!'],
                        'severity'=>'success'
                    ]);
                }else{
                    return response()->json([
                        'success'=>false,
                        'msg'=>['Invoice unable to insert!'],
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return $request->id ? Invoice::with('invoice_items')->where('id',$request->id)->first() : Invoice::paginate(5);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        $validator = Validator::make($request->all(),[
            'date'=>'required',
            'due_date'=>'required',
            'invoice_to'=>'required',
            'country'=>'required',
            'iban'=>'required',
            'swiftcode'=>'required',
            'paymentvia'=>'required',
            'sales_person'=>'required',
            'sales_person'=>'required',
            // 'images'=>'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'msg'=>$validator->errors()->all(),
                'severity'=>'danger'
            ]);
        }else{
            
            $date = substr($request->date,4,11);
            $due_date = substr($request->due_date,4,11);
            $imgs = [];
            if($request->hasfile('images')){
                foreach($request->file('images') as $image){
                    $img_name = time()."_".$image->getClientOriginalName();
                    $image->move(public_path('invoice'), $img_name);
                    array_push($imgs,$img_name);
                }
                
            }
            $invoice = Invoice::find($request->id);
            $invoice->images=array_unique(array_merge(explode(' ',$invoice->images),$imgs));

            $invoice->invoice_date = date('Y-m-d', strtotime($date));
            $invoice->due_date = date('Y-m-d', strtotime($due_date));
            $invoice->invoice_to = $request->invoice_to;
            $invoice->country = $request->country;
            $invoice->iban = $request->iban;
            $invoice->swiftcode = $request->swiftcode;
            $invoice->paymentvia = $request->paymentvia;
            $invoice->sales_person = $request->sales_person;
            $invoice->sub_total = $request->sub_total;
            $invoice->total_discount = $request->total_discount;
            $invoice->tax = $request->tax;
            $invoice->total = $request->total;
            $invoice->images = implode(' ',$invoice->images);
            $invoice->update();
            $invoices = InvoiceItem::where('invoice_id',$request->id)->get();
            $invoiceItems = json_decode($request->invoice_items);
            foreach($invoiceItems as $item){
                if(isset($item->id)){
                    $InvoiceItem = InvoiceItem::where('id',$item->id)->update([
                        'size'=>$item->size,
                        'name'=>$item->name,
                        'cost'=>$item->cost,
                        'quantity'=>$item->quantity,
                        'message'=>$item->message,
                        'discount'=>$item->discount,
                        'discount_type'=>$item->discount_type,
                        'discount_cost'=>$item->discount_cost,
                        'price'=>$item->price,
                        'discount_price'=>$item->discount_price,
                    ]);
                }else{
                    $InvoiceItem = InvoiceItem::create([
                        'invoice_id'=>$request->id,
                        'size'=>$item->size,
                        'name'=>$item->name,
                        'cost'=>$item->cost,
                        'quantity'=>$item->quantity,
                        'message'=>$item->message,
                        'discount'=>$item->discount,
                        'discount_type'=>$item->discount_type,
                        'discount_cost'=>$item->discount_cost,
                        'price'=>$item->price,
                        'discount_price'=>$item->discount_price,
                    ]);
                }
                
            }

            if($invoice){
                return response()->json([
                    'success'=>true,
                    'msg'=>['Invoice updated successfully!'],
                    'severity'=>'success'
                ]);
            }else{
                return response()->json([
                    'success'=>false,
                    'msg'=>['Invoice unable to update!'],
                    'severity'=>'danger'
                ]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        //
    }

    public function searchProduct(Request $request){
        return Product::where('product_name', 'like','%'.$request->search.'%')
        ->orWhere('slug', 'like','%'.$request->search.'%')->get();
    }

    public function invoiceNumber(){
        return Invoice::orderBy('created_at','desc')->first();
    }
}
