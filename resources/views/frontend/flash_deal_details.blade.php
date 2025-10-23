@extends('frontend.layouts.app')

@section('content')
    <section class="flash-deal-details-page-section mb-5">
        <div class="container-full"> {{-- Using container-full for wider layout --}}

            @if($flash_deal->status == 1 && strtotime(date('Y-m-d H:i:s')) <= $flash_deal->end_date) 
                <!-- Flash Sale Header -->
                <div class="flash-page-header">
                    <h1 class="flash-page-title d-flex align-items-center">
                        <span class="flash-icon-spark">⚡</span> {{ translate('FLASH SALE') }}
                    </h1>
                    {{-- Countdown element with custom colon format, 'Ends in:' text removed --}}
                    {{-- <div class="flash-page-countdown custom-colon-countdown" 
                         end-date="{{ date('Y/m/d H:i:s', $flash_deal->end_date) }}">
                     
                    </div> --}}
                       <div class=" flash-page-countdown flash-countdown d-flex align-items-center">
                    <span class="mr-1 text-white">{{ translate('Ends in:') }}</span>
                    <div class="aiz-count-down-circle" style="background: none;" end-date="{{ date('Y/m/d H:i:s', $flash_deal->end_date) }}"></div>
                </div>
                </div>

                <!-- Products Grid -->
                <div class="flash-products-grid mt-4">
                        @foreach ($flash_deal->flash_deal_products as $key => $flash_deal_product)
                            @php
                                $product = get_single_product($flash_deal_product->product_id);
                            @endphp
                            @if ($product != null && $product->published != 0)
                                @php
                                    $product_url = route('product', $product->slug);
                                    if($product->auction_product == 1) {
                                        $product_url = route('auction-product', $product->slug);
                                    }
                                    $name = $product->getTranslation('name');
                                    $thumb = uploaded_asset($product->thumbnail_img) ?: static_asset('assets/img/placeholder.jpg');
                                    $current_price = home_discounted_base_price($product);
                                    $original_price = home_base_price($product);

                                    $discount_percentage = null;
                                    try {
                                        $c = (float) preg_replace('/[^\d.]/', '', $current_price);
                                        $o = (float) preg_replace('/[^\d.]/', '', $original_price);
                                        if ($o > 0 && $c < $o) $discount_percentage = round((($o - $c) / $o) * 100);
                                    } catch (\Throwable $e) {}

                                    $rating = $product->rating ?? rand(3,5); 
                                    $rating_count = $product->rating_count ?? rand(10,50); 
                                    $sold_quantity = (int)($product->num_of_sale ?? rand(5,30)); 
                                    $stock_quantity = (int)($product->current_stock ?? rand(10, 50)); 
                                    $available_quantity = max(0, $stock_quantity - $sold_quantity);

                                @endphp
                                <div class="flash-product-card">
                                        <div class="product-badge-wrapper">
                                            @if(!is_null($discount_percentage) && $discount_percentage > 0)
                                                <span class="product-discount-badge">-{{ $discount_percentage }}%</span>
                                            @endif
                                        </div>
                                        <a href="{{ $product_url }}" class="d-block text-reset">
                                            <div class="product-image-wrapper">
                                                <img src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                                     data-src="{{ $thumb }}"
                                                     class="lazyload flash-product-image"
                                                     alt="{{ $name }}">
                                            </div>
                                            <div class="product-details">
                                                <div class="rating rating-sm mt-1 mb-1">
                                                    @for ($i = 0; $i < 5; $i++)
                                                        @if ($i < $rating)
                                                            <i class="fas fa-star active"></i>
                                                        @else
                                                            <i class="fas fa-star"></i>
                                                        @endif
                                                    @endfor
                                                    <span class="rating-count">({{ $rating_count }})</span>
                                                </div>
                                                <div class="product-name fs-14 fw-600 mb-1">
                                                    {{ $name }}
                                                </div>
                                                <div class="product-price-display mb-1">
                                                    <span class="current-price fs-16 fw-700 text-primary">{{ $current_price }}</span>
                                                    @if($current_price !== $original_price)
                                                        <del class="old-price fs-13 fw-600 text-muted">{{ $original_price }}</del>
                                                    @endif
                                                </div>
                                                <div class="sold-available-info fs-12 text-muted">
                                                    <span>{{ translate('Sold') }}: {{ $sold_quantity }}</span> | 
                                                    <span>{{ translate('Available') }}: {{ $available_quantity }}</span>
                                                </div>
                                                {{-- Placeholder for SELECT OPTIONS button --}}
                                                <button type="button" class="btn btn-block btn-primary btn-sm mt-2 select-options-btn" style="background-color: #3D52A0;">
                                                    {{ translate('SELECT OPTIONS') }}
                                                </button>
                                            </div>
                                        </a>
                                    </div>
                            @endif
                        @endforeach
                </div>

            @else
                <div class="text-center text-dark py-5">
                    <h1 class="h3 my-4">{{ $flash_deal->title }}</h1>
                    <p class="h4">{{  translate('This offer has been expired.') }}</p>
                </div>
            @endif
        </div>
       
    </section>
     @include('frontend.inc.footer')
@endsection

@section('script')
    @parent {{-- Keep parent scripts --}}
    <script>
    </script>
@endsection


