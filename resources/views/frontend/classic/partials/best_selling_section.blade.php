{{-- @php
    $best_selling_products = get_best_selling_products(20);
@endphp
@if (get_setting('best_selling') == 1 && count($best_selling_products) > 0)
    <section class="mb-2 mb-md-3 mt-2 mt-md-3">
        <div class="container">
            <!-- Top Section -->
            <div class="d-flex mb-2 mb-md-3 align-items-baseline justify-content-between">
                <!-- Title -->
                <h3 class="fs-16 fs-md-20 fw-700 mb-2 mb-sm-0">
                    <span class="">{{ translate('Best Selling') }}</span>
                </h3>
                <!-- Links -->
                <div class="d-flex">
                    <a type="button" class="arrow-prev slide-arrow link-disable text-secondary mr-2" onclick="clickToSlide('slick-prev','section_best_selling')"><i class="las la-angle-left fs-20 fw-600"></i></a>
                    <a type="button" class="arrow-next slide-arrow text-secondary ml-2" onclick="clickToSlide('slick-next','section_best_selling')"><i class="las la-angle-right fs-20 fw-600"></i></a>
                </div>
            </div>
            <!-- Product Section -->
            <div class="px-sm-3">
                <div class="aiz-carousel sm-gutters-16 arrow-none" data-items="6" data-xl-items="5" data-lg-items="4"  data-md-items="3" data-sm-items="2.5" data-xs-items="2.5" data-arrows='true' data-infinite='false'>
                    @foreach ($best_selling_products as $key => $product)
                        <div class="carousel-box px-3 position-relative has-transition hov-animate-outline border-right border-top border-bottom @if($key == 0) border-left @endif">
                            @include('frontend.'.get_setting('homepage_select').'.partials.product_box_1',['product' => $product])
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif --}}
@php
  $best_selling_products = get_best_selling_products(12);
@endphp

@if (get_setting('best_selling') == 1 && count($best_selling_products) > 0)
<section class="products-section">
  <div class="container-full">
    <div class="section-header">
      <h2 class="section-title" style="color: var(--skybuy-blue);">{{ translate('Best Selling') }}</h2>
      <a href="{{ route('search', ['best_selling' => 1]) }}" class="view-more-btn">
        {{ translate('View More') }}
      </a>
    </div>

    <div class="products-grid">
      @foreach ($best_selling_products as $product)
        @php
          $name  = $product->getTranslation('name');
          $url   = route('product', $product->slug);
          $thumb = uploaded_asset($product->thumbnail_img) ?: static_asset('assets/img/placeholder.jpg');

          $current  = home_discounted_base_price($product);
          $original = home_base_price($product);

          $discPct = null;
          try {
            $c = (float) preg_replace('/[^\d.]/', '', $current);
            $o = (float) preg_replace('/[^\d.]/', '', $original);
            if ($o > 0 && $c < $o) $discPct = round((($o - $c) / $o) * 100);
          } catch (\Throwable $e) {}

          // Calculate sold and available quantities
          $sold_qty = 0;
          $available_qty = 0;
          
          if ($product->variant_product) {
              // For variant products, sum up all variants
              foreach ($product->stocks as $stock) {
                  $sold_qty += $stock->qty_sold ?? 0;
                  $available_qty += $stock->qty ?? 0;
              }
          } else {
              // For simple products
              $sold_qty = $product->num_of_sale ?? 0;
              $available_qty = $product->current_stock ?? 0;
          }
        @endphp

        <a href="{{ $url }}" class="product-card" title="{{ $name }}">
          <div class="product-image">
            <img
              src="{{ static_asset('assets/img/placeholder.jpg') }}"
              data-src="{{ $thumb }}"
              class="lazyload"
              alt="{{ $name }}">
            @if(!is_null($discPct) && $discPct > 0)
              <div class="discount-badge">{{ $discPct }}% {{ translate('Off') }}</div>
            @endif
          </div>

          <div class="product-name">{{ $name }}</div>

          <div class="product-price">
            <span class="current-price">{{ $current }}</span>
            @if($current !== $original)
              <span class="original-price">{{ $original }}</span>
            @endif
          </div>

          <div class="product-meta">
            <span class="sold-count">
                                                <i class="fas fa-shopping-cart"></i>
                                                {{ translate('SOLD') }}: {{ $sold_qty }}
                                            </span>
            <span class="available-count"><i class="fas fa-box"></i> {{ translate('AVAILABLE') }}: {{ $available_qty }}</span>
          </div>
        </a>
      @endforeach
    </div>
  </div>
</section>
@endif

<style>
/* Your existing CSS code here (no changes needed) */
    .products-section{ background:#fff; border-radius:15px; padding:30px; margin-top:20px; box-shadow:0 4px 15px rgba(0,0,0,.1); }
.section-header{ display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; padding-bottom:12px; border-bottom:2px solid #e5e7eb; }
.section-title{ font-size:22px; font-weight:800; color:var(--skybuy-blue); margin:0; }
.view-more-btn{ background:var(--skybuy-blue); color:#fff; padding:10px 16px; border-radius:8px; text-decoration:none; }
.view-more-btn:hover{ background:#1a4a54; }

.products-grid{
  display:grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); /* responsive */ /* [web:85][web:97] */
  gap:20px;
}

.product-card{ background:#f9f9f9; border-radius:12px; color:inherit; text-decoration:none; transition:transform .2s, box-shadow .2s; overflow:hidden; display:block; }
.product-card:hover{ transform:translateY(-4px); box-shadow:0 10px 25px rgba(0,0,0,.12); }

.product-image{ position:relative; width:100%; height:210px; background:#f2f2f2; overflow:hidden; }
.product-image img{ width:100%; height:100%; object-fit:cover; } /* tidy crop */ /* [web:97] */
.discount-badge{ position:absolute; top:10px; right:10px; background:#ff4757; color:#fff; padding:4px 8px; border-radius:14px; font-size:12px; font-weight:700; }

.product-name{
  font-size:14px; font-weight:600; line-height:1.35; margin:10px 0 8px; padding:0 12px;
  display:-webkit-box; -webkit-box-orient:vertical; -webkit-line-clamp:2; overflow:hidden;
}
.product-price{ display:flex; align-items:center; gap:8px; padding:0 12px 8px; }
.current-price{ color:#e74c3c; font-weight:800; }
.original-price{ color:#95a5a6; text-decoration:line-through; }
.product-meta{ display:flex; justify-content:space-between; color:#64748b; font-size:12px; padding:0 12px 14px; }
.product-meta .sold-count{ color:#16a34a; font-weight:bold; }
.product-meta .available-count{ color:#3498db; font-weight:bold; }

</style>

