<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Listing extends Model
{
    use HasFactory;

    protected $primaryKey = 'listing_id';
    protected $fillable = ['seller_id', 'category_id', 'listing_type', 'title', 'description', 'price', 'quantity_available', 'listing_status'];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id', 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function images()
    {
        return $this->hasMany(ListingImage::class, 'listing_id', 'listing_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'listing_id', 'listing_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'listing_id', 'listing_id');
    }
}
