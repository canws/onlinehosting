<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function cart(){
        return $this->hasMany(Cart::class, 'cart_id');
    }

    public function wishlist(){
        return $this->hasMany(Wishlist::class, 'wishlist_id');
    }

    public function attributes(){
        return $this->hasMany(Attribute::class, 'products_id');
    }

    public function featured_products(){
        return $this->hasOne(FeaturedProduct::class, 'product_id');
    }
}
