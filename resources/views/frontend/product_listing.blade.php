@extends('frontend.layouts.app')

{{-- noUiSlider CSS - Place this in your main app layout's <head> or here --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.0/nouislider.min.css" integrity="sha512-kd6crnhech4kGLV/JSLIJx6Nwc02lD/QVPf8T3S/Hrqngg1gKjfmQnnqYVnEJ4ytwYgQyC1SsZkyh3nQxRy0Lw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

{{-- META TITLE & DESCRIPTION LOGIC --}}
{{-- Check for the $category object passed from controller --}}
@if (isset($category) && $category != null) 
    @php
        $meta_title = $category->meta_title ?? $category->getTranslation('name'); // Use category name if meta_title is null
        $meta_description = $category->meta_description ?? $category->getTranslation('name');
    @endphp
{{-- Check for brand object passed from controller --}}
@elseif (isset($brand) && $brand != null) 
    @php
        $meta_title = $brand->meta_title ?? ($brand->getTranslation('name') . ' Products');
        $meta_description = $brand->meta_description ?? ($brand->getTranslation('name') . ' Products');
    @endphp
@else
    @php
        $meta_title = get_setting('meta_title');
        $meta_description = get_setting('meta_description');
    @endphp
@endif

@section('meta_title'){{ $meta_title }}@stop
@section('meta_description'){{ $meta_description }}@stop

@section('meta')
    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ $meta_title }}">
    <meta itemprop="description" content="{{ $meta_description }}">

    <!-- Twitter Card data -->
    <meta name="twitter:title" content="{{ $meta_title }}">
    <meta name="twitter:description" content="{{ $meta_description }}">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $meta_title }}" />
    <meta property="og:description" content="{{ $meta_description }}" />
@endsection

