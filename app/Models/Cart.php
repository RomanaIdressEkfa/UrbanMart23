<?php

namespace App\Models;

use App\Models\User;
use App\Models\Address;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{

    protected $guarded = [];
    // Mohammad Hassan
    // Mohammad Hassan - Removed price_tier_min_qty and tier_price as we use product_price_tiers table
    // Mohammad Hassan - Added unit_price for variant-specific pricing
    protected $fillable = ['address_id','price','unit_price','tax','shipping_cost','discount','product_referral_code','coupon_code','coupon_applied','quantity','user_id','temp_user_id','owner_id','product_id','variation','color_variant','variant_name'];

    protected $attributes = [
        'tax' => 0.00,
        'price' => 0.00,
        'unit_price' => 0.00,
        'shipping_cost' => 0.00,
        'discount' => 0.00
    ];

    protected $casts = [
        'tax' => 'decimal:2',
        'price' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount' => 'decimal:2',
    ];

    /**
     * Set the tax attribute.
     * Ensures tax is never null by setting default value.
     */
    public function setTaxAttribute($value)
    {
        $this->attributes['tax'] = $value ?? 0.00;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
