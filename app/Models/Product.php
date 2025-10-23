<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class Product extends Model
{
    use PreventDemoModeChanges;
    
    protected $guarded = ['choice_attributes'];
    
    protected $fillable = [
        'is_preorder', 'preorder_price', 'preorder_payment_percentage'
    ];

    protected $with = ['product_translations', 'taxes', 'thumbnail'];

    public function getTranslation($field = '', $lang = false)
    {
        $lang = $lang == false ? App::getLocale() : $lang;
        $product_translations = $this->product_translations->where('lang', $lang)->first();
        return $product_translations != null ? $product_translations->$field : $this->$field;
    }

    public function product_translations()
    {
        return $this->hasMany(ProductTranslation::class);
    }

    public function main_category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    public function frequently_bought_products()
    {
        return $this->hasMany(FrequentlyBoughtProduct::class);
    }

    public function product_categories()
    {
        return $this->hasMany(ProductCategory::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function product_queries()
    {
        return $this->hasMany(ProductQuery::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function stocks()
    {
        return $this->hasMany(ProductStock::class);
    }

    public function taxes()
    {
        return $this->hasMany(ProductTax::class);
    }

    public function flash_deal_products()
    {
        return $this->hasMany(FlashDealProduct::class);
    }

    public function bids()
    {
        return $this->hasMany(AuctionProductBid::class);
    }

    public function thumbnail()
    {
        return $this->belongsTo(Upload::class, 'thumbnail_img');
    }

    public function scopePhysical($query)
    {
        return $query->where('digital', 0);
    }

    public function scopeDigital($query)
    {
        return $query->where('digital', 1);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
    
    public function scopeIsApprovedPublished($query)
    {
        return $query->where('approved', '1')->where('published', 1);
    }

    public function last_viewed_products()
    {
        return $this->hasMany(LastViewedProduct::class);
    }

    public function warranty()
    {
        return $this->belongsTo(Warranty::class);
    }

    public function warrantyNote()
    {
        return $this->belongsTo(Note::class, 'warranty_note_id');
    }

    public function refundNote()
    {
        return $this->belongsTo(Note::class, 'refund_note_id');
    }

    // Mohammad Hassan
    public function isOutOfStock()
    {
        // For variant products, calculate total stock from all variants
        if ($this->variant_product && $this->stocks && $this->stocks->count() > 0) {
            $totalStock = $this->stocks->sum('qty');
            return $totalStock <= 0;
        }
        
        // For non-variant products, use current_stock or first stock entry
        if ($this->stocks && $this->stocks->count() > 0) {
            return $this->stocks->first()->qty <= 0;
        }
        
        // Fallback to current_stock field
        return $this->current_stock <= 0;
    }

    // Mohammad Hassan
    public function isPreorderAvailable()
    {
        // Check if product is out of stock and preorder system is enabled
        if (!$this->isOutOfStock()) {
            return false; // Product is in stock, no need for preorder
        }
        
        // For out of stock products, check if preorder is enabled for this specific product
        return $this->is_preorder == true;
    }

    // Mohammad Hassan
    public function preorders()
    {
        return $this->hasMany(Preorder::class, 'product_id');
    }

    // Mohammad Hassan - Pre-order helper methods
    public function getPreorderPrice()
    {
        if ($this->is_preorder && $this->preorder_price) {
            return $this->preorder_price;
        }
        // Default: 50% of the unit price for pre-order
        return $this->unit_price * 0.5;
    }

    public function getRemainingPrice()
    {
        // Remaining amount to be paid after product arrival
        return $this->unit_price - $this->getPreorderPrice();
    }
    
    public function getPreorderPaymentPercentage()
    {
        return $this->preorder_payment_percentage ?? 50;
    }
    
    public function isPreorderProduct()
    {
        return $this->is_preorder == 1;
    }
    
    public function getPreorderPriceByPercentage()
    {
        $percentage = $this->getPreorderPaymentPercentage();
        return ($this->unit_price * $percentage) / 100;
    }

    public function priceTiers()
    {
        return $this->hasMany(ProductPriceTier::class)->orderBy('min_qty', 'asc');
    }

}
