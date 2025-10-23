{{-- Mohammad Hassan --}}
{{-- Reusable Cart Table Component --}}
@php
    $is_checkout = $is_checkout ?? false;
    $show_actions = $show_actions ?? true;
    $show_selection = false; // Mohammad Hassan - Removed checkmark by default
    $products = $products ?? [];
    $product_variation = $product_variation ?? [];
    $owner_id = $owner_id ?? null;
    $seller_type = $seller_type ?? 'admin';
@endphp

<div class="table-responsive">
    <table class="table table-borderless">
        <thead class="bg-light">
            <tr>
                {{-- @if($show_selection) Removed checkbox header --}}
                {{-- <th class="border-0 fs-14 fw-600" width="5%">
                    <div class="aiz-checkbox-inline">
                        <label class="aiz-checkbox">
                            <input type="checkbox" class="check-all" @if(isset($all_selected) && $all_selected) checked @endif>
                            <span class="aiz-square-check"></span>
                        </label>
                    </div>
                </th> --}}
                {{-- @endif --}}
                <th class="border-0 fs-14 fw-600">{{ translate('Product') }}</th>
                <th class="border-0 fs-14 fw-600 text-center">{{ translate('Unit Price') }}</th>
                <th class="border-0 fs-14 fw-600 text-center">{{ translate('Qty') }}</th>
                <th class="border-0 fs-14 fw-600 text-right">{{ translate('Total') }}</th>
                @if($show_actions && !$is_checkout)
                <th class="border-0 fs-14 fw-600 text-center" width="5%">{{ translate('Action') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @php $subtotal = 0; @endphp
            @foreach ($carts as $key => $cart_item)
                @php
                    $product = get_single_product($cart_item['product_id']);
                    if($cart_item && $product) {
                        // Mohammad Hassan - Always use stored unit_price from cart to avoid double calculation
                        $unit_price = $cart_item['unit_price'] ?? $cart_item['price'] ?? 0;
                        $quantity = $cart_item['quantity'];
                        $total = $unit_price * $quantity;
                        $subtotal += $total;
                    }
                @endphp
                @if($cart_item && $product)
                    {{-- Mohammad Hassan - Cart item row integrated directly --}}
                    <tr class="cart-item-row">
                        {{-- @if($show_selection) Removed checkbox column --}}
                        {{-- <td class="align-middle">
                            <div class="aiz-checkbox">
                                <label class="aiz-checkbox">
                                    <input type="checkbox" class="check-one {{ isset($seller_type) ? 'check-one-'.$seller_type : '' }}"
                                           name="id[]" value="{{ $product->id }}"
                                           @if($cart_item['status'] == 1) checked @endif>
                                    <span class="aiz-square-check"></span>
                                </label>
                            </div>
                        </td> --}}
                        {{-- @endif --}}

                        <!-- Product Image & Name -->
                        <td class="align-middle">
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    {{-- Mohammad Hassan - Enhanced product image display --}}
                                    <a href="{{ route('product', $product->slug) }}" class="d-block position-relative">
                                        <img src="{{ uploaded_asset($product->thumbnail_img) }}"
                                             class="img-fit size-80px rounded border"
                                             alt="{{ $product->getTranslation('name') }}"
                                             onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                             style="object-fit: cover;">
                                        {{-- Product badge for variants --}}
                                        @if((isset($cart_item['variant_name']) && $cart_item['variant_name'] != '') || $cart_item['variation'] != '')
                                            <span class="badge badge-primary badge-sm position-absolute" style="top: -5px; right: -5px; font-size: 10px;">
                                                <i class="las la-tag"></i>
                                            </span>
                                        @endif
                                    </a>
                                </div>
                                <div class="flex-grow-1">
                                    {{-- Mohammad Hassan - Enhanced product name display with variant integration --}}
                                    <a href="{{ route('product', $product->slug) }}" class="text-decoration-none">
                                        <h6 class="fs-15 fw-600 text-dark mb-2 hover-text-primary line-height-1-4" title="{{ $product->getTranslation('name') }}">
                                            {{ $product->getTranslation('name') }}
                                        </h6>
                                    </a>

                                    {{-- Mohammad Hassan - Enhanced variant display for both cart and checkout --}}
                                    @php
                                        // Mohammad Hassan - Determine the best variant name to display
                                        $display_variant = '';
                                        if(isset($cart_item['variant_name']) && $cart_item['variant_name'] != '') {
                                            $display_variant = $cart_item['variant_name'];
                                        } elseif($cart_item['variation'] != '') {
                                            $display_variant = $cart_item['variation'];
                                        }
                                    @endphp

                                    @if($is_checkout)
                                        {{-- Mohammad Hassan - Enhanced checkout variant display to match cart styling --}}
                                        @if($display_variant != '')
                                            <div class="fs-13 fw-500 text-primary mb-1 d-flex align-items-center">
                                                <i class="las la-tag mr-1 text-primary"></i>
                                                <span class="fs-12 p-1 rounded bg-soft-primary">
                                                    <strong>{{ translate('Variant') }}:</strong> {{ $display_variant }}
                                                </span>
                                            </div>
                                        @endif

                                        {{-- Show color variant with enhanced styling to match cart --}}
                                        @if(isset($cart_item['color_variant']) && $cart_item['color_variant'] != '')
                                            <div class="fs-12 mb-1 d-flex align-items-center">
                                                <i class="las la-palette mr-1 text-info"></i>
                                                <span class="fs-11 p-1 rounded bg-soft-info">{{ translate('Color') }}: {{ $cart_item['color_variant'] }}</span>
                                            </div>
                                        @endif

                                        {{-- Show unique product identifier for variants in checkout --}}
                                        @if((isset($cart_item['variant_name']) && $cart_item['variant_name'] != '') || $cart_item['variation'] != '' || (isset($cart_item['color_variant']) && $cart_item['color_variant'] != ''))
                                            <div class="fs-11 text-muted mt-1">
                                                <i class="las la-fingerprint mr-1"></i>{{ translate('SKU') }}: {{ $product->id }}-{{ $cart_item['id'] }}
                                            </div>
                                        @endif

                                        {{-- Show quantity prominently in checkout --}}
                                        <div class="fs-12 fw-500 text-success mb-1">
                                            <i class="las la-cubes mr-1"></i><strong>{{ translate('Quantity') }}:</strong> {{ $cart_item['quantity'] }} {{ translate('pcs') }}
                                        </div>
                                    @else
                                        {{-- Mohammad Hassan - Enhanced cart view - show variant information prominently like order details --}}
                                        @php
                                            // Mohammad Hassan - Determine the best variant name to display
                                            $display_variant = '';
                                            if(isset($cart_item['variant_name']) && $cart_item['variant_name'] != '') {
                                                $display_variant = $cart_item['variant_name'];
                                            } elseif($cart_item['variation'] != '') {
                                                $display_variant = $cart_item['variation'];
                                            }
                                        @endphp

                                        @if($display_variant != '')
                                            <div class="fs-13 fw-500 text-primary mb-1 d-flex align-items-center">
                                                <i class="las la-tag mr-1 text-primary"></i>
                                                <span class="fs-12 p-1 rounded bg-soft-primary">
                                                    <strong>{{ translate('Variant') }}:</strong> {{ $display_variant }}
                                                </span>
                                            </div>
                                        @endif

                                        {{-- Show color variant with enhanced styling --}}
                                        @if(isset($cart_item['color_variant']) && $cart_item['color_variant'] != '')
                                            <div class="fs-12 mb-1 d-flex align-items-center">
                                                <i class="las la-palette mr-1 text-info"></i>
                                                <span class="fs-11 p-1 rounded bg-soft-info">{{ translate('Color') }}: {{ $cart_item['color_variant'] }}</span>
                                            </div>
                                        @endif

                                        {{-- Show unique product identifier for variants --}}
                                        @if((isset($cart_item['variant_name']) && $cart_item['variant_name'] != '') || $cart_item['variation'] != '' || (isset($cart_item['color_variant']) && $cart_item['color_variant'] != ''))
                                            <div class="fs-11 text-muted mt-1">
                                                <i class="las la-fingerprint mr-1"></i>{{ translate('SKU') }}: {{ $product->id }}-{{ $cart_item['id'] }}
                                            </div>
                                        @endif
                                    @endif

                                    {{-- Mohammad Hassan - Add preorder information display --}}
                    @if($product && $product->isOutOfStock() && $product->isPreorderAvailable())
                                        <div class="fs-12 mb-1">
                                            <span class="preorder-badge">
                                                <i class="las la-clock mr-1"></i>{{ translate('Pre-order Item') }}
                                            </span>
                                        </div>
                                        <div class="fs-11 text-muted">
                                            {{ translate('Advance payment required') }} ({{ $product->getPreorderPaymentPercentage() }}%)
                                        </div>
                                        @if($product->available_date)
                                            <div class="fs-11 text-info">
                                                {{ translate('Expected availability') }}: {{ date('M d, Y', strtotime($product->available_date)) }}
                                            </div>
                                        @endif
                                    @endif

                                    <div class="fs-12 text-secondary mt-1">
                                        {{ translate('Tax') }}: {{ cart_product_tax($cart_item, $product) }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Unit Price -->
                        <td class="text-center align-middle">
                            <span class="fw-600 fs-14">{{ single_price($unit_price) }}</span>
                        </td>

                        <!-- Quantity -->
                        <td class="text-center align-middle">
                            {{-- Removed quantity increase/decrease buttons and input field --}}
                            <span class="fw-600 fs-14">{{ $quantity }}</span>
                        </td>

                        <!-- Total Price -->
                        <td class="text-right align-middle">
                            <span class="fw-700 fs-16 text-primary">{{ single_price($total) }}</span>
                        </td>

                        @if($show_actions && !$is_checkout)
                        <!-- Actions -->
                        <td class="text-center align-middle">
                            <a href="javascript:void(0)" onclick="removeFromCartView(event, {{ $cart_item['id'] }})"
                               class="btn btn-icon btn-sm btn-soft-danger" data-toggle="tooltip"
                               data-title="{{ translate('Remove') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                        @endif
                    </tr>
                @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="{{ $show_actions && !$is_checkout ? '4' : '3' }}" class="text-right"> {{-- Adjusted colspan --}}
                    <strong>{{ translate('Subtotal') }}: {{ single_price($subtotal) }}</strong>
                </td>
            </tr>
        </tfoot>
    </table>
</div>

