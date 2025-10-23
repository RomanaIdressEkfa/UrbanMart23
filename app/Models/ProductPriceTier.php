<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class ProductPriceTier extends Model
{
    use HasFactory, PreventDemoModeChanges;
    
    // Mohammad Hassan
    protected $fillable = ['product_id', 'min_qty', 'price'];
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
