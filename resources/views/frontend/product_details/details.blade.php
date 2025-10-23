@php
    // Get product discount rate
    $product_discount_rate = 0;
    if (isset($detailedProduct->discount) && $detailedProduct->discount_type == 'percent') {
        $product_discount_rate = (float) $detailedProduct->discount;
    }

    // Get product tax rate
    $product_tax_rate = 0;
    if (isset($detailedProduct->tax) && $detailedProduct->tax_type == 'percent') {
        $product_tax_rate = (float) $detailedProduct->tax;
    }

    $price_tiers_json = '[]';
    if (
        Auth::check() &&
        Auth::user()->user_type == 'wholesaler' &&
        $detailedProduct->priceTiers &&
        count($detailedProduct->priceTiers) > 0
    ) {
        $tiers = collect($detailedProduct->priceTiers)
            ->map(function ($tier) {
                return ['min_qty' => (int) $tier->min_qty, 'price' => (float) $tier->price];
            })
            ->sortByDesc('min_qty')
            ->values(); // Sorting high to low is key for the JS logic
        $price_tiers_json = json_encode($tiers);
    }

    $base_price = $detailedProduct->unit_price;
    $discounted_price = $base_price;
    $has_discount = false;

    // Only apply regular discounts for non-wholesaler users
    if (!(Auth::check() && Auth::user()->user_type == 'wholesaler')) {
        if ($detailedProduct->discount_type == 'percent' && $detailedProduct->discount > 0) {
            $discounted_price = $base_price - ($base_price * $detailedProduct->discount) / 100;
        } elseif ($detailedProduct->discount_type == 'amount' && $detailedProduct->discount > 0) {
            $discounted_price = $base_price - $detailedProduct->discount;
        }
        $has_discount = $base_price > $discounted_price;
    }
@endphp

