<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingChargeSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_enabled',
        'title',
        'description',
        'inside_dhaka_charge',
        'outside_dhaka_charge',
        'free_shipping_threshold',
        'delivery_time_inside',
        'delivery_time_outside'
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'inside_dhaka_charge' => 'decimal:2',
        'outside_dhaka_charge' => 'decimal:2',
        'free_shipping_threshold' => 'decimal:2'
    ];

    public static function getSettings()
    {
        return self::first() ?? self::create([
            'is_enabled' => true,
            'title' => 'শিপিং চার্জ',
            'inside_dhaka_charge' => 60.00,
            'outside_dhaka_charge' => 120.00,
            'delivery_time_inside' => '১-২ দিন',
            'delivery_time_outside' => '৩-৫ দিন'
        ]);
    }
}
