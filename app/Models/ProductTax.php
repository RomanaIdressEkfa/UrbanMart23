<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class ProductTax extends Model
{
    use PreventDemoModeChanges;

    protected $fillable = [
        'product_id',
        'tax_id', 
        'tax',
        'tax_type'
    ];

    protected $attributes = [
        'tax' => 0.00
    ];

    protected $casts = [
        'tax' => 'decimal:2',
    ];

    /**
     * Set the tax attribute.
     * Ensures tax is never null by setting default value.
     */
    public function setTaxAttribute($value)
    {
        $this->attributes['tax'] = $value ?? 0.00;
    }
}
