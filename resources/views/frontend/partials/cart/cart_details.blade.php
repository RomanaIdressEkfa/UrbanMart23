<div class="container">
    @php
        $cart_count = count($carts);
        $active_carts = $cart_count > 0 ? $carts->toQuery()->active()->get() : [];
    @endphp
    @if( $cart_count > 0 )
        <div class="row">
            <div class="col-lg-8">
                @if(auth()->check())
                    @php
                        $welcomeCoupon = ifUserHasWelcomeCouponAndNotUsed();
                    @endphp
                    @if($welcomeCoupon)
                        <div class="alert alert-primary align-items-center border d-flex flex-wrap justify-content-between rounded-0" style="border-color: #3490F3 !important;">
                            @php
                                $discount = $welcomeCoupon->discount_type == 'amount' ? single_price($welcomeCoupon->discount) : $welcomeCoupon->discount.'%';
                            @endphp
                            <div class="fw-400 fs-14" style="color: #3490F3 !important;">
                                {{ translate('Welcome Coupon') }} <strong>{{ $discount }}</strong> {{ translate('Discount on your Purchase Within') }} <strong>{{ $welcomeCoupon->validation_days }}</strong> {{ translate('days of Registration') }}
                            </div>
                            <button class="btn btn-sm mt-3 mt-lg-0 rounded-4" onclick="copyCouponCode('{{ $welcomeCoupon->coupon_code }}')" style="background-color: #3490F3; color: white;" >{{ translate('Copy coupon Code') }}</button>
                        </div>
                    @endif
                @endif
                <div class="bg-white p-3 p-lg-4 text-left">
                    <div class="mb-4">
                        {{-- Mohammad Hassan --}}
                        <!-- Cart Items using reusable components -->
                        @include('frontend.partials.cart.cart_table', [
                            'carts' => $carts,
                            'is_checkout' => false,
                            'show_actions' => true,
                            'show_selection' => true,
                            'all_selected' => count($active_carts) == $cart_count
                        ])
                        @php
                            $total = 0;
                            $admin_products = array();
                            $seller_products = array();
                            $admin_product_variation = array();
                            $seller_product_variation = array();
                            foreach ($carts as $key => $cartItem){
                                $product = get_single_product($cartItem['product_id']);

                                if($product->added_by == 'admin'){
                                    array_push($admin_products, $cartItem['product_id']);
                                    $admin_product_variation[] = $cartItem['variation'];
                                }
                                else{
                                    $product_ids = array();
                                    if(isset($seller_products[$product->user_id])){
                                        $product_ids = $seller_products[$product->user_id];
                                    }
                                    array_push($product_ids, $cartItem['product_id']);
                                    $seller_products[$product->user_id] = $product_ids;
                                    $seller_product_variation[$product->user_id][] = $cartItem['variation'];
                                }
                            }
                        @endphp
                    </div>
                </div>
            </div>

            <!-- Cart Summary -->
            <!-- Mohammad Hassan -->
            <div class="col-lg-4 mt-lg-0 mt-4 position-sticky" id="cart_summary" style="top: 100px; height: fit-content;">
                @include('frontend.partials.cart.cart_summary', ['proceed' => 1, 'carts' => $active_carts])
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-xl-8 mx-auto">
                <div class="border bg-white p-4">
                    <!-- Empty cart -->
                    <div class="text-center p-3">
                        <i class="las la-frown la-3x opacity-60 mb-3"></i>
                        <h3 class="h4 fw-700">{{translate('Your Cart is empty')}}</h3>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

