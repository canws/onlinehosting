<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'transaction_id',
        'payment_status',
        'payment_method',
        'order_status',
        'order_date',
        'total_amount',
        'status',
    ];

    public function order(){
        return $this->belongsTo(Order::class,'id');
    }
}
