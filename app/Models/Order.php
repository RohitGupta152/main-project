<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Order extends Model
{
    use HasFactory;
    // protected $fillable = ['order_id', 'user_name', 'email', 'total_amount', 'total_qty'];

    // public function products()
    // {
    //     return $this->hasMany(Product::class, 'order_id', 'order_id');
    // }
    protected $table = 'orders';

    // protected $fillable = ['user_id', 'order_no', 'customer_name', 'email', 'charged_amount', 'contact_no', 'address1', 'address2', 'pin_code', 'city', 'state','country', 'total_amount', 'total_qty', 'weight', 'length', 'height', 'updated_orders', 'created_date', 'updated_date'];

    // public function products()
    // {
    //     return $this->hasMany(Product::class, 'order_table_id', 'id'); // No foreign key, just a relation
    // }

    public $timestamps = false;


    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'user_id', 'id');
    // }











    protected $fillable = [
        'user_id',
        'order_no',
        'customer_name',
        'email',
        'contact_no',
        'address1',
        'address2',
        'pin_code',
        'city',
        'state',
        'country',
        'charged_amount',
        'charged_weight',
        'weight',
        'length',
        'width',
        'height',
        'total_amount',
        'total_qty',
        'updated_orders',
        'status',
        'is_deleted',
        'created_date',
        'updated_date'
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'order_table_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