@section('content')
<section class="product-listing-section">
    <div class="container-fluid">
        <form class="" id="search-form" action="" method="GET">
            <!-- Single Line Horizontal Filter -->
            <div class="filter-wrapper">
                <div class="container-fluid">
                    <div class="horizontal-filters">
                        
                        <!-- Price Range -->
                        <div class="filter-item price-filter">
                            <label class="filter-label">Price Range</label>
                            <div class="price-slider-container">
                                @php
                                    $product_count = get_products_count();
                                    $min_prod_price = ($product_count < 1) ? 0 : floor(get_product_min_unit_price());
                                    $max_prod_price = ($product_count < 1) ? 0 : ceil(get_product_max_unit_price());
                                    $currentMin = request('min_price', $min_prod_price);
                                    $currentMax = request('max_price', $max_prod_price);
                                @endphp
                                <div class="price-display">
                                    ৳<span id="price-min">{{ floor($currentMin) }}</span> - ৳<span id="price-max">{{ ceil($currentMax) }}</span>
                                </div>
                                <div id="price-slider" 
                                     data-min="{{ $min_prod_price }}" 
                                     data-max="{{ $max_prod_price }}" 
                                     data-current-min="{{ floor($currentMin) }}" 
                                     data-current-max="{{ ceil($currentMax) }}"></div>
                            </div>
                            <!-- Hidden Inputs for Price Filter -->
                            <input type="hidden" name="min_price" value="{{ $currentMin }}" id="min-price-input">
                            <input type="hidden" name="max_price" value="{{ $currentMax }}" id="max-price-input">
                        </div>

                        <!-- Colors -->
                        @if(get_setting('color_filter_activation') && count($colors) > 0)
                            <div class="filter-item color-filter">
                                <label class="filter-label">Colors</label>
                                <div class="color-options">
                                    @foreach($colors->take(8) as $color)
                                        <label class="color-item {{ (request('color') == $color->code || (isset($selected_color) && $selected_color == $color->code)) ? 'active' : '' }}" 
                                               data-color="{{ $color->code }}" 
                                               title="{{ $color->name }}">
                                            <input type="radio" name="color" value="{{ $color->code }}" 
                                                   @if((isset($selected_color) && $selected_color == $color->code) || request('color') == $color->code) checked @endif
                                                   onchange="filter()" style="display: none;">
                                            <div class="color-circle" style="background-color: {{ $color->code }};"></div>
                                        </label>
                                    @endforeach
                                    @if(count($colors) > 8)
                                        <div class="color-more-btn" onclick="toggleMoreColors(this)" data-initial-count="{{ count($colors) - 8 }}">
                                            +{{ count($colors) - 8 }}
                                        </div>
                                        <div class="more-colors" style="display: none;">
                                            @foreach($colors->skip(8) as $color)
                                                <label class="color-item {{ (request('color') == $color->code || (isset($selected_color) && $selected_color == $color->code)) ? 'active' : '' }}" 
                                                       data-color="{{ $color->code }}" 
                                                       title="{{ $color->name }}">
                                                    <input type="radio" name="color" value="{{ $color->code }}" 
                                                           @if((isset($selected_color) && $selected_color == $color->code) || request('color') == $color->code) checked @endif
                                                           onchange="filter()" style="display: none;">
                                                    <div class="color-circle" style="background-color: {{ $color->code }};"></div>
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Attributes (Size, Fabric, Liter etc.) -->
                        @if(!empty($attributes))
                            @foreach($attributes as $attribute)
                                @if(count($attribute->attribute_values) > 0)
                                    <div class="filter-item attribute-filter">
                                        <label class="filter-label">{{ $attribute->getTranslation('name') }}</label>
                                        <div class="attribute-options">
                                            @foreach($attribute->attribute_values->take(4) as $attr_value)
                                                <label class="attribute-item {{ (in_array($attr_value->value, request('selected_attribute_values', [])) || (isset($selected_attribute_values) && in_array($attr_value->value, $selected_attribute_values))) ? 'active' : '' }}">
                                                    <input type="checkbox" name="selected_attribute_values[]" 
                                                           value="{{ $attr_value->value }}" 
                                                           @if((in_array($attr_value->value, request('selected_attribute_values', []))) || (isset($selected_attribute_values) && in_array($attr_value->value, $selected_attribute_values))) checked @endif
                                                           onchange="filter()" style="display: none;">
                                                    {{ $attr_value->value }}
                                                </label>
                                            @endforeach
                                            @if(count($attribute->attribute_values) > 4)
                                                <div class="attribute-more-btn" onclick="toggleMoreAttributes(this, '{{ $attribute->id }}')" 
                                                     data-initial-count="{{ count($attribute->attribute_values) - 4 }}">
                                                    +{{ count($attribute->attribute_values) - 4 }}
                                                </div>
                                                <div class="more-attributes attribute-{{ $attribute->id }}" style="display: none;">
                                                    @foreach($attribute->attribute_values->skip(4) as $attr_value)
                                                        <label class="attribute-item {{ (in_array($attr_value->value, request('selected_attribute_values', [])) || (isset($selected_attribute_values) && in_array($attr_value->value, $selected_attribute_values))) ? 'active' : '' }}">
                                                            <input type="checkbox" name="selected_attribute_values[]" 
                                                                   value="{{ $attr_value->value }}" 
                                                                   @if((in_array($attr_value->value, request('selected_attribute_values', []))) || (isset($selected_attribute_values) && in_array($attr_value->value, $selected_attribute_values))) checked @endif
                                                                   onchange="filter()" style="display: none;">
                                                            {{ $attr_value->value }}
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif

                        <!-- Sort By -->
                        <div class="filter-item sort-filter">
                            <label class="filter-label">Sort By</label>
                            <select class="sort-select" name="sort_by" onchange="filter()">
                                <option value="">{{ translate('Default') }}</option>
                                <option value="newest" {{ request('sort_by') == 'newest' ? 'selected' : '' }}>{{ translate('Newest') }}</option>
                                <option value="oldest" {{ request('sort_by') == 'oldest' ? 'selected' : '' }}>{{ translate('Oldest') }}</option>
                                <option value="price-asc" {{ request('sort_by') == 'price-asc' ? 'selected' : '' }}>{{ translate('Price: Low to High') }}</option>
                                <option value="price-desc" {{ request('sort_by') == 'price-desc' ? 'selected' : '' }}>{{ translate('Price: High to Low') }}</option>
                            </select>
                        </div>

                        <!-- Clear All -->
                        <div class="filter-item clear-filter">
                            <button type="button" class="clear-all-btn" onclick="clearAllFilters()">
                                <i class="fas fa-times me-1"></i> {{ translate('Clear All') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preorder/General Product Tabs -->
            @if(addon_is_activated('preorder') && Route::currentRouteName() == 'search')
                <div class="container-full mt-4">
                    <div class="product-type-tabs d-flex justify-content-start align-items-center">
                        <label class="product-type-tab-item me-3">
                            <input type="radio" name="product_type" value="general_product" onchange="filter()" @if(!request('product_type') || request('product_type') == 'general_product') checked @endif>
                            <span class="product-type-tab-text">{{ translate('General Products') }}</span>
                        </label>
                        <label class="product-type-tab-item">
                            <input type="radio" name="product_type" value="preorder_product" onchange="filter()" @if(request('product_type') == 'preorder_product') checked @endif>
                            <span class="product-type-tab-text">{{ translate('Preorder Products') }}</span>
                        </label>
                    </div>
                </div>
            @endif
            
            <!-- Page Header -->
            <div class="page-header">
                <div class="container-full">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="page-title">
                                {{-- Updated logic to determine page title --}}
                                @if(isset($category) && $category != null) {{-- Check for the $category object --}}
                                    {{ $category->getTranslation('name') }}
                                @elseif(isset($brand) && $brand != null) {{-- Check for brand object passed from controller --}}
                                    {{ $brand->getTranslation('name') }} {{ translate('Products') }}
                                @elseif(request('keyword'))
                                    {{ translate('Search results for') }} "{{ request('keyword') }}"
                                @elseif(isset($is_featured_search) && $is_featured_search)
                                    {{ translate('All Featured Products') }}
                                @elseif(isset($is_best_selling_search) && $is_best_selling_search)
                                    {{ translate('All Best Selling Products') }}
                                @elseif(request('sort_by') == 'newest') {{-- Newest Products title --}}
                                    {{ translate('Newest Products') }}
                                @else
                                    {{ translate('All Products') }}
                                @endif
                            </h1>
                            <p class="results-count">
                                <span id="product-count">{{ $products->total() }}</span> {{ translate('products found') }}
                            </p>
                            <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                        </div>
                        <div class="col-md-4">
                            <div class="view-toggle">
                                <button type="button" class="view-btn active" data-view="grid">
                                    <i class="fas fa-th"></i>
                                </button>
                                <button type="button" class="view-btn" data-view="list">
                                    <i class="fas fa-list"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Container -->
            <div class="products-container">
                <div class="container-full">
                    <div class="products-grid" id="products-grid">
                        @forelse($products as $product)
                            <div class="product-item">
                                <div class="product-card">
                                    @if(request('product_type') == 'preorder_product')
                                        @include('preorder.frontend.product_box3', ['product' => $product])
                                    @else
                                        @include('frontend.'.get_setting('homepage_select').'.partials.product_box_1', ['product' => $product])
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="no-products">
                                <i class="fas fa-search fa-3x"></i>
                                <h3>{{ translate('No products found') }}</h3>
                                <p>{{ translate('Try adjusting your filters to find what you\'re looking for.') }}</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if($products->hasPages())
                        <div class="pagination-wrapper" id="pagination-wrapper">
                            {{ $products->appends(request()->except('page'))->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </form>
    </div>

    @include('frontend.inc.footer')
</section>
@endsection

@section('script')
{{-- noUiSlider JS - Place this in your main app layout's </body> or here --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.0/nouislider.min.js" integrity="sha512-HtgITRKzMMQyqL8sM+uxKqjmU/V8A/3LtmC5YcMlpzJ0j/jF5o/rY+T42pYJ5Q3m4s/0i+5K/1R+O45pC/yA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    // General filter function - submits the form
    function filter() {
        document.getElementById('search-form').submit();
    }

    // Initialize price slider
    document.addEventListener('DOMContentLoaded', function() {
        const priceSliderElement = document.getElementById('price-slider');
        if (priceSliderElement) {
            const minPrice = parseFloat(priceSliderElement.dataset.min);
            const maxPrice = parseFloat(priceSliderElement.dataset.max);
            const currentMin = parseFloat(priceSliderElement.dataset.currentMin);
            const currentMax = parseFloat(priceSliderElement.dataset.max); 

            noUiSlider.create(priceSliderElement, {
                start: [currentMin, currentMax],
                connect: true,
                range: {
                    'min': minPrice,
                    'max': maxPrice
                },
                step: 1, // Integer steps for price, adjust to 0.01 if decimals are needed
                format: {
                    to: function (value) {
                        return Math.round(value); // Display as rounded integer
                    },
                    from: function (value) {
                        return Number(value);
                    }
                }
            });

            priceSliderElement.noUiSlider.on('update', function (values, handle) {
                const minVal = Math.round(values[0]);
                const maxVal = Math.round(values[1]);
                document.getElementById('price-min').textContent = minVal;
                document.getElementById('price-max').textContent = maxVal;
                document.getElementById('min-price-input').value = minVal;
                document.getElementById('max-price-input').value = maxVal;
            });

            priceSliderElement.noUiSlider.on('set', function () {
                filter(); // Submit form when slider is released
            });
        }
    });

    // Toggle more colors visibility
    function toggleMoreColors(button) {
        const moreColorsContainer = button.nextElementSibling; // The div containing more colors
        if (moreColorsContainer && moreColorsContainer.classList.contains('more-colors')) {
            if (moreColorsContainer.style.display === 'none') {
                moreColorsContainer.style.display = 'flex';
                button.textContent = 'Less';
            } else {
                moreColorsContainer.style.display = 'none';
                const initialCount = button.dataset.initialCount || 0; 
                button.textContent = `+${initialCount}`;
            }
        }
    }

    // Toggle more attributes visibility
    function toggleMoreAttributes(button, attributeId) {
        const moreAttributesContainer = button.nextElementSibling; // The div containing more attributes
        if (moreAttributesContainer && moreAttributesContainer.classList.contains(`attribute-${attributeId}`)) {
            if (moreAttributesContainer.style.display === 'none') {
                moreAttributesContainer.style.display = 'flex';
                button.textContent = 'Less';
            } else {
                moreAttributesContainer.style.display = 'none';
                const initialCount = button.dataset.initialCount || 0; 
                button.textContent = `+${initialCount}`;
            }
        }
    }

    // Clear All Filters
    function clearAllFilters() {
        // Clear price range (set to min/max from initial data)
        const priceSliderElement = document.getElementById('price-slider');
        if (priceSliderElement && priceSliderElement.noUiSlider) {
            priceSliderElement.noUiSlider.set([priceSliderElement.dataset.min, priceSliderElement.dataset.max]);
        }

        // Uncheck all color radios
        document.querySelectorAll('input[name="color"]:checked').forEach(radio => {
            radio.checked = false;
        });

        // Uncheck all attribute checkboxes
        document.querySelectorAll('input[name="selected_attribute_values[]"]:checked').forEach(checkbox => {
            checkbox.checked = false;
        });

        // Reset Sort By select box
        document.querySelector('.sort-select').value = '';

        // Reset product_type if it exists (for preorder/general tabs)
        const generalProductRadio = document.querySelector('input[name="product_type"][value="general_product"]');
        if (generalProductRadio) {
            generalProductRadio.checked = true; // Default to General Products
        }
        
        filter(); // Submit the form to apply cleared filters
    }

    // View toggle functionality (Grid/List)
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const grid = document.getElementById('products-grid');
            if (this.dataset.view === 'list') {
                grid.classList.add('list-view');
            } else {
                grid.classList.remove('list-view');
            }
            // You might want to store this preference in localStorage
            // or send an AJAX request to update user settings.
        });
    });

    // Update 'More' button text on page load for attributes
    document.querySelectorAll('.attribute-more-btn').forEach(button => {
        const moreAttributesContainer = button.nextElementSibling;
        if (moreAttributesContainer && moreAttributesContainer.style.display === 'flex') {
            button.textContent = 'Less';
        }
    });
    // Update 'More' button text on page load for colors
    document.querySelectorAll('.color-more-btn').forEach(button => {
        const moreColorsContainer = button.nextElementSibling;
        if (moreColorsContainer && moreColorsContainer.style.display === 'flex') {
            button.textContent = 'Less';
        }
    });

</script>
@endsection

<style>
/* === GENERAL STYLES === */
body {
    font-family: 'Poppins', sans-serif;
    background-color: #f8f9fa;
    color: #333;
    line-height: 1.6;
}

.container-fluid {
    max-width: 1600px; /* Optional: adjust for larger screens */
    margin-left: auto;
    margin-right: auto;
    padding-left: 15px;
    padding-right: 15px;
}

/* === HORIZONTAL FILTER WRAPPER === */
.filter-wrapper {
    background: white;
    border-bottom: 2px solid #e9ecef;
    position: sticky;
    top: 0;
    z-index: 1000; /* Ensure it stays on top */
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    padding: 15px 0; /* Reduced padding for compactness */
    margin-bottom: 20px; /* Space below filters */
}
.product-listing-section,
.filter-wrapper {
    overflow: visible;
}

.horizontal-filters {
    display: flex;
    align-items: center;
    gap: 30px; /* Reduced gap between filter items */
    overflow-x: auto; /* Enable horizontal scrolling */
    scrollbar-width: none; /* Hide scrollbar for Firefox */
    -ms-overflow-style: none; /* Hide scrollbar for IE/Edge */
    padding: 0 10px; /* Add some horizontal padding for scrollable area */
}

.horizontal-filters::-webkit-scrollbar {
    display: none; /* Hide scrollbar for Chrome/Safari */
}

.filter-item {
    flex-shrink: 0; /* Prevent items from shrinking */
    display: flex;
    flex-direction: column;
    gap: 6px; /* Reduced gap within a filter item */
    min-width: fit-content; /* Ensure content determines minimum width */
    padding: 5px 0; /* Small vertical padding */
}

.filter-label {
    font-size: 0.9rem; /* Smaller font size */
    font-weight: 600;
    color: #495057;
    margin-bottom: 0; /* Remove default margin */
    white-space: nowrap; /* Keep label on single line */
}

/* === PRICE FILTER === */
.price-filter {
    min-width: 180px; /* Min width for price filter */
}

.price-slider-container {
    padding: 0 5px; /* Adjust slider padding */
}

.price-display {
    text-align: center;
    font-weight: 600;
    color: #007bff;
    font-size: 0.85rem; /* Smaller font for price display */
    margin-bottom: 8px; /* Reduced margin */
    white-space: nowrap;
}

#price-slider {
    width: 100%; /* Make slider fill its container */
    height: 6px; /* Thinner slider */
    margin: 0;
}

.noUi-connect {
    background: linear-gradient(90deg, #007bff 0%, #0056b3 100%);
    box-shadow: none; /* Remove default shadow */
}
.noUi-horizontal .noUi-background {
    background: #e9ecef; /* Lighter background for unselected part */
}

.noUi-handle {
    width: 16px; /* Smaller handle */
    height: 16px;
    border-radius: 50%;
    background: white;
    border: 2px solid #007bff; /* Thinner border */
    box-shadow: 0 1px 4px rgba(0,123,255,0.3); /* Lighter shadow */
    cursor: grab;
    top: -5px; /* Adjust position */
    left: -8px; /* Adjust position */
    transition: all 0.15s ease;
}

.noUi-handle:hover {
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(0,123,255,0.4);
}

.noUi-handle:before,
.noUi-handle:after {
    display: none; /* Remove default inner lines */
}

/* === COLOR FILTER === */
.color-options {
    display: flex;
    align-items: center;
    gap: 6px; /* Reduced gap */
    flex-wrap: nowrap; /* Keep colors in one line initially */
    max-height: 36px; /* Max height to hide overflow, allowing more-btn */
    overflow: hidden; /* Hide overflow */
}

.color-item {
    cursor: pointer;
    position: relative;
    transition: transform 0.15s ease;
    flex-shrink: 0; /* Prevent shrinking */
}

.color-item:hover {
    transform: scale(1.08);
}

.color-item.active {
    transform: scale(1.12);
}

.color-item.active::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-weight: bold;
    font-size: 10px;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.7);
}

