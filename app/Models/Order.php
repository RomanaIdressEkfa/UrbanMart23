<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class Order extends Model
{
    use PreventDemoModeChanges;
    
    // Mohammad Hassan - Add fillable fields for preorder functionality
    protected $fillable = [
        'is_preorder',
        'preorder_status',
        'paid_amount',
        'preorder_notes',
        'confirmed_at',
        'product_arrived_at',
        'completed_at',
        'cancelled_at',
        'delivery_date',
        'delivery_notes',
        'delivery_location'
    ];

    // Mohammad Hassan - Cast date fields
    protected $casts = [
        'is_preorder' => 'boolean',
        'confirmed_at' => 'datetime',
        'product_arrived_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'delivery_date' => 'date',
    ];

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function refund_requests()
    {
        return $this->hasMany(RefundRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shop()
    {
        return $this->hasOne(Shop::class, 'user_id', 'seller_id');
    }

    public function pickup_point()
    {
        return $this->belongsTo(PickupPoint::class);
    }

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }

    public function affiliate_log()
    {
        return $this->hasMany(AffiliateLog::class);
    }

    public function club_point()
    {
        return $this->hasMany(ClubPoint::class);
    }

    public function delivery_boy()
    {
        return $this->belongsTo(User::class, 'assign_delivery_boy', 'id');
    }

    public function proxy_cart_reference_id()
    {
        return $this->hasMany(ProxyPayment::class)->select('reference_id');
    }

    public function commissionHistory()
    {
        return $this->hasOne(CommissionHistory::class);
    }

    // Mohammad Hassan - Preorder helper methods
    public function isPreorder()
    {
        return $this->is_preorder;
    }

    public function getRemainingAmount()
    {
        return $this->grand_total - $this->paid_amount;
    }

    public function getPreorderStatusLabel()
    {
        return match($this->preorder_status) {
            'pending' => translate('Pending'),
            'confirmed' => translate('Confirmed'),
            'product_arrived' => translate('Product Arrived'),
            'completed' => translate('Completed'),
            'cancelled' => translate('Cancelled'),
            default => translate('Unknown')
        };
    }

    public function canMarkAsArrived()
    {
        return $this->is_preorder && $this->preorder_status === 'confirmed';
    }

    public function canCompletePayment()
    {
        return $this->is_preorder && $this->preorder_status === 'product_arrived';
    }
}
