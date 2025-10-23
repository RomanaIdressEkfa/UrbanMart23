@php
    $cart_added = [];
@endphp
{{-- `d-flex flex-column` এবং `pb-3` ক্লাস যোগ করা হয়েছে `aiz-card-box` এ --}}
<div class="aiz-card-box h-auto bg-white hov-scale-img d-flex flex-column pb-3"> 
    {{-- ছবির কন্টেইনার। `h-100px h-md-200px` উচ্চতা ফিক্স করা আছে --}}
    <div class="position-relative h-100px h-md-200px overflow-hidden">
        @php
            $product_url = route('product', $product->slug);
            if ($product->auction_product == 1) {
                $product_url = route('auction-product', $product->slug);
            }
        @endphp
        <!-- Image -->
        <a href="{{ $product_url }}" class="d-block h-100">
            {{-- `mx-auto` সরানো হয়েছে img ট্যাগ থেকে। `img-fit` ক্লাসটি css এ `object-fit: cover` নিশ্চিত করবে --}}
            <img class="lazyload img-fit has-transition" src="{{ get_image($product->thumbnail) }}"
                alt="{{ $product->getTranslation('name') }}" title="{{ $product->getTranslation('name') }}"
                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
        </a>
        <!-- Discount percentage tag -->
        @if (discount_in_percentage($product) > 0)
            <span class="absolute-top-left bg-primary ml-1 mt-1 fs-11 fw-700 text-white w-35px text-center"
                style="padding-top:2px;padding-bottom:2px;">-{{ discount_in_percentage($product) }}%</span>
        @endif
        <!-- Wholesale tag -->
        @if ($product->wholesale_product)
            <span class="absolute-top-left fs-11 text-white fw-700 px-2 lh-1-8 ml-1 mt-1"
                style="background-color: #455a64; @if (discount_in_percentage($product) > 0) top:25px; @endif">
                {{ translate('Wholesale') }}
            </span>
        @endif

        @if ($product->auction_product == 0)
            {{-- Desktop Icons (Top Right) -- এই অংশটি কমেন্ট করা আছে --}}
            {{-- Mobile Icons (Bottom) -- এই অংশটি এখন ছবির নিচে আর থাকবে না, তাই এটি সরিয়ে দিন বা কমেন্ট করে রাখুন --}}
            {{-- Original Add to Cart (Desktop only) -- এই অংশটি এখান থেকে সরানো হয়েছে --}}
        @endif

        {{-- Original Place Bid button (auction products) -- এই অংশটিও এখান থেকে সরানো হয়েছে --}}
    </div>

    {{-- পণ্যের নাম, দাম এবং নতুন Add to Cart বাটন এই div-এর মধ্যে থাকবে --}}
    {{-- `p-3` যোগ করা হয়েছে যাতে ভেতরের কন্টেন্টের চারপাশে পর্যাপ্ত প্যাডিং থাকে --}}
    <div class="p-3 text-left d-flex flex-column flex-grow-1">
        <!-- Product name -->
        <h3 class="fw-400 fs-13 text-truncate-2 lh-1-4 mb-2 h-35px "> {{-- mb-2 যোগ করা হয়েছে --}}
            <a class="product-name" style="margin-left: -12px;" href="{{ $product_url }}" class="d-block text-reset hov-text-primary"
                title="{{ $product->getTranslation('name') }}">{{ $product->getTranslation('name') }}</a>
        </h3>
        <style>
       .product-name{
             font-size: 14px;
    font-weight: 600;
    margin-bottom: 8px;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    padding: 0 15px;
       }
        </style>
        <div class="fs-14 d-flex mb-auto product-price"> {{-- mt-3 থেকে mb-auto তে পরিবর্তন --}}
            @if ($product->auction_product == 0)
                <!-- Previous price -->
                @if (home_base_price($product) != home_discounted_base_price($product))
                    <div class="disc-amount has-transition ">
                        <del class="original-price fw-400 text-secondary mr-1">{{ home_base_price($product) }}</del>
                    </div>
                @endif
                <!-- price -->
                <div class="" style="margin-left: 25px;">
                    <span class="current-price text-primary">{{ home_discounted_base_price($product) }}</span>
                </div>
            @endif
             
            @if ($product->auction_product == 1)
                <!-- Bid Amount -->
                <div class="">
                    <span class="fw-700 text-primary">{{ single_price($product->starting_bid) }}</span>
                </div>
            @endif
        </div>
         <div class="sold-info text-secondary fs-12 mt-1"> 
                {{-- 'SOLD:' এর সাথে $product->num_of_sale যোগ করুন --}}
                SOLD: {{ $product->num_of_sale ?? 0 }} 
                @if ($product->auction_product == 0 && $product->stock_visibility_state == 'quantity' && isset($product->stocks) && count($product->stocks) > 0)
                    {{-- যদি স্টক দেখাতে চান --}}
                    | available: {{ $product->stocks->sum('qty') }} 
                @endif
            </div>
        <style>
            .products-section .product-price {
            display: flex
        ;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
            padding: 0 15px;
        }
        .current-price {
            color: #e74c3c!important;
            font-weight: 800!important;
        }
        .original-price {
            color: #95a5a6;
            text-decoration: line-through;
        }
        </style>

        {{-- Add to Cart / Place Bid বাটনটি এখানে সরানো হয়েছে এবং mt-3 ব্যবহার করা হয়েছে যাতে এটি দাম থেকে কিছুটা নিচে থাকে --}}
        {{-- <div class="mt-3">
            @if ($product->auction_product == 0)
                <button type="button" class="btn btn-block add-to-cart-product-box"
                    onclick="showAddToCartModal({{ $product->id }})">
                    {{ translate('Add to Cart') }}
                </button>
            @endif

            @if (
                $product->auction_product == 1 &&
                $product->auction_start_date <= strtotime('now') &&
                $product->auction_end_date >= strtotime('now'))
                @php
                    $highest_bid = $product->bids->max('amount');
                    $min_bid_amount = $highest_bid != null ? $highest_bid + 1 : $product->starting_bid;
                @endphp
                <button type="button" class="btn btn-block add-to-cart-product-box"
                    onclick="bid_single_modal({{ $product->id }}, {{ $min_bid_amount }})">
                    {{ translate('Place Bid') }}
                </button>
            @endif
        </div> --}}
    </div>
</div>

