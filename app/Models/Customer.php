<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';

    protected $fillable = [
        'name', 
        'email', 
        'mobile', 
        'address',
        'country_id',
        'city_id',
        'image'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }
    
    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id', 'id');
    }
}