<style>
    /* General section styling */
    .flash-deal-details-page-section { /* Specific class for this page */
        background-color: #f8f8f8; /* Light background for the whole section */
        padding: 20px 0;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,.08);
    }

    .container-full {
        max-width: 1600px; /* Adjust as per your site's full width container */
        margin-left: auto;
        margin-right: auto;
        padding: 0 15px;
    }

    /* Flash Sale Header Styles */
    .flash-page-header { /* Specific class for the header of this page */
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 15px;
        border-bottom: 2px solid #eee;
        margin-bottom: 20px;
    }

    .flash-page-title {
        font-size: 24px;
        font-weight: 700;
        color: #333;
        margin: 0;
        display: flex;
        align-items: center;
    }

    .flash-icon-spark {
        font-size: 28px;
        margin-right: 8px;
        line-height: 1;
        color: #f7a000; /* Yellow spark icon */
    }

    /* Countdown Styling (now without "Ends in:" text) */
    .flash-page-countdown {
        background-color: #FF4D00; /* Changed to match screenshot red/brownish */
        color: white;
        padding: 0px 15px;
        border-radius: 5px;
        font-weight: 700;
        display: flex;
        align-items: center;
        margin-left: 20px; /* Space from title */
        flex-shrink: 0;
        /* min-width: 180px;  Adjust min-width to prevent squishing (no longer needed with `flex-grow: 0`) */
        flex-grow: 0; /* Prevent it from growing, fix width */
        justify-content: center; /* Center content within the box */
    }

    /* Styles for the individual countdown units within aiz-count-down-circle */
    .flash-page-countdown .aiz-count-down-circle.custom-colon-countdown {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0; /* Remove default gap */
        line-height: 1;
        font-size: 20px; /* Base font size for numbers */
        font-weight: 800; /* Bold numbers */
    }
    
    .flash-page-countdown .aiz-count-down-circle.custom-colon-countdown > div { /* Each unit (e.g., "10") */
        background: transparent !important;
        border: none !important;
        padding: 0 3px !important; /* Adjusted padding around numbers */
        display: flex;
        flex-direction: column; /* Stack number and label (though label is hidden) */
        align-items: center;
        justify-content: center;
        height: auto;
        width: auto;
        margin: 0 !important;
        position: relative;
    }

    .flash-page-countdown .aiz-count-down-circle.custom-colon-countdown > div > span:first-child { /* The number (e.g., 10) */
        color: white !important;
        font-size: inherit !important;
        font-weight: inherit !important;
        text-align: center;
        display: block;
    }

    .flash-page-countdown .aiz-count-down-circle.custom-colon-countdown > div > span:last-child { /* The label (e.g., DAYS) */
        display: none !important; /* Hide labels for compact colon format */
    }

    .flash-page-countdown .aiz-count-down-circle.custom-colon-countdown > div:not(:last-child)::after {
        content: ':';
        color: white;
        font-size: inherit;
        font-weight: inherit;
        margin: 0 2px; /* Space around colon */
    }

    /* Product Grid Styles */
    .flash-products-grid { /* This is the new grid container */
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); /* Responsive grid columns */
        gap: 16px; /* Gap between product cards */
        padding: 0 5px; /* Little padding inside the grid */
    }

    /* Individual Flash Product Card Styles */
    .flash-product-card {
        background-color: white;
        border: 1px solid #eee;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        transition: transform 0.2s, box-shadow 0.2s;
        height: 100%;
        display: flex;
        flex-direction: column;
        text-align: left;
        position: relative;
    }

    .flash-product-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .product-badge-wrapper {
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 5;
    }
    .product-discount-badge {
        background-color: #b70b0b;
        color: white;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 4px;
        display: inline-block;
    }

    .product-image-wrapper {
        height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background-color: #f9f9f9;
        border-bottom: 1px solid #eee;
    }
    .flash-product-image {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        transition: transform 0.3s ease;
    }
    .flash-product-card:hover .flash-product-image {
        transform: scale(1.05);
    }

    .product-details {
        padding: 10px 12px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .rating {
        margin-top: 5px;
        margin-bottom: 5px;
        line-height: 1;
    }
    .rating .fas.fa-star {
        color: #f7a000;
        font-size: 12px;
    }
    .rating .fas.fa-star:not(.active) {
        color: #ddd;
    }
    .rating-count {
        font-size: 12px;
        color: #888;
        margin-left: 5px;
        vertical-align: middle;
    }

    .product-name {
        font-size: 14px;
        font-weight: 600;
        line-height: 1.4;
        color: #333;
        min-height: 40px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 8px;
    }

    .product-price-display {
        display: flex;
        align-items: baseline;
        gap: 8px;
        margin-top: 5px;
        margin-bottom: 5px;
    }
    .current-price {
        font-size: 16px;
        font-weight: 700;
        color: #e44d26;
    }
    .old-price {
        font-size: 13px;
        font-weight: 500;
        color: #999;
        text-decoration: line-through;
    }

    .sold-available-info {
        font-size: 12px;
        color: #666;
        margin-bottom: 10px;
    }

    .select-options-btn {
        background-color: #4a69bd;
        border-color: #4a69bd;
        color: white;
        font-weight: 600;
        padding: 8px 10px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        text-transform: uppercase;
        width: 100%;
        text-align: center;
        display: block;
    }
    .select-options-btn:hover {
        background-color: #3c57a0;
        border-color: #3c57a0;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .flash-page-header {
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        .flash-page-title {
            font-size: 20px;
        }
        .flash-page-countdown {
            margin-left: 0;
            width: 100%;
            justify-content: center;
            padding: 6px 10px;
        }
        .flash-page-countdown .aiz-count-down-circle.custom-colon-countdown {
            font-size: 18px;
        }
        .flash-page-countdown .aiz-count-down-circle.custom-colon-countdown > div > span:first-child {
            font-size: inherit !important;
        }
        .flash-products-grid { /* Updated to target grid */
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); /* More compact grid on small screens */
            gap: 10px;
            padding: 0;
        }
    }
</style>

