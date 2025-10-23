{{-- Mohammad Hassan --}}
@php
    $availability = "out of stock";
    $qty = 0;
    if($product->variant_product) {
        foreach ($product->stocks as $key => $stock) {
            $qty += $stock->qty;
        }
    }
    else {
        $qty = optional($product->stocks->first())->qty;
    }
    if($qty > 0){
        $availability = "in stock";
    }
@endphp

<!-- Schema.org markup for Google+ -->
<meta itemprop="name" content="{{ $product->meta_title }}">
<meta itemprop="description" content="{{ $product->meta_description }}">
<meta itemprop="image" content="{{ uploaded_asset($product->meta_img) }}">

<!-- Twitter Card data -->
<meta name="twitter:card" content="product">
<meta name="twitter:site" content="@publisher_handle">
<meta name="twitter:title" content="{{ $product->meta_title }}">
<meta name="twitter:description" content="{{ $product->meta_description }}">
<meta name="twitter:creator" content="@author_handle">
<meta name="twitter:image" content="{{ uploaded_asset($product->meta_img) }}">
<meta name="twitter:data1" content="{{ single_price($product->unit_price) }}">
<meta name="twitter:label1" content="Price">

<!-- Open Graph data -->
<meta property="og:title" content="{{ $product->meta_title }}" />
<meta property="og:type" content="og:product" />
<meta property="og:url" content="{{ route('product', $product->slug) }}" />
<meta property="og:image" content="{{ uploaded_asset($product->meta_img) }}" />
<meta property="og:description" content="{{ $product->meta_description }}" />
<meta property="og:site_name" content="{{ get_setting('meta_title') }}" />
<meta property="og:price:amount" content="{{ single_price($product->unit_price) }}" />
<meta property="product:brand" content="{{ $product->brand ? $product->brand->name : env('APP_NAME') }}">
<meta property="product:availability" content="{{ $availability }}">
<meta property="product:condition" content="new">
<meta property="product:price:amount" content="{{ number_format($product->unit_price, 2) }}">
<meta property="product:retailer_item_id" content="{{ $product->slug }}">
<meta property="product:price:currency" content="{{ get_system_default_currency()->code }}" />
<meta property="fb:app_id" content="{{ env('FACEBOOK_PIXEL_ID') }}">

