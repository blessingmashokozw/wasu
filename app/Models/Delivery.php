<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Delivery extends Model
{
    use HasFactory;

    protected $primaryKey = 'delivery_id';
    protected $fillable = ['order_id', 'delivery_method', 'estimated_date', 'current_status', 'received_at'];

    protected $casts = [
        'estimated_date' => 'date',
        'received_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function updates()
    {
        return $this->hasMany(DeliveryUpdate::class, 'delivery_id', 'delivery_id');
    }
}
