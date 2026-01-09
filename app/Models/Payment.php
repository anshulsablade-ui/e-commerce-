<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'order_id',
        'gateway',
        'transaction_id',
        'amount',
        'currency',
        'status',
        'response',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
}
