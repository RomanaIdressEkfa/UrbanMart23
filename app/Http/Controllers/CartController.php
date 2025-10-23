<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Carrier;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Cart;
use App\Models\Country;
use Auth;
use App\Utility\CartUtility;
use Session;
use Cookie;

class CartController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user() != null) {
            $user_id = Auth::user()->id;
            if ($request->session()->get('temp_user_id')) {
                Cart::where('temp_user_id', $request->session()->get('temp_user_id'))
                    ->update(
                        [
                            'user_id' => $user_id,
                            'temp_user_id' => null
                        ]
                    );

                Session::forget('temp_user_id');
            }
            $carts = Cart::where('user_id', $user_id)->get();
        } else {
            $temp_user_id = $request->session()->get('temp_user_id');
            $carts = ($temp_user_id != null) ? Cart::where('temp_user_id', $temp_user_id)->get() : [];
        }
        if (count($carts) > 0) {
            $carts->toQuery()->update(['shipping_cost' => 0]);
            $carts = $carts->fresh();
        }

        return view('frontend.view_cart', compact('carts'));
    }

    public function showCartModal(Request $request)
    {
        $product = Product::find($request->id);
        return view('frontend.partials.cart.addToCart', compact('product'));
    }

    public function showCartModalAuction(Request $request)
    {
        $product = Product::find($request->id);
        return view('auction.frontend.addToCartAuction', compact('product'));
    }

   // app/Http/Controllers/CartController.php

// public function addToCart(Request $request)
// {
//     $product = Product::find($request->id);
//     if (!$product) {
//         return response()->json(['status' => 0, 'message' => translate('Product not found.')]);
//     }

//     $user = auth()->user();
//     $temp_user_id = null;
//     if (!$user) {
//         $temp_user_id = $request->session()->get('temp_user_id') ?: bin2hex(random_bytes(10));
//         $request->session()->put('temp_user_id', $temp_user_id);
//     }

//     $items_to_process = [];
//     if ($request->has('selected_items')) {
//         $selectedItems = json_decode($request->selected_items, true);
//         if (is_array($selectedItems)) {
//             foreach ($selectedItems as $item) {
//                 if (!empty($item['stock_id']) && !empty($item['quantity']) && $item['quantity'] > 0) {
//                     $items_to_process[] = ['stock_id' => $item['stock_id'], 'quantity' => (int)$item['quantity']];
//                 }
//             }
//         }
//     }

//     if (empty($items_to_process)) {
//         return response()->json(['status' => 0, 'message' => translate('Please select product options and quantity.')]);
//     }

//     $added_cart_items = [];
//     try {
//         foreach ($items_to_process as $item) {
//             $stock = \App\Models\ProductStock::find($item['stock_id']);
//             if (!$stock || $stock->qty < $item['quantity']) continue;

//             $cartQuery = Cart::query();
//             if ($user) {
//                 $cartQuery->where('user_id', $user->id);
//             } else {
//                 $cartQuery->where('temp_user_id', $temp_user_id);
//             }
//             $existing_cart = $cartQuery->where('product_id', $product->id)->where('variation', $stock->variant)->first();

//             if ($existing_cart) {
//                 $existing_cart->quantity += $item['quantity'];
//                 // Recalculate price and tax based on updated quantity and variant
//                 $unit_price = \App\Utility\CartUtility::get_price($product, $stock, $existing_cart->quantity, $user);
//                 $tax = \App\Utility\CartUtility::tax_calculation($product, $unit_price);
//                 $existing_cart->price = $unit_price;
//                 $existing_cart->unit_price = $unit_price;
//                 $existing_cart->tax = $tax;
//                 $existing_cart->save();
//                 $added_cart_items[] = $existing_cart;
//             } else {
//                 $cart = new Cart;
//                 $cart->owner_id = $product->user_id;
//                 $cart->user_id = $user ? $user->id : null;
//                 $cart->temp_user_id = $temp_user_id;
//                 $cart->product_id = $product->id;
//                 $cart->variation = $stock->variant;
//                 $unit_price = \App\Utility\CartUtility::get_price($product, $stock, $item['quantity'], $user);
//                 $tax = \App\Utility\CartUtility::tax_calculation($product, $unit_price);
//                 $cart->price = $unit_price;
//                 $cart->unit_price = $unit_price;
//                 $cart->tax = $tax;
//                 $cart->shipping_cost = 0;
//                 $cart->quantity = $item['quantity'];
//                 $cart->save();
//                 $added_cart_items[] = $cart;
//             }
//         }
//     } catch (\Exception $e) {
//         \Log::error('Add to Cart Error: '.$e->getMessage());
//         return response()->json(['status' => 0, 'message' => translate('Something went wrong. Please check logs.')]);
//     }