.color-circle {
    width: 28px; /* Smaller circle */
    height: 28px;
    border-radius: 50%;
    border: 2px solid #dee2e6;
    transition: all 0.15s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1); /* Subtle shadow */
}

.color-item.active .color-circle {
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.3);
}

.color-more-btn {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    border: 1px dashed #ced4da; /* Lighter dashed border */
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem; /* Smaller font */
    color: #6c757d;
    cursor: pointer;
    transition: all 0.15s ease;
    flex-shrink: 0;
}

.color-more-btn:hover {
    border-color: #007bff;
    color: #007bff;
    background-color: #f0f8ff; /* Light hover background */
}

.more-colors {
    display: flex;
    gap: 6px;
    margin-left: 10px; /* Keep it aligned with other items */
    flex-wrap: wrap; /* Allow wrapping when expanded */
    overflow: visible !important; /* Make sure it doesn't clip when shown */
    max-height: none !important; /* Allow content to dictate height when shown */
}

/* === ATTRIBUTE FILTER === */
.attribute-options {
    display: flex;
    gap: 6px; /* Reduced gap */
    flex-wrap: nowrap; /* Keep initial items in one line */
    
    overflow: hidden; /* Hide overflow */
    align-items: center;
}

.attribute-item {
    padding: 5px 10px; /* Reduced padding */
    background: #e9ecef; /* Lighter background */
    border: 1px solid #ced4da; /* Lighter border */
    border-radius: 15px; /* More rounded */
    font-size: 0.85rem; /* Smaller font */
    color: #495057;
    cursor: pointer;
    transition: all 0.15s ease;
    white-space: nowrap;
    flex-shrink: 0;
}

