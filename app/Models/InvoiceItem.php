<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'name',
        'size',
        'cost',
        'quantity',
        'message',
        'discount',
        'discount_cost',
        'discount_type',
        'price',
        'discount_price',
    ]; 

    public function invoce(){
        return $this->belongsTo(Invoice::class, 'id');
    }
}
