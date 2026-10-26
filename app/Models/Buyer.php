<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Buyer extends Model
{
    use HasFactory;

    protected $primaryKey = 'user_id';
    protected $fillable = ['delivery_address', 'city'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
    public function orders()
    {
        return $this->hasMany(Order::class, 'buyer_id', 'user_id');
    }

    public function reviews()
    {
        return $this->user->reviewsReceived();
    }

    public function averageRating()
    {
        return $this->user->averageRating();
    }
}