.attribute-item:hover {
    background: #dee2e6;
    border-color: #007bff;
}

.attribute-item.active {
    background: #007bff;
    border-color: #007bff;
    color: white;
    box-shadow: 0 1px 4px rgba(0,123,255,0.3);
}

.attribute-more-btn {
    padding: 5px 10px;
    background: #6c757d;
    color: white;
    border-radius: 15px;
    font-size: 0.7rem; /* Smaller font */
    cursor: pointer;
    transition: background 0.15s ease;
    flex-shrink: 0;
    white-space: nowrap;
}

.attribute-more-btn:hover {
    background: #495057;
}

.more-attributes {
    display: flex;
    gap: 6px;
    margin-top: 6px; /* Space from initial items */
    flex-wrap: wrap;
    overflow: visible !important;
    max-height: none !important;
}

/* === SORT FILTER === */
.sort-select {
    padding: 7px 10px; /* Reduced padding */
    border: 1px solid #ced4da;
    border-radius: 6px;
    background: white;
    color: #495057;
    font-size: 0.9rem; /* Smaller font */
    cursor: pointer;
    min-width: 120px; /* Reduced min-width */
    height: 36px; /* Consistent height with other elements */
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5L8 11L14 5'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 8px center;
    background-size: 0.8em 0.8em;
}

