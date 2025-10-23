{{-- All HTML content for product details page --}}
<section class="mb-4 pt-3">
    <div class="container">
        <div class="bg-white py-3">
            <div class="row">
                <!-- Product Image Gallery -->
                <div class="col-xl-5 col-lg-6 mb-4">
                    @include('frontend.product_details.image_gallery')
                </div>

                <!-- Product Details -->
                <div class="col-xl-7 col-lg-6">
                    @include('frontend.product_details.details')
                </div>
            </div>
        </div>
    </div>
</section>

<section class="mb-4">
    <div class="container">
        @if ($detailedProduct->auction_product)
            <!-- Reviews & Ratings -->
            @include('frontend.product_details.review_section')
            
            <!-- Description, Video, Downloads -->
            @include('frontend.product_details.description')
            
            <!-- Product Query -->
            @include('frontend.product_details.product_queries')
        @else
            <div class="row gutters-16">
                <!-- Left side -->
                <div class="col-lg-3">
                    <!-- Seller Info -->
                    @include('frontend.product_details.seller_info')

                    <!-- Top Selling Products -->
                   <div class="d-none d-lg-block">
                        @include('frontend.product_details.top_selling_products')
                   </div>
                </div>

                <!-- Right side -->
                <div class="col-lg-9">
                    
                    <!-- Reviews & Ratings -->
                    @include('frontend.product_details.review_section')

                    <!-- Description, Video, Downloads -->
                    @include('frontend.product_details.description')
                    
                    <!-- Frequently Bought products -->
                    @include('frontend.product_details.frequently_bought_products')

                    <!-- Product Query -->
                    @include('frontend.product_details.product_queries')
                    
                    <!-- Top Selling Products -->
                    <div class="d-lg-none">
                         @include('frontend.product_details.top_selling_products')
                    </div>

                </div>
            </div>
        @endif
    </div>
</section>