//     $carts = $user ? Cart::where('user_id', $user->id)->get() : Cart::where('temp_user_id', $temp_user_id)->get();
//     return response()->json([
//         'status' => 1,
//         'message' => translate('Product(s) added to cart successfully'),
//         'cart_count' => count($carts),
//         'modal_view' => view('frontend.partials.cart.addedToCart', compact('product', 'added_cart_items'))->render(),
//         'nav_cart_view' => view('frontend.partials.cart.cart', ['carts' => $carts])->render(),
//     ]);
// }



// app/Http-Contratarallers/CartController.php

public function addToCart(Request $request)
{
    $product = Product::find($request->id);
    if (!$product) {
        return response()->json(['status' => 0, 'message' => translate('Product not found.')]);
    }

    $user = auth()->user();
    $user_id = $user ? $user->id : null;
    $temp_user_id = null;

    if (!$user) {
        $temp_user_id = $request->session()->get('temp_user_id') ?: bin2hex(random_bytes(10));
        $request->session()->put('temp_user_id', $temp_user_id);
    }

    $added_cart_items = [];
    $product_ids = [];

    // --- একাধিক ভ্যারিয়েন্ট (selected_items) হ্যান্ডেল করার জন্য ---
    if ($request->has('selected_items')) {
        $selected_items = json_decode($request->selected_items, true);
        if (!is_array($selected_items)) {
            return response()->json(['status' => 0, 'message' => translate('Invalid items selected.')]);
        }

        foreach ($selected_items as $item) {
            $stock_id = $item['stock_id'] ?? null;
            $quantity = $item['quantity'] ?? 0;
            if (!$stock_id || $quantity <= 0) {
                continue;
            }

            $product_stock = $product->stocks->find($stock_id);
            if (!$product_stock) {
                continue;
            }

            $variant = $product_stock->variant;
            $cartQuery = Cart::query();
            if ($user_id) {
                $cartQuery->where('user_id', $user_id);
            } else {
                $cartQuery->where('temp_user_id', $temp_user_id);
            }
            $cart = $cartQuery->where('product_id', $product->id)->where('variation', $variant)->first();

            // *** প্রধান এবং চূড়ান্ত পরিবর্তন: JavaScript থেকে পাঠানো unit_price ব্যবহার করা হচ্ছে ***
            if (isset($item['unit_price']) && is_numeric($item['unit_price'])) {
                $price = (float) $item['unit_price'];
            } else {
                // ফলব্যাক: যদি কোনো কারণে unit_price না আসে, তাহলে পুরনো পদ্ধতিতে দাম বের করা হবে
                $price = CartUtility::get_price($product, $product_stock, $quantity, $user);
            }
            // *** দাম নির্ধারণের লজিক শেষ ***

            if ($cart) {
                $quantity = $cart->quantity + $quantity;
            } else {
                $cart = new Cart;
                $cart->owner_id = $product->user_id;
                $cart->user_id = $user_id;
                $cart->temp_user_id = $temp_user_id;
                $cart->product_id = $product->id;
                $cart->variation = $variant;
            }
            
            // স্টক চেক (প্রি-অর্ডার ছাড়া)
            if (!$item['is_preorder'] && $product->digital != 1 && $product_stock->qty < $quantity) {
                return response()->json([
                    'status' => 0,
                    'message' => translate('The requested quantity is not available for') . ' ' . $product->getTranslation('name') . ' (' . $variant . ')'
                ]);
            }

            $tax = CartUtility::tax_calculation($product, $price);

            $cart->price = $price;
            $cart->unit_price = $price; // unit_price কলামটিও আপডেট করা হচ্ছে
            $cart->quantity = $quantity;
            $cart->tax = $tax;
            $cart->save();

            $added_cart_items[] = $cart->fresh();
        }

    } else {
        // --- একটি মাত্র ভ্যারিয়েন্ট হ্যান্ডেল করার জন্য (যদি selected_items না থাকে) ---
        // এই অংশটি আপনার পুরনো কোড থেকে নেওয়া হয়েছে এবং এটি অপরিবর্তিত থাকতে পারে।
        // তবে আমি এটিকেও নতুন লজিকের সাথে সামঞ্জস্যপূর্ণ করে দিচ্ছি।
        $str = '';
        $tax = 0;
        $quantity = $request->quantity;

        if ($request->has('color')) {
            $str = $request['color'];
        }

        if ($product->choice_options != null) {
            foreach (json_decode($product->choice_options) as $key => $choice) {
                if (isset($request['attribute_id_' . $choice->attribute_id])) {
                    $str .= $str != '' ? '-' . str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]) : str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
                }
            }
        }
        
        $product_stock = $product->stocks->where('variant', $str)->first();
        if(!$product_stock){
            return response()->json(['status' => 0, 'message' => translate('Variant not found.')]);
        }
        
        $price = CartUtility::get_price($product, $product_stock, $quantity, $user);
        $tax = CartUtility::tax_calculation($product, $price);

        $cart = Cart::updateOrCreate([
            'user_id' => $user_id,
            'temp_user_id' => $temp_user_id,
            'product_id' => $product->id,
            'variation' => $str,
        ], [
            'owner_id' => $product->user_id,
            'price' => $price,
            'unit_price' => $price,
            'tax' => $tax,
            'quantity' => DB::raw("quantity + $quantity")
        ]);

        $added_cart_items[] = $cart->fresh();
    }


    // --- রেসপন্স পাঠানো ---
    if (empty($added_cart_items)) {
        return response()->json(['status' => 0, 'message' => translate('No items were added to the cart.')]);
    }
    
    $carts = $user ? Cart::where('user_id', $user->id)->get() : Cart::where('temp_user_id', $temp_user_id)->get();
    return response()->json([
        'status' => 1,
        'message' => translate('Product(s) added to cart successfully'),
        'cart_count' => count($carts),
        'modal_view' => view('frontend.partials.cart.addedToCart', compact('product', 'added_cart_items'))->render(),
        'nav_cart_view' => view('frontend.partials.cart.cart', compact('carts'))->render(),
    ]);
}

    private function addMultipleVariantsToCart($request, $authUser, $product)
    {
        $selected_items = json_decode($request->selected_items, true);
        $added_count = 0;
        $total_cart_count = 0;
        $added_cart_items = []; // Mohammad Hassan - Collect all added cart items

        if($authUser != null) {
            $user_id = $authUser->id;
            $data['user_id'] = $user_id;
        } else {
            $temp_user_id = $request->session()->get('temp_user_id');
            $data['temp_user_id'] = $temp_user_id;
        }

        foreach ($selected_items as $item) {
            if (!isset($item['size']) || !isset($item['quantity']) || $item['quantity'] <= 0) {
                continue;
            }

            $variant = $item['size'];
            $quantity = $item['quantity'];

            // Get product stock for this variant
            $product_stock = $product->stocks->where('variant', $variant)->first();

            // Check if this is a preorder product
            $is_preorder = $product->isOutOfStock() && $product->isPreorderAvailable();

            // Mohammad Hassan - Check for existing cart item with same product and variant
            $existing_cart = null;
            if($authUser != null) {
                $existing_cart = Cart::where('user_id', $authUser->id)
                    ->where('product_id', $product->id)
                    ->where('variation', $variant)
                    ->first();
            } else {
                $temp_user_id = $request->session()->get('temp_user_id');
                $existing_cart = Cart::where('temp_user_id', $temp_user_id)
                    ->where('product_id', $product->id)
                    ->where('variation', $variant)
                    ->first();
            }

            if ($existing_cart) {
                // Mohammad Hassan - Update existing cart item quantity
                $new_quantity = $existing_cart->quantity + $quantity;

                // Check stock availability for the new total quantity
                if (!$is_preorder && $product->digital == 0 && $product_stock !== null && $product_stock->qty < $new_quantity) {
                    continue; // Skip this variant if insufficient stock
                }

                // Update existing cart item
                $existing_cart->quantity = $new_quantity;

                // Recalculate price and tax for the updated quantity
                $price = CartUtility::get_price($product, $product_stock, $new_quantity, $authUser);
                $tax = CartUtility::tax_calculation($product, $price);

                $existing_cart->price = $price;
                $existing_cart->unit_price = $price;
                $existing_cart->tax = $tax;

                $existing_cart->save();
                $added_count++;
                $added_cart_items[] = $existing_cart; // Mohammad Hassan - Collect updated cart item
            } else {
                // Mohammad Hassan - Create new cart entry for new variant
                // Check stock availability for non-preorder products
                if (!$is_preorder && $product->digital == 0 && $product_stock !== null && $product_stock->qty < $quantity) {
                    continue; // Skip this variant if insufficient stock
                }

                $cart = new Cart;
                $cart->owner_id = $product->user_id;
                $cart->product_id = $product->id;
                $cart->variation = $variant;
                $cart->quantity = $quantity;

                // Calculate price and tax
                $price = CartUtility::get_price($product, $product_stock, $quantity, $authUser);
                $tax = CartUtility::tax_calculation($product, $price);

                $cart->price = $price;
                $cart->unit_price = $price;
                $cart->tax = $tax;

                if($authUser != null) {
                    $cart->user_id = $authUser->id;
                } else {
                    $cart->temp_user_id = $request->session()->get('temp_user_id');
                }

                $cart->save();
                $added_count++;
                $added_cart_items[] = $cart; // Mohammad Hassan - Collect new cart item
            }
        }

        // Get updated cart count
        if($authUser != null) {
            $carts = Cart::where('user_id', $authUser->id)->get();
        } else {
            $temp_user_id = $request->session()->get('temp_user_id');
            $carts = Cart::where('temp_user_id', $temp_user_id)->get();
        }

        // Mohammad Hassan - Get the last added cart item for modal display
        $last_cart = null;
        if($authUser != null) {
            $last_cart = Cart::where('user_id', $authUser->id)->where('product_id', $product->id)->latest()->first();
        } else {
            $temp_user_id = $request->session()->get('temp_user_id');
            $last_cart = Cart::where('temp_user_id', $temp_user_id)->where('product_id', $product->id)->latest()->first();
        }

        return array(
            'status' => 1,
            'cart_count' => count($carts),
            'modal_view' => view('frontend.partials.cart.addedToCart', compact('product', 'added_cart_items'))->render(),
            'nav_cart_view' => view('frontend.partials.cart.cart')->render(),
        );
    }

    //removes from Cart
    public function removeFromCart(Request $request)
    {
        Cart::destroy($request->id);
        $authUser = auth()->user();
        if ($authUser != null) {
            $user_id = $authUser->id;
            $carts = Cart::where('user_id', $user_id)->get();
        } else {
            $temp_user_id = $request->session()->get('temp_user_id');
            $carts = Cart::where('temp_user_id', $temp_user_id)->get();
        }

        return array(
            'cart_count' => count($carts),
            'cart_view' => view('frontend.partials.cart.cart_details', compact('carts'))->render(),
            'nav_cart_view' => view('frontend.partials.cart.cart')->render(),
        );
    }

    // Mohammad Hassan
    // Updated the quantity for a cart item with proper price recalculation
    public function updateQuantity(Request $request)
    {
        $cartItem = Cart::findOrFail($request->id);

        if ($cartItem['id'] == $request->id) {
            $product = Product::find($cartItem['product_id']);
            $product_stock = $product->stocks->where('variant', $cartItem['variation'])->first();
            $stock_quantity = $product_stock ? $product_stock->qty : 0;

            // Mohammad Hassan - Use CartUtility for consistent price calculation
            $authUser = auth()->user();
            $unit_price = CartUtility::get_price($product, $product_stock, $request->quantity, $authUser);
            $tax = CartUtility::tax_calculation($product, $unit_price);

            // Check stock availability (skip for preorder products)
            $is_preorder = $product->isOutOfStock() && $product->isPreorderAvailable();

            if (!$is_preorder && $stock_quantity < $request->quantity) {
                return array(
                    'status' => 0,
                    'message' => translate('Insufficient stock available'),
                    'cart_count' => 0,
                    'cart_view' => '',
                    'nav_cart_view' => '',
                );
            }

            // Check minimum quantity requirement
            if ($request->quantity < $product->min_qty) {
                return array(
                    'status' => 0,
                    'message' => translate('Minimum quantity required: ') . $product->min_qty,
                    'cart_count' => 0,
                    'cart_view' => '',
                    'nav_cart_view' => '',
                );
            }

            // Update cart item
            $cartItem['quantity'] = $request->quantity;
            $cartItem['price'] = $unit_price;
            $cartItem['unit_price'] = $unit_price;
            $cartItem['tax'] = $tax;
            $cartItem->save();
        }

        // Get updated cart items
        if (auth()->user() != null) {
            $user_id = Auth::user()->id;
            $carts = Cart::where('user_id', $user_id)->get();
        } else {
            $temp_user_id = $request->session()->get('temp_user_id');
            $carts = Cart::where('temp_user_id', $temp_user_id)->get();
        }

        return array(
            'status' => 1,
            'cart_count' => count($carts),
            'cart_view' => view('frontend.partials.cart.cart_details', compact('carts'))->render(),
            'nav_cart_view' => view('frontend.partials.cart.cart')->render(),
        );
    }


