<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class Payment extends Model
{
    use PreventDemoModeChanges;

    // Mohammad Hassan - Updated fillable fields for payment tracking
    protected $fillable = [
        'seller_id',
        'order_id',
        'combined_order_id',
        'amount',
        'payment_details',
        'payment_method',
        'transaction_type',
        'transaction_status',
        'txn_code',
        'gateway_transaction_id',
        'gateway_response'
    ];

    // Mohammad Hassan
    protected $casts = [
        'payment_details' => 'array',
        'gateway_response' => 'array',
        'amount' => 'decimal:2'
    ];

    // Mohammad Hassan
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Mohammad Hassan
    public function combinedOrder()
    {
        return $this->belongsTo(CombinedOrder::class);
    }

    // Mohammad Hassan
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
