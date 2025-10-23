<div class="sticky-top z-3 row gutters-10">
    @php
        // Completely safe photo handling
        $photos = [];
        
        try {
            if (isset($detailedProduct->photos) && $detailedProduct->photos !== null) {
                if (is_array($detailedProduct->photos)) {
                    $photos = $detailedProduct->photos;
                } elseif (is_string($detailedProduct->photos) && !empty($detailedProduct->photos)) {
                    // Handle both comma-separated and single photo strings
                    if (strpos($detailedProduct->photos, ',') !== false) {
                        $photos = explode(',', $detailedProduct->photos);
                    } else {
                        $photos = [$detailedProduct->photos];
                    }
                }
            }
            
            // Clean up array - remove empty, null, or invalid entries
            $photos = array_filter($photos, function($photo) {
                return !empty($photo) && is_string($photo);
            });
            
            // Re-index array to avoid gaps
            $photos = array_values($photos);
            
        } catch (Exception $e) {
            // Log error and set empty array as fallback
            \Log::error('Product photos error: ' . $e->getMessage());
            $photos = [];
        }
        
        // Get stock images safely
        $stockImages = [];
        try {
            if ($detailedProduct->digital == 0 && isset($detailedProduct->stocks)) {
                foreach ($detailedProduct->stocks as $stock) {
                    if (isset($stock->image) && !empty($stock->image)) {
                        $stockImages[] = $stock;
                    }
                }
            }
        } catch (Exception $e) {
            \Log::error('Stock images error: ' . $e->getMessage());
            $stockImages = [];
        }
    @endphp
    
    <!-- Gallery Images -->
    <div class="col-12">
        <div class="modern-product-gallery">
            <div class="main-image-container">
                @php $totalImages = count($photos) + count($stockImages); @endphp
                
                @if ($totalImages > 0)
                    <div class="aiz-carousel product-gallery arrow-inactive-transparent arrow-lg-none"
                        data-nav-for='.product-gallery-thumb' 
                        data-fade='true' 
                        data-auto-height='true' 
                        data-arrows='true'>
                        
                        <!-- Stock Images First -->
                        @if (!empty($stockImages))
                            @foreach ($stockImages as $index => $stock)
                                <div class="carousel-box img-zoom rounded-0" style="width: 80%!important; ">
                                    <div class="image-wrapper">
                                        <img class="img-fluid h-auto lazyload mx-auto main-product-image"
                                            src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                            data-src="{{ uploaded_asset($stock->image) }}"
                                            alt="{{ $detailedProduct->getTranslation('name') }} - {{ $stock->variant ?? 'Variant' }}"
                                            data-zoom="{{ uploaded_asset($stock->image) }}"
                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                        
                                        <!-- Image overlay info -->
                                        @if (isset($stock->variant))
                                            <div class="image-overlay">
                                                <span class="variant-label">{{ $stock->variant }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endif
                        
                        <!-- Regular Product Photos -->
                        @if (!empty($photos))
                            @foreach ($photos as $index => $photo)
                                <div class="carousel-box img-zoom rounded-0">
                                    <div class="image-wrapper">
                                        <img class="img-fluid h-auto lazyload mx-auto main-product-image"
                                            src="{{ static_asset('assets/img/placeholder.jpg') }}" 
                                            data-src="{{ uploaded_asset($photo) }}"
                                            alt="{{ $detailedProduct->getTranslation('name') }} - Image {{ $index + 1 }}"
                                            data-zoom="{{ uploaded_asset($photo) }}"
                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    
                    <!-- Image Counter -->
                    <div class="image-counter">
                        <span class="current-image">1</span> / <span class="total-images">{{ $totalImages }}</span>
                    </div>
                    
                @else
                    <!-- No Images Available -->
                    <div class="no-image-container">
                        <img class="img-fluid h-auto mx-auto"
                            src="{{ static_asset('assets/img/placeholder.jpg') }}"
                            alt="{{ $detailedProduct->getTranslation('name') }}">
                        <p class="text-muted text-center mt-2">{{ translate('No images available') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Thumbnail Images -->
    <div class="col-12 mt-3 d-none d-lg-block">
        @if ($totalImages > 1)
            <div class="thumbnail-gallery-container">
                <div class="aiz-carousel half-outside-arrow product-gallery-thumb" 
                     data-items='{{ min($totalImages, 7) }}' 
                     data-nav-for='.product-gallery'
                     data-focus-select='true' 
                     data-arrows='{{ $totalImages > 7 ? "true" : "false" }}' 
                     data-vertical='false' 
                     data-auto-height='true'>

                    <!-- Stock Thumbnails -->
                    @if (!empty($stockImages))
                        @foreach ($stockImages as $index => $stock)
                            <div class="carousel-box c-pointer rounded-0 thumbnail-item" 
                                 data-variation="{{ $stock->variant ?? '' }}"
                                 data-index="{{ $index }}"
                                 title="{{ $stock->variant ?? 'Variant ' . ($index + 1) }}">
                                <div class="thumbnail-wrapper">
                                    <img class="lazyload mw-100 size-60px mx-auto border p-1 thumbnail-image"
                                        src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                        data-src="{{ uploaded_asset($stock->image) }}"
                                        alt="Thumbnail {{ $index + 1 }}"
                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                    
                                    @if (isset($stock->variant))
                                        <div class="thumbnail-label">{{ Str::limit($stock->variant, 10) }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <!-- Regular Photo Thumbnails -->
                    @if (!empty($photos))
                        @foreach ($photos as $index => $photo)
                            <div class="carousel-box c-pointer rounded-0 thumbnail-item" 
                                 data-index="{{ count($stockImages) + $index }}"
                                 title="Image {{ $index + 1 }}">
                                <div class="thumbnail-wrapper">
                                    <img class="lazyload mw-100 size-60px mx-auto border p-1 thumbnail-image"
                                        src="{{ static_asset('assets/img/placeholder.jpg') }}" 
                                        data-src="{{ uploaded_asset($photo) }}"
                                        alt="Thumbnail {{ $index + 1 }}"
                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<style>
/* Enhanced Gallery Styles */
.modern-product-gallery {
    background: white;
    /* border-radius: 12px; */
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.main-image-container {
    position: relative;
}

.image-wrapper {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
    /* Fixed height for main gallery area */
    /* height: 520px; */
}

.main-product-image {
    transition: transform 0.3s ease;
    cursor: zoom-in;
    width: 100% !important;
    height: 100% !important;
    object-fit: contain;
}

.main-product-image:hover {
    transform: scale(1.05);
}

.image-overlay {
    position: absolute;
    bottom: 10px;
    left: 10px;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
}

.image-counter {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
}

.no-image-container {
    padding: 40px 20px;
    text-align: center;
    background: #f8f9fa;
}

.thumbnail-gallery-container {
    background: white;
    padding: 15px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.thumbnail-item {
    transition: transform 0.3s ease;
}

.thumbnail-item:hover {
    transform: translateY(-3px);
}

.thumbnail-wrapper {
    position: relative;
    /* Fixed box for thumbnails */
    width: 64px;
    height: 64px;
}

.thumbnail-image {
    border-radius: 6px;
    transition: all 0.3s ease;
    /* Fixed thumbnail sizing */
    width: 100% !important;
    height: 100% !important;
    object-fit: cover;
}

.thumbnail-item:hover .thumbnail-image {
    border-color: #3498db !important;
}

.thumbnail-label {
    position: absolute;
    bottom: -15px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 10px;
    color: #666;
    white-space: nowrap;
}

/* Slick carousel active state */
.slick-current .thumbnail-image {
    border-color: #3498db !important;
    box-shadow: 0 0 10px rgba(52, 152, 219, 0.3);
}

/* Error handling styles */
.image-error {
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 300px;
    color: #999;
    flex-direction: column;
    border-radius: 8px;
}

.image-error i {
    font-size: 48px;
    margin-bottom: 10px;
}

/* Responsive fixes */
@media (max-width: 768px) {
    .image-wrapper { height: 320px; }
    .thumbnail-wrapper { width: 56px; height: 56px; }
}
</style>

<script>
$(document).ready(function() {
    // Update image counter on slide change
    $('.product-gallery').on('afterChange', function(event, slick, currentSlide) {
        $('.current-image').text(currentSlide + 1);
    });
    
    // Handle image loading errors
    $('.main-product-image, .thumbnail-image').on('error', function() {
        $(this).closest('.carousel-box').addClass('image-error')
               .html('<i class="las la-image"></i><span>Image not available</span>');
    });
});
</script>