public function clearCartForBuyNow(Request $request)
{
    $query = Cart::query();
    if (auth()->check()) {
        $query->where('user_id', auth()->id());
    } else {
        $temp_user_id = $request->session()->get('temp_user_id');
        if ($temp_user_id) {
            $query->where('temp_user_id', $temp_user_id);
        } else {
            // যদি কার্টে কিছু না থাকে, তাহলে কিছু করার দরকার নেই
            return response()->json(['status' => 1, 'message' => 'Cart is already empty.']);
        }
    }

    $query->delete(); // ব্যবহারকারীর সব কার্ট আইটেম মুছে ফেলবে

    return response()->json(['status' => 1, 'message' => 'Cart cleared successfully for Buy Now.']);
}




    public function updateCartStatus(Request $request)
    {
        $product_ids = $request->product_id;

        if (auth()->user() != null) {
            $user_id = Auth::user()->id;
            $carts = Cart::where('user_id', $user_id)->get();
        } else {
            $temp_user_id = $request->session()->get('temp_user_id');
            $carts = Cart::where('temp_user_id', $temp_user_id)->get();
        }

        $coupon_applied = $carts->toQuery()->where('coupon_applied', 1)->first();
        if($coupon_applied != null){
            $owner_id = $coupon_applied->owner_id;
            $coupon_code = $coupon_applied->coupon_code;
            $user_carts = $carts->toQuery()->where('owner_id', $owner_id)->get();
            $coupon_discount = $user_carts->toQuery()->sum('discount');
            $user_carts->toQuery()->update(
                [
                    'discount' => 0.00,
                    'coupon_code' => '',
                    'coupon_applied' => 0
                ]
            );
        }

        $carts->toQuery()->update(['status' => 0]);
        if($product_ids != null){
            if($coupon_applied != null){
                $active_user_carts = $user_carts->toQuery()->whereIn('product_id', $product_ids)->get();
                if (count($active_user_carts) > 0) {
                    $active_user_carts->toQuery()->update(
                        [
                            'discount' => $coupon_discount / count($active_user_carts),
                            'coupon_code' => $coupon_code,
                            'coupon_applied' => 1
                        ]
                    );
                }
            }

            $carts->toQuery()->whereIn('product_id', $product_ids)->update(['status' => 1]);
        }
        $carts = $carts->fresh();

        return view('frontend.partials.cart.cart_details', compact('carts'))->render();
    }
}
