<!-- Last Viewed Products  -->
@if (get_setting('last_viewed_product_activation') == 1 && Auth::check() && auth()->user()->user_type == 'customer')
    <div class="border-top" id="section_last_viewed_products" style="background-color: #fcfcfc;">
        @php
            $lastViewedProducts = getLastViewedProducts();
        @endphp
        @if (count($lastViewedProducts) > 0)
            <section class="my-2 my-md-3">
                <div class="container">
                    <!-- Top Section -->
                    <div class="d-flex mb-2 mb-md-3 align-items-baseline justify-content-between">
                        <!-- Title -->
                        <h3 class="fs-16 fw-700 mb-2 mb-sm-0">
                            <span class="">{{ translate('Last Viewed Products') }}</span>
                        </h3>
                        <!-- Links -->
                        <div class="d-flex">
                            <a type="button" class="arrow-prev slide-arrow link-disable text-secondary mr-2"
                                onclick="clickToSlide('slick-prev','section_last_viewed_products')"><i
                                    class="las la-angle-left fs-20 fw-600"></i></a>
                            <a type="button" class="arrow-next slide-arrow text-secondary ml-2"
                                onclick="clickToSlide('slick-next','section_last_viewed_products')"><i
                                    class="las la-angle-right fs-20 fw-600"></i></a>
                        </div>
                    </div>
                    <!-- Product Section -->
                    <div class="px-sm-3">
                        <div class="aiz-carousel slick-left sm-gutters-16 arrow-none" data-items="6" data-xl-items="5"
                            data-lg-items="4" data-md-items="3" data-sm-items="2" data-xs-items="2" data-arrows='true'
                            data-infinite='false'>
                            @foreach ($lastViewedProducts as $key => $lastViewedProduct)
                                <div
                                    class="carousel-box px-3 position-relative has-transition hov-animate-outline border-right border-top border-bottom @if ($key == 0) border-left @endif">
                                    @include(
                                        'frontend.' .
                                            get_setting('homepage_select') .
                                            '.partials.last_view_product_box_1',
                                        ['product' => $lastViewedProduct->product]
                                    )
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
        @endif
    </div>
