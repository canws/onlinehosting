<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'houseno',
        'landmark',
        'city',
        'pincode',
        'state',
        'address_type'
    ];

    protected $guarded = []; 

    public function user(){
        return $this->belongsTo(User::class,'id');
    }
}
