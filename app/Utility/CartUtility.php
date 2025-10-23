<?php

namespace App\Utility;

use App\Models\Cart;
use Cookie;

class CartUtility
{

    // Mohammad Hassan - Enhanced cart variant creation with color support
    public static function create_cart_variant($product, $request)
    {
        $str = null;
        if (isset($request['color'])) {
            $str = $request['color'];
        }

        if (isset($product->choice_options) && count(json_decode($product->choice_options)) > 0) {
            //Gets all the choice values of customer choice option and generate a string like Black-S-Cotton
            foreach (json_decode($product->choice_options) as $key => $choice) {
                $attribute_key = 'attribute_id_' . $choice->attribute_id;
                if (isset($request[$attribute_key])) {
                    if ($str != null) {
                        $str .= '-' . str_replace(' ', '', $request[$attribute_key]);
                    } else {
                        $str .= str_replace(' ', '', $request[$attribute_key]);
                    }
                }
            }
        }
        
        // Handle selected_items from table-based selection
        if (isset($request['selected_items'])) {
            $selectedItems = json_decode($request['selected_items'], true);
            if (is_array($selectedItems) && count($selectedItems) > 0) {
                // For multiple items, we'll handle them separately in the controller
                // For now, just use the first item's variant
                $firstItem = $selectedItems[0];
                if (isset($firstItem['size'])) {
                    $str = $firstItem['size'];
                }
            }
        }
        
        return $str;
    }

    // Mohammad Hassan - Enhanced price calculation with variant-specific pricing and wholesale discounts
   // app/Utility/CartUtility.php

public static function get_price($product, $product_stock, $quantity, $user = null, $selected_items = null)
{
    if ($product_stock === null) {
        $price = $product->unit_price ?? 0;
    } else {
        $price = $product_stock->price;
    }

    if ($user === null) {
        $user = auth()->user();
    }

    if ($user && $user->user_type == 'wholesaler') {
        // ভ্যারিয়েন্ট-ভিত্তিক হোলসেল দাম চেক করা
        if ($product_stock && $product_stock->wholesalePrices->isNotEmpty()) {
            $variantWholesalePrice = $product_stock->wholesalePrices
                ->where('min_qty', '<=', $quantity)
                ->sortByDesc('min_qty')
                ->first();
            if ($variantWholesalePrice) {
                return (float) $variantWholesalePrice->price;
            }
        }
        
        // সাধারণ হোলসেল টায়ার চেক করা
        if ($product->priceTiers && $product->priceTiers->isNotEmpty()) {
             $total_cart_quantity = 0;
             if (request()->session()->has('cart')) {
                 $cart_items = request()->session()->get('cart');
                 foreach ($cart_items as $item) {
                     $total_cart_quantity += $item['quantity'];
                 }
             } else {
                 $total_cart_quantity = $quantity;
             }

            $tierPrice = $product->priceTiers
                ->where('min_qty', '<=', $total_cart_quantity)
                ->sortByDesc('min_qty')
                ->first();
            if ($tierPrice) {
                return (float) $tierPrice->price;
            }
        }
    }
    
    // সাধারণ ডিসকাউন্ট
    if ($product_stock !== null && isset($product_stock->discount_price) && $product_stock->discount_price > 0) {
        $price = $product_stock->discount_price;
    } else {
        $price = self::discount_calculation($product, $price);
    }
    
    return $price;
}

    public static function discount_calculation($product, $price)
    {
        $discount_applicable = false;

        if (
            $product->discount_start_date == null ||
            (strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
                strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date)
        ) {
            $discount_applicable = true;
        }

        if ($discount_applicable) {
            if ($product->discount_type == 'percent') {
                $price -= ($price * $product->discount) / 100;
            } elseif ($product->discount_type == 'amount') {
                $price -= $product->discount;
            }
        }
        return $price;
    }

    public static function tax_calculation($product, $price)
    {
        $tax = 0;
        foreach ($product->taxes as $product_tax) {
            if ($product_tax->tax_type == 'percent') {
                $tax += ($price * $product_tax->tax) / 100;
            } elseif ($product_tax->tax_type == 'amount') {
                $tax += $product_tax->tax;
            }
        }

        return $tax;
    }

    // Mohammad Hassan - Enhanced cart data saving with color variant and price tier support
    public static function save_cart_data($cart, $product, $request, $quantity, $price, $tax, $shipping_cost, $product_stock = null)
    {
        // Mohammad Hassan - Enhanced save_cart_data with variant-specific pricing
        $selected_items = isset($request['selected_items']) ? json_decode($request['selected_items'], true) : null;
        $price = self::get_price($product, $product_stock, $quantity, auth()->user(), $selected_items);
        $tax = self::tax_calculation($product, $price);
        
        $cart->product_id = $product->id;
        $cart->price = $price;
        $cart->tax = $tax;
        $cart->shipping_cost = $shipping_cost;
        $cart->quantity = $quantity;
        
        // Mohammad Hassan - Store unit price for variant-specific pricing
        $cart->unit_price = $price;
        
        // Mohammad Hassan - Store color variant if available
        if (isset($request['color'])) {
            $cart->color_variant = $request['color'];
        }
        
        // Mohammad Hassan - Store variant name for display purposes
        $variant = CartUtility::create_cart_variant($product, $request);
        if ($variant) {
            $cart->variant_name = $variant;
        }
        
        // Mohammad Hassan - Handle selected_items from table-based variant selection
        if (isset($request['selected_items'])) {
            $selectedItems = json_decode($request['selected_items'], true);
            if (is_array($selectedItems) && count($selectedItems) > 0) {
                $firstItem = $selectedItems[0];
                if (isset($firstItem['variant_name'])) {
                    $cart->variant_name = $firstItem['variant_name'];
                }
                if (isset($firstItem['unit_price'])) {
                    $cart->unit_price = $firstItem['unit_price'];
                }
            }
        }
        
        $cart->variation = CartUtility::create_cart_variant($product, $request);
        $cart->owner_id = $product->user_id;
        $cart->product_referral_code = null;

        if (Cookie::has('referred_product_id') && Cookie::get('referred_product_id') == $product->id) {
            $cart->product_referral_code = Cookie::get('product_referral_code');
        }

        $cart->save();
    }

    public static function check_auction_in_cart($carts)
    {
        foreach ($carts as $cart) {
            if ($cart->product->auction_product == 1) {
                return true;
            }
        }

        return false;
    }
}