@endif
<section style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%)!important;">
    <!-- footer subscription & icons -->
    <div class=" footer-bottom"> {{-- Removed linear-gradient, set to white background --}}
        <div class="container">
            <div class="text-center"> {{-- Centering all content within this section --}}
                <!-- footer logo -->
                <a href="{{ route('home') }}" class="d-inline-block mb-1 mt-2"> {{-- Use d-inline-block to center image itself with text-center --}}
                    @if (get_setting('footer_logo') != null)
                        <img class="lazyload h-50px" src="{{ static_asset('assets/img/placeholder-rect.jpg') }}"
                            data-src="{{ uploaded_asset(get_setting('footer_logo')) }}" alt="{{ env('APP_NAME') }}"
                            height="50">
                    @else
                        <img class="lazyload h-50px" src="{{ static_asset('assets/img/placeholder-rect.jpg') }}"
                            data-src="{{ static_asset('assets/img/logo.png') }}" alt="{{ env('APP_NAME') }}"
                            height="50">
                    @endif
                </a>

                <!-- Social -->
                @if (get_setting('show_social_links'))
                    <ul class="list-inline social-icons mb-3"> {{-- Custom class for styling --}}
                        @if (!empty(get_setting('facebook_link')))
                            <li class="list-inline-item mx-2">
                                <a href="{{ get_setting('facebook_link') }}" target="_blank" class="facebook"><i
                                        class="lab la-facebook-f"></i></a>
                            </li>
                        @endif
                        {{-- Twitter link is not in the reference image, but keeping for functionality --}}
                        @if (!empty(get_setting('twitter_link')))
                            <li class="list-inline-item mx-2">
                                <a href="{{ get_setting('twitter_link') }}" target="_blank" class="twitter"><i
                                        class="lab la-twitter"></i></a>
                            </li>
                        @endif
                        @if (!empty(get_setting('instagram_link')))
                            <li class="list-inline-item mx-2">
                                <a href="{{ get_setting('instagram_link') }}" target="_blank" class="instagram"><i
                                        class="lab la-instagram"></i></a>
                            </li>
                        @endif
                        @if (!empty(get_setting('youtube_link')))
                            <li class="list-inline-item mx-2">
                                <a href="{{ get_setting('youtube_link') }}" target="_blank" class="youtube"><i
                                        class="lab la-youtube"></i></a>
                            </li>
                        @endif
                        @if (!empty(get_setting('linkedin_link')))
                            <li class="list-inline-item mx-2">
                                <a href="{{ get_setting('linkedin_link') }}" target="_blank" class="linkedin"><i
                                        class="lab la-linkedin-in"></i></a>
                            </li>
                        @endif
                        {{-- Added TikTok icon as per the reference image --}}
                        <li class="list-inline-item mx-2">
                            <a href="https://www.tiktok.com/@skybuy" target="_blank" class="tiktok"><i
                                    class="lab la-tiktok"></i></a>
                        </li>
                    </ul>
                @endif

                <!-- Tagline -->
                <p class="fs-16 fw-600 text-dark mb-4">{!! get_setting('about_us_description', null, App::getLocale()) !!}</p>
            </div>

            {{-- Removed original content (about text, newsletter, old social/app links) from here --}}

        </div>
    </div>

    <!-- Custom Styles -->
    <style>
        :root {
            --primary: #3d3d46;
            /* Example primary color, adjust to your theme's primary color */
            --dark: #343a40;
            --secondary-color: #919199;
            --primary-color: #007bff;
            /* Example for text-primary, update if your theme has specific */
        }

        .border-primary {
            border-color: #3d3d46 !important;
            /* Tealish color from the image, adjust as needed */
        }

        .border-width-2 {
            border-width: 1px !important;
        }

        .text-dark {
            color: var(--dark) !important;
        }

        .fs-16 {
            font-size: 1rem !important;
        }

        .fw-600 {
            font-weight: 600 !important;
        }

        .fw-700 {
            font-weight: 700 !important;
        }

        .gap-3 {
            gap: 1rem;
        }

        /* Custom utility for flex gap */

        /* Footer Specific Styles */
        section.bg-white {
            /* Targeting this specific section */
            background-color: white !important;
        }

        .text-soft-light {
            color: white !important;
        }

        .h-50px {
            /* For the main logo */
            height: 60px;
            object-fit: contain;
        }

        .h-40px {
            /* For the smaller brand logos */
            height: 40px;
            object-fit: contain;
        }

        /* Social Icons (custom styling to match the circular colored icons) */
        .social-icons {
            display: flex;
            justify-content: center;
            list-style: none;
            padding: 0;
            margin-top: 1rem;
            /* Adjust spacing as needed */
            margin-bottom: 1.5rem;
        }

        .social-icons .list-inline-item {
            margin: 0 8px;
            /* Adjust spacing between icons */
        }

        .social-icons a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            /* Size of the circular background */
            height: 36px;
            border-radius: 50%;
            color: white;
            /* Icon color */
            font-size: 18px;
            /* Icon size */
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        /* Specific background colors for social icons */
        .social-icons .facebook {
            background-color: #3b5998;
        }

        .social-icons .twitter {
            background-color: #00acee;
        }

        /* If you decide to include twitter */
        .social-icons .instagram {
            background-color: #e4405f;
        }

        /* Or a gradient if more complex */
        .social-icons .youtube {
            background-color: #c4302b;
        }

        .social-icons .linkedin {
            background-color: #0077b5;
        }

        .social-icons .tiktok {
            background-color: #000;
        }

        /* TikTok's primary color */

        .social-icons a:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }

        /* Generic flex utility, adjust if your theme already has one */
        .d-flex.flex-wrap.gap-3 {
            gap: 1rem;
            /* Equivalent to Bootstrap's gap-3 */
        }
    </style>

    @php
        $col_values =
            get_setting('vendor_system_activation') == 1 || addon_is_activated('delivery_boy')
                ? 'col-lg-3 col-md-6 col-sm-6'
                : 'col-md-4 col-sm-6';
    @endphp
    <div class="py-lg-3 text-light footer-widget footer-bottom">
        <!-- footer widgets ========== [Accordion Fotter widgets are bellow from this]-->
        <div class="container d-none d-lg-block">
            <div class="row">
                <!-- Quick links -->
                <div class="{{ $col_values }}">
                    <div class="text-center text-sm-left mt-4">
                        <h4 class="fs-14 text-secondary text-uppercase fw-700 mb-3">
                            {{ get_setting('widget_one', null, App::getLocale()) }}
                        </h4>
                        <ul class="list-unstyled">
                            @if (get_setting('widget_one_labels', null, App::getLocale()) != null)
                                @foreach (json_decode(get_setting('widget_one_labels', null, App::getLocale()), true) as $key => $value)
                                    @php
                                        $widget_one_links = '';
                                        if (isset(json_decode(get_setting('widget_one_links'), true)[$key])) {
                                            $widget_one_links = json_decode(get_setting('widget_one_links'), true)[
                                                $key
                                            ];
                                        }
                                    @endphp
                                    <li class="mb-2">
                                        <a href="{{ $widget_one_links }}"
                                            class="fs-13 text-soft-light animate-underline-white">
                                            {{ $value }}
                                        </a>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>

                <!-- Contacts -->
                <div class="{{ $col_values }}">
                    <div class="text-center text-sm-left mt-4">
                        <h4 class="fs-14 text-secondary text-uppercase fw-700 mb-3">{{ translate('Contacts') }}</h4>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <p class="fs-13 text-secondary mb-1">{{ translate('Address') }}</p>
                                <p class="fs-13 text-soft-light">
                                    {{ get_setting('contact_address', null, App::getLocale()) }}</p>
                            </li>
                            <li class="mb-2">
                                <p class="fs-13 text-secondary mb-1">{{ translate('Phone') }}</p>
                                <p class="fs-13 text-soft-light">{{ get_setting('contact_phone') }}</p>
                            </li>
                            <li class="mb-2">
                                <p class="fs-13 text-secondary mb-1">{{ translate('Email') }}</p>
                                <p class="">
                                    <a href="mailto:{{ get_setting('contact_email') }}"
                                        class="fs-13 text-soft-light hov-text-primary">{{ get_setting('contact_email') }}</a>
                                </p>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- My Account -->
                <div class="{{ $col_values }}">
                    <div class="text-center text-sm-left mt-4">
                        <h4 class="fs-14 text-secondary text-uppercase fw-700 mb-3">{{ translate('My Account') }}</h4>
                        <ul class="list-unstyled">
                            @if (Auth::check())
                                <li class="mb-2">
                                    <a class="fs-13 text-soft-light animate-underline-white"
                                        href="{{ route('logout') }}">
                                        {{ translate('Logout') }}
                                    </a>
                                </li>
                            @else
                                <li class="mb-2">
                                    <a class="fs-13 text-soft-light animate-underline-white"
                                        href="{{ route('user.login') }}">
                                        {{ translate('Login') }}
                                    </a>
                                </li>
                            @endif
                            <li class="mb-2">
                                <a class="fs-13 text-soft-light animate-underline-white"
                                    href="{{ route('purchase_history.index') }}">
                                    {{ translate('Order History') }}
                                </a>
                            </li>
                            <li class="mb-2">
                                <a class="fs-13 text-soft-light animate-underline-white"
                                    href="{{ route('wishlists.index') }}">
                                    {{ translate('My Wishlist') }}
                                </a>
                            </li>
                            <li class="mb-2">
                                <a class="fs-13 text-soft-light animate-underline-white"
                                    href="{{ route('orders.track') }}">
                                    {{ translate('Track Order') }}
                                </a>
                            </li>
                            @if (addon_is_activated('affiliate_system'))
                                <li class="mb-2">
                                    <a class="fs-13 text-soft-light animate-underline-white"
                                        href="{{ route('affiliate.apply') }}">
                                        {{ translate('Be an affiliate partner') }}
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <!-- Seller & Delivery Boy -->
                @if (get_setting('vendor_system_activation') == 1 || addon_is_activated('delivery_boy'))
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="text-center text-sm-left mt-4">
                            <!-- Seller -->
                            @if (get_setting('vendor_system_activation') == 1)
                                <h4 class="fs-14 text-secondary text-uppercase fw-700 mb-3">
                                    {{ translate('Seller Zone') }}</h4>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <p class="fs-13 text-soft-light mb-0">
                                            {{ translate('Become A Seller') }}
                                            <a href="{{ route(get_setting('seller_registration_verify') === '1' ? 'shop-reg.verification' : 'shops.create') }}"
                                                class="fs-13 fw-700 text-secondary-base ml-2">{{ translate('Apply Now') }}</a>
                                            <a href="{{ route('shops.create') }}"
                                                class="fs-13 fw-700 text-secondary-base ml-2">{{ translate('Apply Now') }}</a>
                                        </p>
                                    </li>
                                    @guest
                                        <li class="mb-2">
                                            <a class="fs-13 text-soft-light animate-underline-white"
                                                href="{{ route('seller.login') }}">
                                                {{ translate('Login to Seller Panel') }}
                                            </a>
                                        </li>
                                    @endguest
                                    @if (get_setting('seller_app_link'))
                                        <li class="mb-2">
                                            <a class="fs-13 text-soft-light animate-underline-white" target="_blank"
                                                href="{{ get_setting('seller_app_link') }}">
                                                {{ translate('Download Seller App') }}
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            @endif

                            <!-- Delivery Boy -->
                            @if (addon_is_activated('delivery_boy'))
                                <h4 class="fs-14 text-secondary text-uppercase fw-700 mt-4 mb-3">
                                    {{ translate('Delivery Boy') }}</h4>
                                <ul class="list-unstyled">
                                    @guest
                                        <li class="mb-2">
                                            <a class="fs-13 text-soft-light animate-underline-white"
                                                href="{{ route('deliveryboy.login') }}">
                                                {{ translate('Login to Delivery Boy Panel') }}
                                            </a>
                                        </li>
                                    @endguest

                                    @if (get_setting('delivery_boy_app_link'))
                                        <li class="mb-2">
                                            <a class="fs-13 text-soft-light animate-underline-white" target="_blank"
                                                href="{{ get_setting('delivery_boy_app_link') }}">
                                                {{ translate('Download Delivery Boy App') }}
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Accordion Fotter widgets -->
        <div class="d-lg-none bg-transparent">
            <!-- Quick links -->
            <div class="aiz-accordion-wrap ">
                <div class="aiz-accordion-heading container">
                    <button
                        class="aiz-accordion fs-14 text-white bg-transparent">{{ get_setting('widget_one', null, App::getLocale()) }}</button>
                </div>
                <div class="aiz-accordion-panel bg-transparent"
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%)!important;">
                    <div class="container">
                        <ul class="list-unstyled mt-3">
                            @if (get_setting('widget_one_labels', null, App::getLocale()) != null)
                                @foreach (json_decode(get_setting('widget_one_labels', null, App::getLocale()), true) as $key => $value)
                                    @php
                                        $widget_one_links = '';
                                        if (isset(json_decode(get_setting('widget_one_links'), true)[$key])) {
                                            $widget_one_links = json_decode(get_setting('widget_one_links'), true)[
                                                $key
                                            ];
                                        }
                                    @endphp
                                    <li class="mb-2 pb-2 @if (url()->current() == $widget_one_links) active @endif">
                                        <a href="{{ $widget_one_links }}"
                                            class="fs-13 text-soft-light text-sm-secondary animate-underline-white">
                                            {{ $value }}
                                        </a>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Contacts -->
            <div class="aiz-accordion-wrap">
                <div class="aiz-accordion-heading container">
                    <button class="aiz-accordion fs-14 text-white bg-transparent">{{ translate('Contacts') }}</button>
                </div>
                <div class="aiz-accordion-panel bg-transparent"
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%)!important;">
                    <div class="container">
                        <ul class="list-unstyled mt-3">
                            <li class="mb-2">
                                <p class="fs-13 text-secondary mb-1">{{ translate('Address') }}</p>
                                <p class="fs-13 text-soft-light">
                                    {{ get_setting('contact_address', null, App::getLocale()) }}</p>
                            </li>
                            <li class="mb-2">
                                <p class="fs-13 text-secondary mb-1">{{ translate('Phone') }}</p>
                                <p class="fs-13 text-soft-light">{{ get_setting('contact_phone') }}</p>
                            </li>
                            <li class="mb-2">
                                <p class="fs-13 text-secondary mb-1">{{ translate('Email') }}</p>
                                <p class="">
                                    <a href="mailto:{{ get_setting('contact_email') }}"
                                        class="fs-13 text-soft-light hov-text-primary">{{ get_setting('contact_email') }}</a>
                                </p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- My Account -->
            <div class="aiz-accordion-wrap">
                <div class="aiz-accordion-heading container ">
                    <button
                        class="aiz-accordion fs-14 text-white bg-transparent">{{ translate('My Account') }}</button>
                </div>
                <div class="aiz-accordion-panel bg-transparent"
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%)!important;">
                    <div class="container">
                        <ul class="list-unstyled mt-3">
                            @auth
                                <li class="mb-2 pb-2">
                                    <a class="fs-13 text-soft-light text-sm-secondary animate-underline-white"
                                        href="{{ route('logout') }}">
                                        {{ translate('Logout') }}
                                    </a>
                                </li>
                            @else
                                <li class="mb-2 pb-2 {{ areActiveRoutes(['user.login'], ' active') }}">
                                    <a class="fs-13 text-soft-light text-sm-secondary animate-underline-white"
                                        href="{{ route('user.login') }}">
                                        {{ translate('Login') }}
                                    </a>
                                </li>
                            @endauth
                            <li class="mb-2 pb-2 {{ areActiveRoutes(['purchase_history.index'], ' active') }}">
                                <a class="fs-13 text-soft-light text-sm-secondary animate-underline-white"
                                    href="{{ route('purchase_history.index') }}">
                                    {{ translate('Order History') }}
                                </a>
                            </li>
                            <li class="mb-2 pb-2 {{ areActiveRoutes(['wishlists.index'], ' active') }}">
                                <a class="fs-13 text-soft-light text-sm-secondary animate-underline-white"
                                    href="{{ route('wishlists.index') }}">
                                    {{ translate('My Wishlist') }}
                                </a>
                            </li>
                            <li class="mb-2 pb-2 {{ areActiveRoutes(['orders.track'], ' active') }}">
                                <a class="fs-13 text-soft-light text-sm-secondary animate-underline-white"
                                    href="{{ route('orders.track') }}">
                                    {{ translate('Track Order') }}
                                </a>
                            </li>
                            @if (addon_is_activated('affiliate_system'))
                                <li class="mb-2 pb-2 {{ areActiveRoutes(['affiliate.apply'], ' active') }}">
                                    <a class="fs-13 text-soft-light text-sm-secondary animate-underline-white"
                                        href="{{ route('affiliate.apply') }}">
                                        {{ translate('Be an affiliate partner') }}
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Seller -->
            @if (get_setting('vendor_system_activation') == 1)
                <div class="aiz-accordion-wrap ">
                    <div class="aiz-accordion-heading container ">
                        <button
                            class="aiz-accordion fs-14 text-white bg-transparent">{{ translate('Seller Zone') }}</button>
                    </div>
                    <div class="aiz-accordion-panel bg-transparent"
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%)!important;">
                        <div class="container">
                            <ul class="list-unstyled mt-3">
                                <li class="mb-2 pb-2 {{ areActiveRoutes(['shops.create'], ' active') }}">
                                    <p class="fs-13 text-soft-light text-sm-secondary mb-0">
                                        {{ translate('Become A Seller') }}
                                        <a href="{{ route(get_setting('seller_registration_verify') === '1' ? 'shop-reg.verification' : 'shops.create') }}"
                                            class="fs-13 fw-700 text-secondary-base ml-2">{{ translate('Apply Now') }}</a>
                                    </p>
                                </li>
                                @guest
                                    <li class="mb-2 pb-2 {{ areActiveRoutes(['deliveryboy.login'], ' active') }}">
                                        <a class="fs-13 text-soft-light text-sm-secondary animate-underline-white"
                                            href="{{ route('seller.login') }}">
                                            {{ translate('Login to Seller Panel') }}
                                        </a>
                                    </li>
                                @endguest
                                @if (get_setting('seller_app_link'))
                                    <li class="mb-2 pb-2">
                                        <a class="fs-13 text-soft-light text-sm-secondary animate-underline-white"
                                            target="_blank" href="{{ get_setting('seller_app_link') }}">
                                            {{ translate('Download Seller App') }}
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Delivery Boy -->
            @if (addon_is_activated('delivery_boy'))
                <div class="aiz-accordion-wrap bg-black">
                    <div class="aiz-accordion-heading container bg-black">
                        <button
                            class="aiz-accordion fs-14 text-white bg-transparent">{{ translate('Delivery Boy') }}</button>
                    </div>
                    <div class="aiz-accordion-panel bg-transparent"
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%)!important;">
                        <div class="container">
                            <ul class="list-unstyled mt-3">
                                @guest
                                    <li class="mb-2 pb-2 {{ areActiveRoutes(['deliveryboy.login'], ' active') }}">
                                        <a class="fs-13 text-soft-light text-sm-secondary animate-underline-white"
                                            href="{{ route('deliveryboy.login') }}">
                                            {{ translate('Login to Delivery Boy Panel') }}
                                        </a>
                                    </li>
                                @endguest
                                @if (get_setting('delivery_boy_app_link'))
                                    <li class="mb-2 pb-2">
                                        <a class="fs-13 text-soft-light text-sm-secondary animate-underline-white"
                                            target="_blank" href="{{ get_setting('delivery_boy_app_link') }}">
                                            {{ translate('Download Delivery Boy App') }}
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="pt-3 pb-7 pb-xl-3 text-soft-light ">
        <div class="container">
            <div class="row align-items-center py-3">
                <!-- Copyright -->
                <div class="col-lg-6 order-1 order-lg-0">
                    <div class="text-center text-lg-left fs-14"
                        current-verison="{{ get_setting('current_version') }}">
                        {!! get_setting('frontend_copyright_text', null, App::getLocale()) !!}
                    </div>
                </div>

                <!-- Payment Method Images -->
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="text-center text-lg-right">
                        <ul class="list-inline mb-0">
                            @if (get_setting('payment_method_images') != null)
                                @foreach (explode(',', get_setting('payment_method_images')) as $key => $value)
                                    <li class="list-inline-item mr-3">
                                        <img src="{{ uploaded_asset($value) }}" height="20"
                                            class="mw-100 h-auto" style="max-height: 40px"
                                            alt="{{ translate('payment_method') }}">
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <style>
        /* Custom styles for the mobile bottom navigation to match Skybuy/Urban-Mart design */
        .aiz-mobile-bottom-nav {
            background-color: #fff;
            /* White background for the whole bar */
            border-top: 1px solid #eee;
            /* Light border on top */
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            /* Soft shadow for floating effect */
            padding: 0;
            height: 65px;
            /* Fixed height for the bottom bar */
            width: 100%;
            /* Ensure it takes full width */
            max-width: 100%;
            /* Ensure it doesn't exceed screen width */
            left: 0;
            right: 0;
            z-index: 1050;
            /* High z-index to stay on top */
            border-radius: 0;
            /* Keep corners square for full-width bar */

            /* For fixed position centering and no cutting */
            position: fixed;
            bottom: 0;
            margin: auto;
            /* Centers the fixed element horizontally if left/right are 0 */
            box-sizing: border-box;
            /* Include padding and border in the element's total width and height */
            min-width: 320px;
            /* Minimum width to prevent breakage on very small screens */
        }

        .aiz-mobile-bottom-nav .row {
            height: 100%;
            /* Ensure row takes full height of the nav bar */
            flex-wrap: nowrap;
            /* Prevent items from wrapping to next line */
            justify-content: space-between;
            /* Distribute items evenly with space between them */
        }

        .aiz-mobile-bottom-nav .col {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 5px;
            /* Adjust horizontal padding for columns */
            flex-grow: 1;
            /* Allow columns to grow and take equal space */
            flex-shrink: 0;
            /* Prevent columns from shrinking too much */
            max-width: 25%;
            /* Limit individual item width to ensure even distribution */
        }

        .aiz-mobile-bottom-nav .nav-item-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #888;
            /* Default text color */
            padding: 5px 0;
            /* Vertical padding within each item */
            transition: all 0.2s ease-in-out;
            min-width: 50px;
            /* Minimum width for each nav item to prevent collapse */
            text-align: center;
        }

        .aiz-mobile-bottom-nav .nav-item-wrapper:hover {
            color: #3498db;
            /* Hover effect */
        }

        .aiz-mobile-bottom-nav .nav-item-wrapper.svg-active svg path {
            fill: #3498db;
            /* Active icon color */
        }

        .aiz-mobile-bottom-nav .nav-item-wrapper.text-primary {
            color: #3498db !important;
            /* Active text color */
        }

        .aiz-mobile-bottom-nav .nav-item-wrapper svg {
            width: 22px;
            /* Larger icons */
            height: 22px;
            margin-bottom: 2px;
        }

        .aiz-mobile-bottom-nav .nav-item-wrapper span {
            font-size: 11px;
            /* Smaller text size */
            font-weight: 500;
            white-space: nowrap;
            /* Prevent text from wrapping */
            overflow: hidden;
            /* Hide overflowing text */
            text-overflow: ellipsis;
            /* Add ellipsis for overflowing text */
        }

        /* Central FAB Button Styling */
        .mobile-fab-wrapper {
            position: relative;
            width: 70px;
            /* Width for the FAB button area */
            height: 70px;
            /* Height for the FAB button area */
            margin-top: -30px;
            /* Adjust to make it float above the bar */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 1051;
            /* Higher z-index than the bar */
        }

        .mobile-fab-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 55px;
            /* Diameter of the circular button */
            height: 55px;
            background-color: #3498db;
            /* Blue background for FAB */
            border-radius: 50%;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            /* Stronger shadow for FAB */
            transition: all 0.2s ease-in-out;
            text-decoration: none;
            color: #fff;
            /* Icon color inside FAB */
        }

        .mobile-fab-btn:hover {
            background-color: #2980b9;
            /* Darker blue on hover */
            transform: translateY(-2px);
            /* Slight lift on hover */
        }

        .mobile-fab-btn svg {
            width: 26px;
            /* Larger icon inside FAB */
            height: 26px;
        }

        .mobile-fab-btn svg path {
            fill: #fff !important;
            /* Ensure FAB icon is white */
        }

        .mobile-fab-wrapper .fab-label {
            position: absolute;
            bottom: -20px;
            /* Position label below FAB */
            font-size: 11px;
            font-weight: 500;
            color: #888;
            /* Label text color */
            white-space: nowrap;
        }

        .mobile-fab-wrapper .badge {
            position: absolute;
            top: 2px;
            /* Position badge on FAB */
            right: 10px;
            background-color: #e74c3c;
            /* Red badge color */
            color: #fff;
            padding: 3px 6px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: 700;
            line-height: 1;
        }

        .mobile-fab-wrapper .text-primary {
            /* Active text for cart label */
            color: #3498db !important;
        }

        /* Ensure specific SVG colors are set */
        .aiz-mobile-bottom-nav .nav-item-wrapper svg path {
            fill: #b5b5bf;
            /* Default icon color */
        }

        .aiz-mobile-bottom-nav .nav-item-wrapper.svg-active svg path {
            fill: #d43732;
            /* Active icon color, matching Urban-Mart red */
        }

        .mobile-fab-btn svg path {
            fill: #fff !important;
            /* Ensure FAB icon is white */
        }


        /* Specific overrides for Urban-Mart active styles */
        .aiz-mobile-bottom-nav .nav-item-wrapper.active .svg-active path {
            fill: #d43732 !important;
            /* Urban-Mart active red */
        }

        .aiz-mobile-bottom-nav .nav-item-wrapper.active {
            color: #d43732 !important;
            /* Urban-Mart active red */
        }
    </style>

    <div class="aiz-mobile-bottom-nav d-xl-none">
        <div class="row align-items-center h-100">
            <!-- Home -->
            <div class="col">
                <a href="{{ route('home') }}" class="nav-item-wrapper {{ areActiveRoutes(['home'], 'active') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <g id="Group_24768" data-name="Group 24768" transform="translate(3495.144 -602)">
                            <path id="Path_2916" data-name="Path 2916"
                                d="M15.3,5.4,9.561.481A2,2,0,0,0,8.26,0H7.74a2,2,0,0,0-1.3.481L.7,5.4A2,2,0,0,0,0,6.92V14a2,2,0,0,0,2,2H14a2,2,0,0,0,2-2V6.92A2,2,0,0,0,15.3,5.4M10,15H6V9A1,1,0,0,1,7,8H9a1,1,0,0,1,1,1Zm5-1a1,1,0,0,1-1,1H11V9A2,2,0,0,0,9,7H7A2,2,0,0,0,5,9v6H2a1,1,0,0,1-1-1V6.92a1,1,0,0,1,.349-.76l5.74-4.92A1,1,0,0,1,7.74,1h.52a1,1,0,0,1,.651.24l5.74,4.92A1,1,0,0,1,15,6.92Z"
                                transform="translate(-3495.144 602)" fill="#b5b5bf" />
                        </g>
                    </svg>
                    <span>{{ translate('Home') }}</span>
                </a>
            </div>

            <!-- Categories -->
            <div class="col">
                <a href="{{ route('categories.all') }}"
                    class="nav-item-wrapper {{ areActiveRoutes(['categories.all'], 'active') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <g id="Group_25497" data-name="Group 25497" transform="translate(3373.432 -602)">
                            <path id="Path_2917" data-name="Path 2917"
                                d="M126.713,0h-5V5a2,2,0,0,0,2,2h3a2,2,0,0,0,2-2V2a2,2,0,0,0-2-2m1,5a1,1,0,0,1-1,1h-3a1,1,0,0,1-1-1V1h4a1,1,0,0,1,1,1Z"
                                transform="translate(-3495.144 602)" fill="#91919c" />
                            <path id="Path_2918" data-name="Path 2918"
                                d="M144.713,18h-3a2,2,0,0,0-2,2v3a2,2,0,0,0,2,2h5V20a2,2,0,0,0-2-2m1,6h-4a1,1,0,0,1-1-1V20a1,1,0,0,1,1-1h3a1,1,0,0,1,1,1Z"
                                transform="translate(-3504.144 593)" fill="#91919c" />
                            <path id="Path_2919" data-name="Path 2919"
                                d="M143.213,0a3.5,3.5,0,1,0,3.5,3.5,3.5,3.5,0,0,0-3.5-3.5m0,6a2.5,2.5,0,1,1,2.5-2.5,2.5,2.5,0,0,1-2.5,2.5"
                                transform="translate(-3504.144 602)" fill="#91919c" />
                            <path id="Path_2920" data-name="Path 2920"
                                d="M125.213,18a3.5,3.5,0,1,0,3.5,3.5,3.5,3.5,0,0,0-3.5-3.5m0,6a2.5,2.5,0,1,1,2.5-2.5,2.5,2.5,0,0,1-2.5,2.5"
                                transform="translate(-3495.144 593)" fill="#91919c" />
                        </g>
                    </svg>
                    <span>{{ translate('Categories') }}</span>
                </a>
            </div>

            {{-- Central Floating Cart Button --}}
            @if (Auth::check() && auth()->user()->user_type == 'customer')
                @php
                    $count = count(get_user_cart());
                @endphp
                <div class="col-auto d-flex align-items-center justify-content-center" style="padding: 0px;">
                    <div class="mobile-fab-wrapper {{ areActiveRoutes(['cart'], 'active') }}">
                        <a href="{{ route('cart') }}" class="mobile-fab-btn">
                            <svg id="Group_25499" data-name="Group 25499" xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" width="16.001" height="16"
                                viewBox="0 0 16.001 16">
                                <defs>
                                    <clipPath id="clip-pathw">
                                        <rect id="Rectangle_1383" data-name="Rectangle 1383" width="16"
                                            height="16" fill="#91919c" />
                                    </clipPath>
                                </defs>
                                <g id="Group_8095" data-name="Group 8095" transform="translate(0 0)"
                                    clip-path="url(#clip-pathw)">
                                    <path id="Path_2926" data-name="Path 2926"
                                        d="M8,24a2,2,0,1,0,2,2,2,2,0,0,0-2-2m0,3a1,1,0,1,1,1-1,1,1,0,0,1-1,1"
                                        transform="translate(-3 -11.999)" fill="#fff" />
                                    <path id="Path_2927" data-name="Path 2927"
                                        d="M24,24a2,2,0,1,0,2,2,2,2,0,0,0-2-2m0,3a1,1,0,1,1,1-1,1,1,0,0,1-1,1"
                                        transform="translate(-10.999 -11.999)" fill="#fff" />
                                    <path id="Path_2928" data-name="Path 2928"
                                        d="M15.923,3.975A1.5,1.5,0,0,0,14.5,2h-9a.5.5,0,1,0,0,1h9a.507.507,0,0,1,.129.017.5.5,0,0,1,.355.612l-1.581,6a.5.5,0,0,1-.483.372H5.456a.5.5,0,0,1-.489-.392L3.1,1.176A1.5,1.5,0,0,0,1.632,0H.5a.5.5,0,1,0,0,1H1.544a.5.5,0,0,1,.489.392L3.9,9.826A1.5,1.5,0,0,0,5.368,11h7.551a1.5,1.5,0,0,0,1.423-1.026Z"
                                        transform="translate(0 -0.001)" fill="#fff" />
                                </g>
                            </svg>
                            @if ($count > 0)
                                <span class="badge">{{ $count }}</span>
                            @endif
                        </a>
                        <span
                            class="fab-label {{ areActiveRoutes(['cart'], 'text-primary') }}">{{ translate('Cart') }}</span>
                    </div>
                </div>

                <!-- Notifications -->
                <div class="col">
                    <a href="{{ route('customer.all-notifications') }}"
                        class="nav-item-wrapper {{ areActiveRoutes(['customer.all-notifications'], 'active') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13.6" height="16" viewBox="0 0 13.6 16">
                            <path id="ecf3cc267cd87627e58c1954dc6fbcc2"
                                d="M5.488,14.056a.617.617,0,0,0-.8-.016.6.6,0,0,0-.082.855A2.847,2.847,0,0,0,6.835,16h0l.174-.007a2.846,2.846,0,0,0,2.048-1.1h0l.053-.073a.6.6,0,0,0-.134-.782.616.616,0,0,0-.862.081,1.647,1.647,0,0,1-.334.331,1.591,1.591,0,0,1-2.222-.331H5.55ZM6.828,0C4.372,0,1.618,1.732,1.306,4.512h0v1.45A3,3,0,0,1,.6,7.37a.535.535,0,0,0-.057.077A3.248,3.248,0,0,0,0,9.088H0l.021.148a3.312,3.312,0,0,0,.752,2.2,3.909,3.909,0,0,0,2.5,1.232,32.525,32.525,0,0,0,7.1,0,3.865,3.865,0,0,0,2.456-1.232A3.264,3.264,0,0,0,13.6,9.249h0v-.1a3.361,3.361,0,0,0-.582-1.682h0L12.96,7.4a3.067,3.067,0,0,1-.71-1.408h0V4.54l-.039-.081a.612.612,0,0,0-1.132.208h0v1.45a.363.363,0,0,0,0,.077,4.21,4.21,0,0,0,.979,1.957,2.022,2.022,0,0,1,.312,1h0v.155a2.059,2.059,0,0,1-.468,1.373,2.656,2.656,0,0,1-1.661.788,32.024,32.024,0,0,1-6.87,0,2.663,2.663,0,0,1-1.7-.824,2.037,2.037,0,0,1-.447-1.33h0V9.151a2.1,2.1,0,0,1,.305-1.007A4.212,4.212,0,0,0,2.569,6.187a.363.363,0,0,0,0-.077h0V4.653a4.157,4.157,0,0,1,4.2-3.442,4.608,4.608,0,0,1,2.257.584h0l.084.042A.615.615,0,0,0,9.649,1.8.6.6,0,0,0,9.624.739,5.8,5.8,0,0,0,6.828,0Z"
                                fill="#91919b" />
                        </svg>
                        @if (Auth::check() && count(Auth::user()->unreadNotifications) > 0)
                            <span
                                class="badge badge-sm badge-dot badge-circle badge-primary position-absolute absolute-top-right"
                                style="right: 5px;top: -2px;"></span>
                        @endif
                        <span>{{ translate('Notifications') }}</span>
                    </a>
                </div>
            @endif

            <!-- Account -->
            <!-- Account -->
            <div class="col">
                @if (Auth::check())
                    @if (auth()->user()->user_type == 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="nav-item-wrapper">
                            <span>
                                @if (Auth::user()->avatar_original != null)
                                    <img src="{{ uploaded_asset(Auth::user()->avatar_original) }}"
                                        alt="{{ translate('avatar') }}" class="rounded-circle size-20px">
                                @else
                                    <img src="{{ static_asset('assets/img/avatar-place.png') }}"
                                        alt="{{ translate('avatar') }}" class="rounded-circle size-20px">
                                @endif
                            </span>
                            <span>{{ translate('Admin Panel') }}</span>
                        </a>
                    @elseif(auth()->user()->user_type == 'seller')
                        <a href="{{ route('seller.dashboard') }}" class="nav-item-wrapper">
                            <span>
                                @if (Auth::user()->avatar_original != null)
                                    <img src="{{ uploaded_asset(Auth::user()->avatar_original) }}"
                                        alt="{{ translate('avatar') }}" class="rounded-circle size-20px">
                                @else
                                    <img src="{{ static_asset('assets/img/avatar-place.png') }}"
                                        alt="{{ translate('avatar') }}" class="rounded-circle size-20px">
                                @endif
                            </span>
                            <span>{{ translate('Seller Panel') }}</span>
                        </a>
                    @elseif(auth()->user()->user_type == 'wholesaler')
                        <a href="{{ route('dashboard') }}" class="nav-item-wrapper">
                            <span>
                                @if (Auth::user()->avatar_original != null)
                                    <img src="{{ uploaded_asset(Auth::user()->avatar_original) }}"
                                        alt="{{ translate('avatar') }}" class="rounded-circle size-20px">
                                @else
                                    <img src="{{ static_asset('assets/img/avatar-place.png') }}"
                                        alt="{{ translate('avatar') }}" class="rounded-circle size-20px">
                                @endif
                            </span>
                            <span>{{ translate('Wholesaler Panel') }}</span>
                        </a>
                    @else
                        {{-- Customer user --}}
                        <a href="javascript:void(0)" class="nav-item-wrapper mobile-side-nav-thumb"
                            data-toggle="class-toggle" data-backdrop="static" data-target=".aiz-mobile-side-nav">
                            <span>
                                @if (Auth::user()->avatar_original != null)
                                    <img src="{{ uploaded_asset(Auth::user()->avatar_original) }}"
                                        alt="{{ translate('avatar') }}" class="rounded-circle size-20px">
                                @else
                                    <img src="{{ static_asset('assets/img/avatar-place.png') }}"
                                        alt="{{ translate('avatar') }}" class="rounded-circle size-20px">
                                @endif
                            </span>
                            <span>{{ translate('My Account') }}</span>
                        </a>
                    @endif
                @else
                    {{-- Guest user --}}
                    <a href="javascript:void(0)" onclick="openCustomerLogin()" class="nav-item-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                            <g id="Group_8094" data-name="Group 8094" transform="translate(3176 -602)">
                                <path id="Path_2924" data-name="Path 2924"
                                    d="M331.144,0a4,4,0,1,0,4,4,4,4,0,0,0-4-4m0,7a3,3,0,1,1,3-3,3,3,0,0,1-3,3"
                                    transform="translate(-3499.144 602)" fill="#b5b5bf" />
                                <path id="Path_2925" data-name="Path 2925"
                                    d="M332.144,20h-10a3,3,0,0,0,0,6h10a3,3,0,0,0,0-6m0,5h-10a2,2,0,0,1,0-4h10a2,2,0,0,1,0,4"
                                    transform="translate(-3495.144 592)" fill="#b5b5bf" />
                            </g>
                        </svg>
                        <span>{{ translate('Login') }}</span>
                    </a>
                @endif
            </div>





        </div>
    </div>

    @if (Auth::check() && auth()->user()->user_type == 'customer')
        <!-- User Side nav -->
        <div class="aiz-mobile-side-nav collapse-sidebar-wrap sidebar-xl d-xl-none z-1035">
            <div class="overlay dark c-pointer overlay-fixed" data-toggle="class-toggle" data-backdrop="static"
                data-target=".aiz-mobile-side-nav" data-same=".mobile-side-nav-thumb"></div>
            <div class="collapse-sidebar bg-white">
                @include('frontend.inc.user_side_nav')
            </div>
        </div>
    @endif
</section>