.sort-select:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 0.15rem rgba(0,123,255,0.25);
}

/* === CLEAR ALL BUTTON === */
.clear-filter {
    margin-left: auto; /* Push clear all to the right */
}

.clear-all-btn {
    display: flex;
    align-items: center;
    gap: 5px; /* Reduced gap */
    padding: 7px 14px; /* Reduced padding */
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 18px; /* Slightly less rounded */
    font-size: 0.85rem; /* Smaller font */
    font-weight: 500;
    cursor: pointer;
    transition: background 0.15s ease;
    height: 36px; /* Consistent height */
}

.clear-all-btn:hover {
    background: #c82333;
    box-shadow: 0 2px 6px rgba(220,53,69,0.3);
}

/* === LOADING OVERLAY (Unchanged) === */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.loading-spinner {
    text-align: center;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 15px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* === PRODUCT TYPE TABS (General/Preorder) === */
.product-type-tabs {
    border-radius: 8px; /* Slightly smaller radius */
    background-color: #fff;
    box-shadow: 0 1px 6px rgba(0,0,0,0.05); /* Lighter shadow */
    padding: 6px; /* Reduced padding */
    display: inline-flex;
}

.product-type-tab-item {
    display: block;
    cursor: pointer;
    position: relative;
}

.product-type-tab-item input[type="radio"] {
    display: none;
}

.product-type-tab-text {
    display: block;
    padding: 8px 18px; /* Reduced padding */
    font-size: 0.9rem; /* Smaller font */
    font-weight: 500;
    border-radius: 6px;
    transition: all 0.2s ease;
    color: #6c757d;
    border: 1px solid transparent;
}

.product-type-tab-item input[type="radio"]:checked + .product-type-tab-text {
    background-color: #007bff; /* Primary blue for active */
    color: white;
    box-shadow: 0 2px 8px rgba(0,123,255,0.3);
    border-color: #007bff;
}

.product-type-tab-item input[type="radio"]:not(:checked) + .product-type-tab-text:hover {
    background-color: #f0f2f5;
    color: #495057;
}

/* === PAGE HEADER === */
.page-header {
    background: transparent; /* Changed to transparent for a cleaner look */
    padding: 25px 0 20px; /* Adjust padding */

}

.page-title {
    color: #212529;
    font-weight: 700;
    font-size: 2rem; /* Adjusted font size */
    margin-bottom: 5px;
}

.results-count {
    color: #6c757d;
    font-size: 0.95rem;
    margin: 0;
}

.view-toggle {
    display: flex;
    gap: 6px;
    justify-content: flex-end;
}

.view-btn {
    width: 36px; /* Smaller buttons */
    height: 36px;
    border: 1px solid #dee2e6; /* Thinner border */
    background: white;
    color: #6c757d;
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s ease;
    font-size: 0.9rem;
}

.view-btn.active,
.view-btn:hover {
    background: #007bff;
    border-color: #007bff;
    color: white;
}

/* === PRODUCTS GRID === */
.products-container {
    padding-bottom: 40px;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); /* Adjusted minmax for more items */
    gap: 15px; /* Reduced gap */
    margin-bottom: 30px;
}

