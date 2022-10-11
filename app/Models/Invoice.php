<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'invoice_number',
        'assigned_email',
        'invoice_date',
        'due_date',
        'invoice_to',
        'country',
        'iban',
        'swiftcode',
        'paymentvia',
        'sales_person',
        'sub_total',
        'total_discount',
        'tax',
        'total',
        'images',
    ]; 

    public function invoice_items(){
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }
}