<div class="text-left">
    <!-- Product Name -->
    <h2 class="mb-3 fs-18 fw-700 text-dark">
        {{ $detailedProduct->getTranslation('name') }}
    </h2>

    <style>
        /* Scoped styles for product price box */
        .product-price-box {
            background: #f5f7fb;
            border: 1px solid #e6ebf5;
            border-radius: 12px;
        }

        .product-price-box .price-value {
            font-size: 26px;
            font-weight: 800;
            color: #2E3A59;
        }

        .product-price-box .price-label {
            font-size: 12px;
            color: #8a94a6;
        }

        .product-price-box .original {
            font-size: 18px;
            color: #9aa5b1;
            text-decoration: line-through;
        }

        .product-price-box .off-badge {
            background: #ff4d6d;
            color: #fff;
            font-weight: 700;
            border-radius: 20px;
            padding: 6px 12px;
            font-size: 13px;
        }

        .product-price-box .muted-note {
            color: #5f6c7b;
        }
    </style>

    {{-- <div class="product-price-box mb-4 p-3" style="max-width: calc(100% - 145px);">
        <div class="d-flex align-items-end flex-wrap">
            @if ($has_discount)
                <div class="mr-4 mb-2">
                    <div class="price-value">৳{{ number_format($discounted_price, 2) }}</div>
                    <div class="price-label">{{ translate('Discounted') }}</div>
                </div>
                <div class="mr-4 mb-2">
                    <div class="original">৳{{ number_format($base_price, 2) }}</div>
                    <div class="price-label">{{ translate('Original') }}</div>
                </div>
                <div class="ml-auto mb-2">
                    <span class="off-badge">
                        {{ $detailedProduct->discount_type == 'percent' ? $detailedProduct->discount . '% ' . translate('OFF') : '৳' . $detailedProduct->discount . ' ' . translate('OFF') }}
                    </span>
                </div>
            @else
                <div class="mr-4 mb-2">
                    <div class="price-value">৳{{ number_format($base_price, 2) }}</div>
                    <div class="price-label">{{ translate('Price') }}</div>
                </div>
            @endif
        </div>

        @if ($detailedProduct->tax > 0)
            <div class="mt-2">
                <small class="muted-note">
                    <i class="las la-receipt"></i>
                    {{ translate('Tax') }}:
                    {{ $detailedProduct->tax_type == 'percent' ? $detailedProduct->tax . '%' : '৳' . $detailedProduct->tax }}
                </small>
            </div>
        @endif
    </div> --}}

    <hr>

    <!-- Dynamic Color Section -->
    @php
        $__colors = json_decode($detailedProduct->colors ?? '[]');
        // Map color name => hex/code for quick lookup
        $__colorCodeByName = [];
        foreach ($__colors as $__code) {
            $__name = get_single_color_name($__code);
            $__colorCodeByName[$__name] = $__code;
        }
        $__stocks = $detailedProduct->stocks ?? collect();
        $__firstStock = $__stocks && count($__stocks) > 0 ? $__stocks[0] : null;
        $__initialDisplayName =
            $__firstStock && $__firstStock->variant
                ? $__firstStock->variant
                : (count($__colors) > 0
                    ? get_single_color_name($__colors[0])
                    : '');
    @endphp
    @if ($detailedProduct->stocks && count($detailedProduct->stocks) > 0)
        <div class="mb-4">
            <h5 class="mb-3 fs-16 fw-600">{{ translate('Color') }} :
                <span class="text-primary fw-700" id="selected-color-name">{{ $__initialDisplayName }}</span>
            </h5>
            <div class="d-flex flex-wrap" id="color-options">
                @foreach ($detailedProduct->stocks as $idx => $stock)
                    @php
                        $variantLabel = $stock->variant ?? '';
                        $parts = $variantLabel ? explode('-', $variantLabel) : [];
                        $colorName = $parts[0] ?? '';
                        $colorCode =
                            $colorName && isset($__colorCodeByName[$colorName])
                                ? $__colorCodeByName[$colorName]
                                : $__colors[0] ?? '';
                        $img = $stock->image ? uploaded_asset($stock->image) : null;
                    @endphp
                    <div class="color-option mr-3 mb-2 p-1 border @if ($idx == 0) selected-color @endif"
                        data-color="{{ $variantLabel }}" data-color-value="{{ $colorCode }}"
                        style="border-width: @if ($idx == 0) 2px @else 1px @endif; border-style: solid; border-color: @if ($idx == 0) #3D52A0 @else #ddd @endif; border-radius: 8px; cursor: pointer;"
                        onclick="selectColor(this, '{{ $variantLabel }}', '{{ $colorCode }}')">
                        <div class="color-swatch"
                            style="width: 56px; height: 50px; border-radius: 4px; position: relative; overflow: hidden;">
                            @if ($img)
                                <img src="{{ $img }}" class="img-fit w-100 h-100" alt="{{ $variantLabel }}"
                                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                            @else
                                <div style="width: 100%; height: 100%; background-color: {{ $colorCode }};"></div>
                            @endif
                            {{-- <span class="color-name"
                                style="position: absolute; bottom: 5px; left: 50%; transform: translateX(-50%);
                                         font-size: 10px; color: #000; text-shadow: 1px 1px 1px #fff;">
                                {{ $variantLabel }}
                            </span> --}}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- START: DYNAMIC PRICE TIERS (Only for Wholesalers) --}}
    @if (Auth::check() &&
            Auth::user()->user_type == 'wholesaler' &&
            $detailedProduct->priceTiers &&
            count($detailedProduct->priceTiers) > 0)
        <div class="mb-4">
            <h5 class="mb-3 fs-16 fw-600">{{ translate('Wholesale Price Tiers') }}</h5>
            <div class="d-flex flex-wrap mb-3" id="price-tier-options"
                style="gap: 12px; justify-content: flex-start; margin-right: 80px;">
                @foreach (collect($detailedProduct->priceTiers)->sortBy('min_qty') as $key => $tier)
                    <div class="price-tier-item text-center rounded-lg p-3 mb-2" data-price="{{ $tier->price }}"
                        data-min-qty="{{ $tier->min_qty }}" onclick="selectPriceTier(this)"
                        style="flex: 1 1 calc(25% - 12px); min-width: 110px;">
                        <div class="fs-18 fw-600">৳{{ $tier->price }}</div>
                        <div class="fs-13">{{ $tier->min_qty }} or more</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    {{-- END: DYNAMIC PRICE TIERS --}}

    <!-- Size/Variant Table -->
    <div class="mb-4">
        <h5 class="mb-3 fs-16 fw-600">{{ translate('Model/Size') }}</h5>
        @php
            $isOutOfStock = $detailedProduct->isOutOfStock();
            $isPreorderAvailable = $detailedProduct->isPreorderAvailable();
        @endphp
        <div class="size-table-container"
            style="max-height: 300px; overflow-y: auto; border: 1px solid #e0e0e0; border-radius: 8px;">
            <table class="table table-bordered mb-0" id="sizeTable">
                <thead class="bg-light sticky-top">
                    <tr >
                        @php
                            $attributeName = '';
                            if ($detailedProduct->choice_options != null) {
                                $choiceOptions = json_decode($detailedProduct->choice_options);
                                if (count($choiceOptions) > 0) {
                                    $attributeName = get_single_attribute_name($choiceOptions[0]->attribute_id);
                                }
                            }
                        @endphp
                        <th style="padding: 8px 12px;">{{ $attributeName ?: translate('Variant') }}</th>
                        <th style="padding: 8px 12px;">{{ translate('Unit Price') }}</th>
                        {{-- @if (Auth::check() && Auth::user()->user_type == 'wholesaler')
                            <th style="padding: 8px 12px;">{{ translate('Wholesale Tiers') }}</th>
                        @endif --}}
                        <th style="padding: 8px 12px;">{{ translate('Total Price') }}</th>
                        @if (!$isOutOfStock || $isPreorderAvailable)
                            <th style="padding: 8px 12px;">{{ translate('Quantity') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($detailedProduct->stocks as $key => $stock)
                        @php
                            $variantId = $stock->variant ?? $stock->id;
                            $variantName = $stock->variant ?? translate('Default');

                            // Use variant-specific discount price if available, otherwise use original price
                            $row_base_price = (float) $stock->price;
                            $variant_discount_price = (float) ($stock->discount_price ?? 0);
                            
                            // If variant has a discount price, use it; otherwise use original price
                            $row_discounted_price = $variant_discount_price > 0 ? $variant_discount_price : $row_base_price;
                            
                            // For wholesalers, still use original logic if no variant discount
                            if (!(Auth::check() && Auth::user()->user_type == 'wholesaler') && $variant_discount_price == 0) {
                                if ($detailedProduct->discount_type == 'percent' && $detailedProduct->discount > 0) {
                                    $row_discounted_price =
                                        $row_base_price - ($row_base_price * $detailedProduct->discount) / 100;
                                } elseif (
                                    $detailedProduct->discount_type == 'amount' &&
                                    $detailedProduct->discount > 0
                                ) {
                                    $row_discounted_price = $row_base_price - $detailedProduct->discount;
                                }
                            }
                        @endphp
                        @php
                            $isOutOfStock = $stock->qty === 0;
                            $isPreorderAvailable = $isOutOfStock && $detailedProduct->is_preorder;
                        @endphp
                        <tr data-size="{{ $variantId }}" data-original-price="{{ $stock->price }}"
                            data-discounted-price="{{ $variant_discount_price > 0 ? $variant_discount_price : $stock->price }}" data-stock-qty="{{ $stock->qty }}"
                            data-stock-id="{{ $stock->id }}"
                            data-is-preorder="{{ $isPreorderAvailable ? 'true' : 'false' }}" style="height: 60px;">
                            <td style="padding: 8px 12px;">{{ $variantName }}</td>
                            <td class="unit-price" style="padding: 8px 12px;">
                                @php
                                    // Determine the unit price based on user requirements
                                    $unit_price = $variant_discount_price > 0 ? $variant_discount_price : $stock->price;
                                    $original_price = $stock->price;
                                @endphp
                                @if($variant_discount_price > 0 && $variant_discount_price != $original_price)
                                    <div>
                                        <span class="text-primary fw-bold">৳{{ number_format($variant_discount_price, 2) }}</span>
                                        <br>
                                        <del class="text-muted">৳{{ number_format($original_price, 2) }}</del>
                                    </div>
                                @else
                                    ৳{{ number_format($stock->price, 2) }}
                                @endif
                            </td>
                            {{-- @if (Auth::check() && Auth::user()->user_type == 'wholesaler')
                                <td style="padding: 8px 12px;">
                                    @if ($stock->wholesalePrices && $stock->wholesalePrices->count() > 0)
                                        <div class="wholesale-tiers-variant" data-stock-id="{{ $stock->id }}">
                                            @foreach ($stock->wholesalePrices->sortBy('min_qty') as $tier)
                                                <div class="tier-item-variant mb-1" data-min-qty="{{ $tier->min_qty }}"
                                                    data-max-qty="{{ $tier->max_qty }}"
                                                    data-price="{{ $tier->price }}"
                                                    style="font-size: 12px; padding: 2px 6px; background: #f8f9fa; border-radius: 4px; margin-bottom: 2px;">
                                                    <span
                                                        class="text-primary">{{ $tier->min_qty }}{{ $tier->max_qty ? '-' . $tier->max_qty : '+' }}:</span>
                                                    <span class="fw-600">৳{{ number_format($tier->price, 2) }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <small class="text-muted">{{ translate('No wholesale tiers') }}</small>
                                    @endif
                                </td>
                            @endif --}}
                            <td class="total-price" style="padding: 8px 12px;">৳ 0.00</td>
                            @if (!$isOutOfStock || $isPreorderAvailable)
                                <td style="padding: 8px 12px;">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <button type="button" class="btn add-btn" data-row-id="{{ $variantId }}"
                                            style="background: #3D52A0; color: white; border-radius: 8px; padding: 6px 20px;"
                                            onclick="addToCartRow(this)">{{ translate('Add') }}</button>
                                        <div class="quantity-control d-flex align-items-center"
                                            data-row-id="{{ $variantId }}" style="display: none;">
                                            <button type="button" class="btn btn-sm minus-btn"
                                                style="background: #3D52A0; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;"
                                                onclick="decreaseQuantity(this)">-</button>
                                            <input type="number" class="quantity-input mx-2 text-center"
                                                value="0" min="0"
                                                @if ($isOutOfStock && $isPreorderAvailable) max="999999" @else max="{{ $stock->qty }}" @endif
                                                style="width: 40px; height: 30px;">
                                            <button type="button" class="btn btn-sm plus-btn"
                                                style="background: #3D52A0; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;"
                                                onclick="increaseQuantity(this)">+</button>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @if ($isOutOfStock && $isPreorderAvailable)
                                            <small class="text-warning"><i class="fas fa-exclamation-triangle"></i>
                                                {{ translate('Pre-order Available') }}</small>
                                        @else
                                            <small class="text-muted stock-text">{{ translate('Stock') }}:
                                                {{ $stock->qty }}</small>
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if ($detailedProduct->auction_product != 1)
        <form id="option-choice-form" method="POST" action="javascript:void(0);">
            @csrf
            <input type="hidden" name="id" value="{{ $detailedProduct->id }}">
            <input type="hidden" name="quantity" value="0">
        </form>
    @endif

    {{-- Mohammad Hassan - Enhanced Purchase Buttons --}}
    @if (!$detailedProduct->auction_product)
        <div class="mt-4 mb-3">
            @php
                $isOutOfStock = $detailedProduct->isOutOfStock();
                $isPreorderAvailable = $detailedProduct->isPreorderAvailable();
            @endphp

            @if ($isOutOfStock && $isPreorderAvailable)
                {{-- Pre-order buttons for out of stock products with pre-order enabled --}}
                <div class="alert alert-warning mb-3">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    {{ translate('This product is currently out of stock. You can place a pre-order.') }}
                </div>
                <div class="d-flex flex-wrap gap-3">
                    <button type="button" class="btn btn-primary buy-now fw-600 px-4 py-2 rounded-lg"
                        style="min-width: 160px; background: #fd7e14; border: none;"
                        onclick="buyNowFromTable(true)">
                        <i class="la la-calendar-check mr-1"></i> {{ translate('Pre-order Now') }}
                    </button>
                </div>
                <div class="mt-2">
                    <small class="text-muted">
                        <i class="fas fa-info-circle text-info"></i>
                        {{ translate('Pre-order requires advance payment') }} ({{ $detailedProduct->getPreorderPaymentPercentage() }}%). {{ translate('Remaining amount due on delivery.') }}
                    </small>
                </div>
            @elseif($isOutOfStock && !$isPreorderAvailable)
                {{-- Out of stock without pre-order option --}}
                <div class="alert alert-danger mb-3">
                    <i class="fas fa-times-circle mr-2"></i>
                    {{ translate('This product is currently out of stock.') }}
                </div>
                <div class="d-flex flex-wrap gap-3">
                    <button type="button" class="btn btn-secondary fw-600 px-4 py-2 rounded-lg" disabled
                        style="min-width: 160px; background: #6c757d; border: none; cursor: not-allowed;">
                        <i class="las la-ban mr-1"></i> {{ translate('Out of Stock') }}
                    </button>
                </div>
                <div class="mt-2">
                    <small class="text-muted">
                        <i class="fas fa-info-circle text-secondary"></i>
                        {{ translate('This product will be available soon. Please check back later.') }}
                    </small>
                </div>
            @else
                {{-- Regular buttons for in-stock products --}}
                <div class="d-flex flex-wrap gap-3">
                    <button type="button" class="btn btn-info add-to-cart fw-600 px-4 py-2 rounded-lg text-white"
                        style="min-width: 160px; background: #17a2b8; border: none;"
                        onclick="addToCartFromTable()">
                        <i class="las la-shopping-bag mr-1"></i> {{ translate('Add to Cart') }}
                    </button>
                    <button type="button" class="btn btn-primary buy-now fw-600 px-4 py-2 rounded-lg"
                        style="min-width: 160px; background: #3D52A0; border: none;"
                        onclick="buyNowFromTable()">
                        <i class="la la-shopping-cart mr-1"></i> {{ translate('Buy Now') }}
                    </button>
                </div>
                <div class="mt-2">
                    <small class="text-muted">
                        <i class="fas fa-shield-alt text-success"></i>
                        {{ translate('Secure checkout with multiple payment options') }}
                    </small>
                </div>
            @endif
        </div>
        <hr>
    @endif

    <!-- Share -->
    <div class="row no-gutters mt-4">
        <div class="col-sm-2">
            <div class="text-secondary fs-14 fw-400 mt-2">{{ translate('Share') }}</div>
        </div>
        <div class="col-sm-10">
            <div class="aiz-share"></div>
        </div>
    </div>
</div>

<style>
    .price-tier-item {
        background-color: #f8f9fa;
        color: #333;
        border: 2px solid #e9ecef;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .price-tier-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .price-tier-item.active {
        background-color: #3D52A0;
        color: white;
        border-color: #3D52A0;
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(61, 82, 160, 0.3);
    }

    .color-option {
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .color-option:hover {
        border-color: #3D52A0 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .selected-color {
        border-color: #3D52A0 !important;
        border-width: 2px !important;
        box-shadow: 0 4px 8px rgba(61, 82, 160, 0.2);
    }

    .size-table-container::-webkit-scrollbar {
        width: 8px;
    }

    .size-table-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .size-table-container::-webkit-scrollbar-thumb {
        background: #3D52A0;
        border-radius: 4px;
    }

    .size-table-container::-webkit-scrollbar-thumb:hover {
        background: #2a3d7a;
    }

    .sticky-top {
        position: sticky;
        top: 0;
        z-index: 10;
        background: #f8f9fa !important;
    }

    .quantity-control {
        display: none !important;
    }

    .quantity-control.active {
        display: flex !important;
    }

    .add-btn {
        display: block !important;
        transition: all 0.3s ease;
    }

    .add-btn:hover {
        background: #2a3d7a !important;
        transform: translateY(-1px);
    }

    .add-btn.hidden {
        display: none !important;
    }

    .price-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-left: 4px solid #3D52A0;
    }

    .btn {
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .gap-3 {
        gap: 1rem;
    }
</style>

<script type="text/javascript">
    const PRODUCT_ID = {{ $detailedProduct->id }};
    const LOCAL_STORAGE_KEY = 'cart_state_' + PRODUCT_ID;
    const GLOBAL_DISCOUNT_PERCENT = {{ $product_discount_rate }};
    const GLOBAL_TAX_PERCENT = {{ $product_tax_rate }};
    const PRICE_TIERS = {!! $price_tiers_json !!};

    // ===== PREORDER DEBUGGING UTILITIES =====
    // Toggle to enable/disable verbose preorder debugging output
    window.DEBUG_PREORDER = true;

    function debugTimestamp() {
        try {
            return new Date().toISOString();
        } catch (e) {
            return '' + Date.now();
        }
    }

    function debugLog(marker, payload) {
        if (!window.DEBUG_PREORDER) return;
        const entry = {
            ts: debugTimestamp(),
            path: marker,
            ...payload
        };
        try {
            console.log('[PREORDER DEBUG] ' + JSON.stringify(entry));
        } catch (e) {
            console.log('[PREORDER DEBUG]', entry);
        }
    }

    function debugAlert(marker, payload) {
        if (!window.DEBUG_PREORDER) return;
        const entry = {
            ts: debugTimestamp(),
            path: marker,
            ...payload
        };
        try {
            alert('[PREORDER DEBUG] ' + JSON.stringify(entry));
        } catch (e) {
            // no-op
        }
    }

    function saveCartState() {
        const selectedItems = extractSelectedItems();
        const stateToSave = {
            items: selectedItems
        };
        if (selectedItems.length > 0) {
            localStorage.setItem(LOCAL_STORAGE_KEY, JSON.stringify(stateToSave));
        } else {
            localStorage.removeItem(LOCAL_STORAGE_KEY);
        }
    }

    function loadCartState() {
        const savedState = localStorage.getItem(LOCAL_STORAGE_KEY);
        if (!savedState) return;
        try {
            const state = JSON.parse(savedState);
            if (Array.isArray(state.items) && state.items.length > 0) {
                state.items.forEach(item => {
                    const row = $(`tr[data-size="${item.size}"]`);
                    if (row.length && item.quantity > 0) {
                        row.find('.add-btn').addClass('hidden');
                        row.find('.quantity-control').addClass('active');
                        row.find('.quantity-input').val(item.quantity);
                    }
                });
            }
            updateGrandTotal();
        } catch (e) {
            console.error("Failed to load cart state:", e);
            localStorage.removeItem(LOCAL_STORAGE_KEY);
        }
    }

    function selectPriceTier(element) {
        const minQty = parseInt($(element).data('min-qty'));
        let totalQuantity = 0;
        $('#sizeTable .quantity-input').each(function() {
            if ($(this).closest('.quantity-control').hasClass('active')) {
                totalQuantity += parseInt($(this).val());
            }
        });

        if (totalQuantity < minQty) {
            const neededQty = minQty - totalQuantity;
            let targetRow = $('#sizeTable tbody tr').filter(function() {
                return $(this).find('.quantity-control').hasClass('active');
            }).first();

            if (targetRow.length === 0) {
                targetRow = $('#sizeTable tbody tr').first();
                const addBtn = targetRow.find('.add-btn');
                if (addBtn.length) addToCartRow(addBtn[0], 0);
            }

            const input = targetRow.find('.quantity-input');
            const currentVal = parseInt(input.val());
            const maxStock = parseInt(targetRow.data('stock-qty'));
            const newQty = Math.min(currentVal + neededQty, maxStock);
            input.val(newQty);
        }
        updateGrandTotal();
    }

    // আপনার <script> ট্যাগের ভেতরে এই ফাংশনটি প্রতিস্থাপন করুন

  function updateGrandTotal() {
        let totalQuantity = 0;
        $('#sizeTable tbody tr .quantity-input').each(function() {
            if ($(this).closest('.quantity-control').hasClass('active')) {
                totalQuantity += parseInt($(this).val()) || 0;
            }
        });

        let activeTierPrice = null;
        
        @if (Auth::check() && Auth::user()->user_type == 'wholesaler')
            let activeMinQty = 0;
            if (PRICE_TIERS.length > 0) {
                for (const tier of PRICE_TIERS) {
                    if (totalQuantity >= tier.min_qty) {
                        activeTierPrice = tier.price;
                        activeMinQty = tier.min_qty;
                        break;
                    }
                }
            }
            $('#price-tier-options .price-tier-item').removeClass('active');
            if (activeMinQty > 0) {
                $(`#price-tier-options .price-tier-item[data-min-qty="${activeMinQty}"]`).addClass('active');
            }
        @endif

        $('#sizeTable tbody tr').each(function() {
            const row = $(this);
            const quantity = parseInt(row.find('.quantity-input').val()) || 0;
            const originalPrice = parseFloat(row.data('original-price'));
            const discountedPrice = parseFloat(row.data('discounted-price'));
            
            // --- আপনার ৩টি শর্ত অনুযায়ী চূড়ান্ত Unit Price নির্ধারণ ---
            let unitPrice = originalPrice; // শর্ত ১: ডিফল্ট দাম

            // শর্ত ২: যদি ডিসকাউন্ট থাকে, তাহলে unitPrice হবে ডিসকাউন্ট দাম
            if (discountedPrice > 0 && discountedPrice < originalPrice) {
                unitPrice = discountedPrice;
            }

            // শর্ত ৩: যদি হোলসেলার হন এবং হোলসেল দাম প্রযোজ্য হয়, তাহলে সেটিই হবে চূড়ান্ত দাম
            @if (Auth::check() && Auth::user()->user_type == 'wholesaler')
                let variantWholesalePrice = null;
                if (quantity > 0) {
                    const wholesaleTiersVariant = row.find('.wholesale-tiers-variant .tier-item-variant');
                    wholesaleTiersVariant.each(function() {
                        const tierMinQty = parseInt($(this).data('min-qty'));
                        const tierMaxQty = parseInt($(this).data('max-qty')) || Infinity;
                        if (quantity >= tierMinQty && quantity <= tierMaxQty) {
                            variantWholesalePrice = parseFloat($(this).data('price'));
                            return false;
                        }
                    });
                }
                if (variantWholesalePrice !== null) {
                    unitPrice = variantWholesalePrice;
                } else if (activeTierPrice !== null) {
                    unitPrice = activeTierPrice;
                }
            @endif
            // --- দাম নির্ধারণের লজিক শেষ ---

            // UI আপডেট
            let unitPriceHtml = `৳${unitPrice.toFixed(2)}`;
            if (unitPrice < originalPrice) {
                unitPriceHtml = `<div><span class="text-primary fw-bold">৳${unitPrice.toFixed(2)}</span><br><del class="text-muted">৳${originalPrice.toFixed(2)}</del></div>`;
            }
            row.find('.unit-price').html(unitPriceHtml);

            const totalPrice = (quantity > 0) ? (unitPrice * quantity) : 0;
            row.find('.total-price').text('৳' + totalPrice.toFixed(2));
        });
        
        saveCartState();
    }

    function calculateEffectiveUnitPrice(basePrice) {
        if (basePrice <= 0) return 0;
        const unitDiscounted = basePrice - (basePrice * GLOBAL_DISCOUNT_PERCENT / 100);
        return unitDiscounted + (unitDiscounted * GLOBAL_TAX_PERCENT / 100);
    }

    function addToCartRow(button, initialQty = 1) {
        const row = $(button).closest('tr');
        const stockQty = parseInt(row.data('stock-qty'));
        const isRowPreorder = row.data('is-preorder') === true || row.data('is-preorder') === 'true';

        // Gather product variant selection state
        const variantId = row.data('size');
        const stockId = row.data('stock-id');
        const originalPrice = parseFloat(row.data('original-price'));
        const discountedPrice = parseFloat(row.data('discounted-price'));
        const selectedColor = $('input[name="color"]:checked').val() || ($('#selected-color-name').text() || null);
        const hasAddButton = row.find('.add-btn').length > 0;
        const hasQuantityControl = row.find('.quantity-control').length > 0;

        debugLog('addToCartRow:start', {
            variantId,
            stockId,
            stockQty,
            isRowPreorder,
            originalPrice,
            discountedPrice,
            selectedColor,
            hasAddButton,
            hasQuantityControl
        });

        // If stock is 0 and preorder is not available, prevent adding
        debugLog('addToCartRow:check_out_of_stock_branch', {
            condition: (stockQty === 0 && !isRowPreorder)
        });
        if (stockQty === 0 && !isRowPreorder) {
            debugLog('addToCartRow:out_of_stock_branch', {
                reason: 'stockQty===0 && !isRowPreorder',
                stockQty,
                isRowPreorder
            });
            debugAlert('addToCartRow:alert_out_of_stock', {
                variantId,
                stockId,
                stockQty,
                isRowPreorder
            });
            AIZ.plugins.notify('warning', '{{ translate('This product is out of stock') }}');
            return;
        }

        debugLog('addToCartRow:activate_quantity_controls', {
            initialQty
        });
        $(button).addClass('hidden');
        row.find('.quantity-control').css('display', 'flex').addClass('active');
        row.find('.quantity-input').val(initialQty);
        updateGrandTotal();
        debugLog('addToCartRow:end', {
            active: row.find('.quantity-control').hasClass('active'),
            currentQty: parseInt(row.find('.quantity-input').val())
        });
    }

    function increaseQuantity(button) {
        const row = $(button).closest('tr');
        const input = $(button).siblings('.quantity-input');
        const maxQty = parseInt(row.data('stock-qty'));
        const isRowPreorder = row.data('is-preorder') === true || row.data('is-preorder') === 'true';
        const currentVal = parseInt(input.val()) || 0;

        // For preorder items, no quantity limit
        if (isRowPreorder) {
            input.val(currentVal + 1);
            updateGrandTotal();
        } else if (currentVal < maxQty) {
            input.val(currentVal + 1);
            updateGrandTotal();
        } else {
            // For regular items only, show stock cap warning
            AIZ.plugins.notify('warning', '{{ translate('Maximum stock limit reached') }}');
        }
    }

    function decreaseQuantity(button) {
        const row = $(button).closest('tr');
        const input = $(button).siblings('.quantity-input');
        const isRowPreorder = row.data('is-preorder') === true || row.data('is-preorder') === 'true';

        if (parseInt(input.val()) > 1) {
            input.val(parseInt(input.val()) - 1);
        } else {
            // For preorder items, don't hide the quantity controls when reaching 0
            if (!isRowPreorder) {
                $(button).closest('.quantity-control').css('display', 'none').removeClass('active');
                $(button).closest('tr').find('.add-btn').removeClass('hidden');
                input.val(0);
            } else {
                // For preorder items, minimum quantity is 1
                input.val(1);
            }
        }
        updateGrandTotal();
    }

function extractSelectedItems() {
    const selectedItems = [];
    $('#sizeTable tbody tr').each(function() {
        const row = $(this);
        if (row.find('.quantity-control').hasClass('active')) {
            const quantity = parseInt(row.find('.quantity-input').val()) || 0;
            if (quantity > 0) {
                const unitPriceText = row.find('.unit-price').text().replace(/[^0-9.]/g, '');
                const unitPrice = parseFloat(unitPriceText);

                selectedItems.push({
                    size: row.data('size'),
                    quantity: quantity,
                    is_preorder: row.data('is-preorder') === true,
                    variant_name: row.data('size'),
                    stock_id: row.data('stock-id'),
                    unit_price: unitPrice // *** এখানে আপডেট হওয়া সঠিক দামটি পাঠানো হচ্ছে ***
                });
            }
        }
    });
    return selectedItems;
}

    function setHiddenSelectedItems(items) {
        if (!$('#option-choice-form').find('input[name="selected_items"]').length) {
            $('#option-choice-form').append('<input type="hidden" name="selected_items">');
        }
        $('#option-choice-form').find('input[name="selected_items"]').val(JSON.stringify(items));
    }

    function addToCartFromTable() {
        const selectedItems = extractSelectedItems();
        if (selectedItems.length === 0) {
            AIZ.plugins.notify('warning', '{{ translate('Please select at least one item') }}');
            return;
        }
        setHiddenSelectedItems(selectedItems);
        addToCart();
    }


// details.blade.php

function buyNowFromTable(isPreorder = false) {
    const selectedItems = extractSelectedItems();
    if (selectedItems.length === 0) {
        AIZ.plugins.notify('warning', '{{ translate('Please select at least one item') }}');
        return;
    }

    let hasPreorderItem = isPreorder || selectedItems.some(item => item.is_preorder);

    if (hasPreorderItem) {
        // --- Pre-order Logic (এটি অপরিবর্তিত আছে) ---
        const preorderForm = document.createElement('form');
        preorderForm.method = 'POST';
        preorderForm.action = '{{ route('preorder.direct_checkout') }}';
        // ... (বাকি প্রি-অর্ডার কোড)
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        preorderForm.appendChild(csrfInput);
        const productIdInput = document.createElement('input');
        productIdInput.type = 'hidden';
        productIdInput.name = 'product_id';
        productIdInput.value = {{ $detailedProduct->id }};
        preorderForm.appendChild(productIdInput);
        const selectedItemsInput = document.createElement('input');
        selectedItemsInput.type = 'hidden';
        selectedItemsInput.name = 'selected_items';
        selectedItemsInput.value = JSON.stringify(selectedItems);
        preorderForm.appendChild(selectedItemsInput);
        document.body.appendChild(preorderForm);
        preorderForm.submit();

    } else {
        // --- Regular "Buy Now" Logic (এখানে প্রধান পরিবর্তন করা হয়েছে) ---
        
        // ধাপ ১: প্রথমে কার্ট খালি করার জন্য AJAX রিকোয়েস্ট পাঠানো হচ্ছে
        $.ajax({
            type: "POST",
            url: '{{ route('cart.clearForBuyNow') }}',
            data: { _token: '{{ csrf_token() }}' },
            success: function(clearResponse) {
                if (clearResponse.status == 1) {
                    
                    // ধাপ ২: কার্ট খালি হওয়ার পর, নতুন আইটেম যোগ করার জন্য দ্বিতীয় AJAX রিকোয়েস্ট পাঠানো হচ্ছে
                    setHiddenSelectedItems(selectedItems);
                    const form = document.getElementById('option-choice-form');
                    const formData = new FormData(form);
                    
                    $.ajax({
                        type: "POST",
                        url: '{{ route('cart.addToCart') }}',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(addResponse) {
                            if (addResponse.status == 1) {
                                // সফলভাবে যোগ হওয়ার পর চেকআউট পেজে রিডাইরেক্ট
                                window.location.href = "{{ route('checkout') }}";
                            } else {
                                AIZ.plugins.notify('danger', addResponse.message || "{{ translate('Something went wrong') }}");
                            }
                        },
                        error: function() {
                            AIZ.plugins.notify('danger', "{{ translate('Failed to add items to cart.') }}");
                        }
                    });

                } else {
                     AIZ.plugins.notify('danger', clearResponse.message || "{{ translate('Could not clear cart.') }}");
                }
            },
            error: function() {
                AIZ.plugins.notify('danger', "{{ translate('An error occurred while preparing your order.') }}");
            }
        });
    }
}



   
// function buyNowFromTable(isPreorder = false) {
//     const selectedItems = extractSelectedItems();
//     if (selectedItems.length === 0) {
//         AIZ.plugins.notify('warning', '{{ translate('Please select at least one item') }}');
//         return;
//     }

//     let hasPreorderItem = isPreorder || selectedItems.some(item => item.is_preorder);

//     if (hasPreorderItem) {
//         // --- Pre-order Logic ---
//         const preorderForm = document.createElement('form');
//         preorderForm.method = 'POST';
//         preorderForm.action = '{{ route('preorder.direct_checkout') }}';

//         const csrfInput = document.createElement('input');
//         csrfInput.type = 'hidden';
//         csrfInput.name = '_token';
//         csrfInput.value = '{{ csrf_token() }}';
//         preorderForm.appendChild(csrfInput);

//         const productIdInput = document.createElement('input');
//         productIdInput.type = 'hidden';
//         productIdInput.name = 'product_id';
//         productIdInput.value = {{ $detailedProduct->id }};
//         preorderForm.appendChild(productIdInput);

//         const selectedItemsInput = document.createElement('input');
//         selectedItemsInput.type = 'hidden';
//         selectedItemsInput.name = 'selected_items';
//         selectedItemsInput.value = JSON.stringify(selectedItems);
//         preorderForm.appendChild(selectedItemsInput);

//         document.body.appendChild(preorderForm);
//         preorderForm.submit();

//     } else {
       
//         setHiddenSelectedItems(selectedItems); // This is from your existing code
//         const form = document.getElementById('option-choice-form');
//         const formData = new FormData(form);
//         formData.append('buy_now', '1');

//         $.ajax({
//             type: "POST",
//             url: '{{ route('cart.addToCart') }}',
//             data: formData,
//             processData: false,
//             contentType: false,
//             success: function(data) {
//                 if (data.status == 1) {
//                     // সফলভাবে কার্টে যোগ হওয়ার পর চেকআউট পেজে রিডাইরেক্ট
//                     window.location.href = "{{ route('checkout') }}";
//                 } else {
//                     AIZ.plugins.notify('danger', data.message || "{{ translate('Something went wrong') }}");
//                 }
//             },
//             error: function() {
//                 AIZ.plugins.notify('danger', "{{ translate('Something went wrong') }}");
//             }
//         });
//     }
// }

    $(document).ready(function() {
        loadCartState();
        updateGrandTotal();

        // Button rendering logic flow per row
        $('#sizeTable tbody tr').each(function(index) {
            const row = $(this);
            const stockQty = parseInt(row.data('stock-qty')) || 0;
            const isRowPreorder = row.data('is-preorder') === true || row.data('is-preorder') === 'true';
            const hasAddButton = row.find('.add-btn').length > 0;
            const hasQuantityControl = row.find('.quantity-control').length > 0;
            const renderAddBtnExpected = (stockQty > 0) || isRowPreorder; // mirrors blade condition
            debugLog('document_ready:row_state', {
                index,
                stockQty,
                isRowPreorder,
                hasAddButton,
                hasQuantityControl,
                renderAddBtnExpected
            });
        });

        // Handle manual quantity input changes for pre-order products
        $('.quantity-input').on('input', function() {
            const input = $(this);
            let value = parseInt(input.val()) || 0;
            const row = input.closest('tr');
            const isRowPreorder = row.data('is-preorder') === true || row.data('is-preorder') === 'true';
            const stockQty = parseInt(row.data('stock-qty')) || 0;

            if (isRowPreorder) {
                // Only enforce minimum of 1 for preorder items
                if (value < 1) value = 1;
                input.val(value);
            } else {
                // Regular items: cap to available stock
                if (value > stockQty) {
                    input.val(stockQty);
                    AIZ.plugins.notify('warning',
                        '{{ translate('Cannot exceed available stock quantity') }}: ' + stockQty);
                }
                if (value < 0) input.val(0);
            }

            // Update grand total after validation
            updateGrandTotal();
            debugLog('quantity_input:change', {
                isRowPreorder,
                stockQty,
                value: parseInt(input.val())
            });
        });
    });
</script>

<script>
    function selectColor(element, colorName, colorValue) {
        document.querySelectorAll('#color-options .color-option').forEach(option => {
            option.classList.remove('selected-color');
            option.style.borderWidth = '1px';
            option.style.borderColor = '#ddd';
        });
        element.classList.add('selected-color');
        // Show full variant label on UI (e.g., Black-Xiaomi14Pro)
        document.getElementById('selected-color-name').textContent = colorName;
        // But select the base color radio for price/stock logic
        const baseColorName = (element.getAttribute('data-base-color-name') || (colorName ? colorName.split('-')[0] :
            ''));
        if (baseColorName) {
            $('input[name="color"][value="' + baseColorName + '"]').prop('checked', true);
        }
        if (typeof getVariantPrice === 'function') {
            getVariantPrice();
        }
    }

    // Prevent accidental form submission that could cause GET requests
    document.addEventListener('DOMContentLoaded', function() {
        const optionForm = document.getElementById('option-choice-form');
        if (optionForm) {
            optionForm.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Form submission prevented - use AJAX instead');
                return false;
            });
        }
    });
</script>

