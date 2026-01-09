<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'order_number',
        'customer_id',
        'subtotal',
        'discount',
        'discount_amount',
        'grand_total',
        'status',
        'payment_status'
    ];

    protected $casts = [
        'created_at' => 'datetime:d-m-Y h:i A',
        'updated_at' => 'datetime:d-m-Y h:i A',
    ];


    public function orderItem()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'order_id', 'id');
    }
}