.products-grid.list-view {
    grid-template-columns: 1fr;
}

/* Responsive Grid Adjustments */
@media (min-width: 1400px) {
    .products-grid {
        grid-template-columns: repeat(6, 1fr);
    }
}
@media (min-width: 1200px) and (max-width: 1399px) {
    .products-grid {
        grid-template-columns: repeat(5, 1fr);
    }
}
@media (min-width: 992px) and (max-width: 1199px) {
    .products-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}
@media (min-width: 768px) and (max-width: 991px) {
    .products-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}
@media (max-width: 767px) {
    .products-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    
    .horizontal-filters {
        gap: 15px;
        padding: 10px 0;
    }
    
    .filter-item {
        padding: 0; /* Remove vertical padding */
    }

    .filter-label {
        font-size: 0.8rem;
    }
    
    .price-filter {
        min-width: 140px;
    }
    
    .color-circle {
        width: 24px;
        height: 24px;
    }
    .color-more-btn {
        width: 24px;
        height: 24px;
        font-size: 0.7rem;
    }
    
    .attribute-item {
        font-size: 0.75rem;
        padding: 4px 8px;
    }
    .attribute-more-btn {
        font-size: 0.65rem;
        padding: 4px 8px;
    }
    
    .sort-select {
        font-size: 0.8rem;
        padding: 5px 8px;
        min-width: 100px;
        height: 32px;
    }
    .clear-all-btn {
        font-size: 0.8rem;
        padding: 5px 10px;
        height: 32px;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    .results-count {
        font-size: 0.85rem;
    }
    .view-btn {
        width: 32px;
        height: 32px;
        font-size: 0.8rem;
    }

    .product-type-tab-text {
        font-size: 0.8rem;
        padding: 6px 12px;
    }
    .product-type-tabs {
        padding: 4px;
    }
}
@media (max-width: 420px) {
    .products-grid {
        grid-template-columns: 1fr; /* Single column on very small screens */
    }
    .page-header .row {
        flex-direction: column;
        text-align: center;
    }
    .view-toggle {
        justify-content: center;
        margin-top: 15px;
    }
    .clear-filter {
        margin-left: unset; /* Remove auto margin */
        width: 100%; /* Make clear all button full width */
        text-align: center;
    }
    .clear-all-btn {
        width: auto;
        margin: 0 auto;
    }
}

/* === PAGINATION === */
.pagination-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 30px;
}

