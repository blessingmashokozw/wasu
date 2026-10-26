<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeliveryUpdate extends Model
{
    use HasFactory;

    protected $primaryKey = 'update_id';
    protected $fillable = ['delivery_id', 'updated_by', 'status', 'note'];
    public $timestamps = false;

    protected $casts = [
        'updated_at' => 'datetime',
    ];

    public function delivery()
    {
        return $this->belongsTo(Delivery::class, 'delivery_id', 'delivery_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'user_id');
    }
}
