<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Product extends Model
{
    use HasFactory;
    // protected $fillable = ['order_id', 'user_name', 'email', 'product_name', 'price', 'quantity'];

    // public function order()
    // {
    //     return $this->belongsTo(Order::class, 'order_id', 'order_id');
    // }
    
    protected $table = 'products';

    protected $fillable = ['order_table_id', 'order_no', 'product_name', 'price', 'quantity','created_date', 'updated_date'];

    /*'weight', 'length', 'width', 'height', */
    public $timestamps = false;

    public function order() {
        return $this->belongsTo(Order::class);
    }

}


