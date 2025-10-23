<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class OrderDetail extends Model
{
    use PreventDemoModeChanges;

    // Mohammad Hassan
    protected $fillable = [
        'order_id',
        'seller_id',
        'product_id',
        'variation',
        'price',
        'tax',
        'shipping_cost',
        'shipping_type',
        'product_referral_code',
        'quantity',
        'payment_status',
        'delivery_status'
    ];

    protected $attributes = [
        'tax' => 0.00
    ];

    protected $casts = [
        'tax' => 'decimal:2',
        'price' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
    ];

    /**
     * Set the tax attribute.
     * Ensures tax is never null by setting default value.
     */
    public function setTaxAttribute($value)
    {
        $this->attributes['tax'] = $value ?? 0.00;
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function pickup_point()
    {
        return $this->belongsTo(PickupPoint::class);
    }

    public function refund_request()
    {
        return $this->hasOne(RefundRequest::class);
    }

    public function affiliate_log()
    {
        return $this->hasMany(AffiliateLog::class);
    }
}
