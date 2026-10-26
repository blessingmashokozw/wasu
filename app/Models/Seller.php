<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Seller extends Model
{
    use HasFactory;

    protected $primaryKey = 'user_id';
    protected $fillable = ['business_name', 'business_reg_no', 'business_address', 'verification_status', 'verified_by', 'verified_at'];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by', 'user_id');
    }

    public function listings()
    {
        return $this->hasMany(Listing::class, 'seller_id', 'user_id');
    }

    public function orders(){
        return $this->hasMany(Order::class,'seller_id','user_id');
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
