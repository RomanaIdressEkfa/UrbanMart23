<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class WholesalePrice extends Model
{
    use HasFactory, PreventDemoModeChanges;

    protected $fillable = [
        'product_stock_id',
        'min_qty',
        'max_qty',
        'price'
    ];

    protected $casts = [
        'min_qty' => 'integer',
        'max_qty' => 'integer',
        'price' => 'decimal:2'
    ];

    /**
     * Get the product stock that owns the wholesale price.
     */
    public function productStock()
    {
        return $this->belongsTo(ProductStock::class);
    }

    /**
     * Get the product through the product stock relationship.
     */
    public function product()
    {
        return $this->hasOneThrough(Product::class, ProductStock::class, 'id', 'id', 'product_stock_id', 'product_id');
    }

    /**
     * Scope to get wholesale prices for a specific quantity.
     */
    public function scopeForQuantity($query, $quantity)
    {
        return $query->where('min_qty', '<=', $quantity)
                    ->where(function($q) use ($quantity) {
                        $q->where('max_qty', '>=', $quantity)
                          ->orWhereNull('max_qty');
                    });
    }

    /**
     * Get the best wholesale price for a given quantity.
     */
    public static function getBestPriceForQuantity($productStockId, $quantity)
    {
        return static::where('product_stock_id', $productStockId)
                    ->forQuantity($quantity)
                    ->orderBy('min_qty', 'desc')
                    ->first();
    }
}