/* Product Card Styling (Assumes existing partials) */
.product-item {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.2s ease-in-out;
}

.product-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.12);
}

/* No Products Found */
.no-products {
    grid-column: 1 / -1; /* Span full width of the grid */
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-top: 20px;
}

.no-products i {
    margin-bottom: 15px;
    color: #dee2e6;
    font-size: 4rem;
}

.no-products h3 {
    margin-bottom: 8px;
    color: #495057;
    font-size: 1.8rem;
    font-weight: 600;
}

.no-products p {
    font-size: 1rem;
    max-width: 400px;
    margin: 0 auto;
}

/* Add this CSS to your existing <style> block in product_listing.blade.php */

/* 1. Ensure image fills its container and covers the space */
.img-fit {
    width: 100%;
    height: 100%;
    object-fit: cover; /* This crops the image to fill the space */
    object-position: center; /* Ensures the center of the image is shown */
}

/* 2. Override default padding for aiz-card-box to control spacing manually */
.aiz-card-box {
    padding-top: 0 !important; /* Remove initial pt-3 */
    /* padding-bottom is now handled by the pb-3 class in HTML */
}

/* 3. Style for the Add to Cart/Place Bid button */
.add-to-cart-product-box {
    /* Reference button style: A solid, slightly rounded button */
    background-color: #5b69c6; /* Example: A shade of blue/purple (adjust as needed) */
    color: white;
    border: none;
    border-radius: 8px; /* Slightly more rounded corners */
    padding: 10px 15px; /* Comfortable padding */
    font-size: 0.95rem; /* Slightly larger text */
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    width: 100%; /* Ensure it spans full width */
    text-align: center; /* Center text */
    box-shadow: 0 2px 6px rgba(0,0,0,0.1); /* Subtle shadow */
}

.add-to-cart-product-box:hover {
    background-color: #4a57b5; /* Slightly darker on hover */
    transform: translateY(-1px); /* Slight lift effect */
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}

/* Adjust margins for price and name for better spacing */
.h-35px.text-center { /* Product name */
    margin-bottom: 0.5rem; /* Give some space below the name */
}

.fs-14.d-flex.justify-content-center { /* Price display */
    margin-top: 0.5rem !important; /* Adjust if default mt-3 was too much/little */
    /* mb-auto will push the button wrapper to the bottom */
}

/* Ensure the product card takes full height of its grid item */
.product-item {
    height: 100%;
}
.product-card {
    height: 100%;
}

/* Ensure product name wraps nicely */
.text-truncate-2 {
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2; /* Limit to 2 lines */
    -webkit-box-orient: vertical;
    white-space: normal; /* Allow text to wrap */
}

/* Adjust existing specific overrides if necessary, e.g., if another CSS is conflicting */
/* For example, if .aiz-card-box has a default padding that is stubborn */
/* .aiz-card-box {
    padding: 0 !important;
} */



</style>

