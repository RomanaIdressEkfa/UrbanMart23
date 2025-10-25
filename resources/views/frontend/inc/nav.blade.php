<!-- Main Header Container -->
<header class="@if (get_setting('header_stikcy') == 'on') sticky-top @endif z-1020 shadow-sm">
    <!-- Changed header background to a clean white with a subtle shadow -->
    <div class="bg-white py-2" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container d-flex align-items-center justify-content-between">

            <!-- Left Section: Mobile Menu Toggle & Logo -->
            <div class="d-flex align-items-center">
                <!-- Mobile Menu Button (Hamburger) -->
                <button type="button" class="btn d-lg-none mr-3 mr-sm-4 p-0 active" data-toggle="class-toggle"
                    data-target=".aiz-top-menu-sidebar">
                    <svg id="Component_43_1" data-name="Component 43 – 1" xmlns="http://www.w3.org/2000/svg"
                        width="18" height="18" viewBox="0 0 16 16">
                        <rect id="Rectangle_19062" data-name="Rectangle 19062" width="16" height="2"
                            transform="translate(0 7)" fill="#333" />
                        <rect id="Rectangle_19063" data-name="Rectangle 19063" width="16" height="2"
                            fill="#333" />
                        <rect id="Rectangle_19064" data-name="Rectangle 19064" width="16" height="2"
                            transform="translate(0 14)" fill="#333" />
                    </svg>
                </button>

                <!-- Logo -->
                <a class="d-block py-0" href="{{ route('home') }}">
                    @php
                        $header_logo = get_setting('header_logo');
                    @endphp
                    @if ($header_logo != null)
                        <img src="{{ uploaded_asset($header_logo) }}" alt="{{ env('APP_NAME') }}"
                            class="mw-100 h-40px h-md-50px" height="50">
                    @else
                        <img src="{{ static_asset('assets/img/logo.png') }}" alt="{{ env('APP_NAME') }}"
                            class="mw-100 h-40px h-md-50px" height="50">
                    @endif
                </a>
                <style>
                    /* NEW CSS for Avatar visibility */
                    .nav-user-img {
                        width: 40px;
                        /* Fixed width */
                        height: 40px;
                        /* Fixed height */
                        display: flex;
                        /* Use flexbox for centering image/icon */
                        align-items: center;
                        justify-content: center;
                        border-radius: 50%;
                        /* Make it circular */
                        overflow: hidden;
                        /* Hide overflow if image is too big */
                        background-color: rgba(255, 255, 255, 0.2);
                        /* Light background for default icon */
                        flex-shrink: 0;
                        /* Prevent shrinking on small screens */
                    }

                    .nav-user-img img {
                        width: 100%;
                        height: 100%;
                        object-fit: cover;
                        /* Crop image to fit circle */
                        border-radius: 50%;
                        /* Ensure image itself is circular if it wasn't already */
                    }

                    .nav-user-img svg {
                        width: 24px;
                        /* Default SVG icon size */
                        height: 24px;
                    }


                    img.mw-100.h-40px.h-md-50px {
                        height: 55px;
                    }

                    .container,
                    .container-xxl {
                        max-width: 1500px;
                    }

                    /* User Dropdown Menu - Click Only */
                    .hover-user-top-menu {
                        opacity: 0;
                        visibility: hidden;
                        transition: all 0.3s ease;
                        pointer-events: none;
                        position: absolute;
                        top: 100%;
                        right: 0;
                        z-index: 1000;
                        width: 220px;
                        transform: translateY(-10px);
                    }

                    .user-dropdown-container {
                        position: relative;
                    }

                    .hover-user-top-menu.show-dropdown {
                        opacity: 1 !important;
                        visibility: visible !important;
                        pointer-events: auto !important;
                        transform: translateY(0);
                    }

                    .nav-user-info {
                        border-radius: 8px;
                        transition: all 0.2s ease;
                    }

                    /* Remove hover effects - click only */
                    .nav-user-info:hover {
                        background-color: rgba(255, 255, 255, 0.05);
                    }

                    .cursor-pointer {
                        cursor: pointer;
                    }

                    .aiz-user-top-menu {
                        border: 1px solid #e1e5e9 !important;
                        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
                        border-radius: 8px !important;
                        background: #ffffff !important;
                        overflow: hidden;
                    }

                    .user-top-nav-element a {
                        color: #333 !important;
                        text-decoration: none;
                        transition: all 0.2s ease;
                        padding: 12px 16px !important;
                    }

                    .user-top-nav-element:hover a {
                        background-color: #f8f9fa !important;
                        color: #3498db !important;
                    }

                    .user-top-nav-element:hover svg path {
                        fill: #3498db !important;
                    }

                    .user-top-menu-name {
                        color: inherit !important;
                    }
                </style>
            </div>

            <div class="flex-grow-1 front-header-search d-none d-sm-flex align-items-center mx-3 mx-lg-5">
                <div class="position-relative flex-grow-1">
                    <form action="{{ route('search') }}" method="GET" class="stop-propagation">
                        <div class="d-flex position-relative align-items-center">
                            <!-- Mobile Search Back Button -->
                            <div class="d-lg-none" data-toggle="class-toggle" data-target=".front-header-search">
                                <button class="btn px-2" type="button"><i
                                        class="la la-2x la-long-arrow-left"></i></button>
                            </div>
                            <!-- Search Input -->
                            <div class="search-input-box w-100">
                                <input type="text" class="form-control fs-14 h-40px rounded-pill pl-4 pr-5 border-0 "
                                    id="search" name="keyword"
                                    @isset($query) value="{{ $query }}" @endisset
                                    placeholder="{{ translate('I am shopping for...') }}" autocomplete="off">
                                <!-- Search Icon - now a submit button -->
                                <button type="submit" class="search-icon position-absolute border-0 bg-transparent p-2"
                                    style="top: 50%; right: 8px; transform: translateY(-50%); z-index: 10;">
                                    <svg id="Group_723" data-name="Group 723" xmlns="http://www.w3.org/2000/svg"
                                        width="18" height="18" viewBox="0 0 20.001 20">
                                        <path id="Path_3090" data-name="Path 3090"
                                            d="M9.847,17.839a7.993,7.993,0,1,1,7.993-7.993A8,8,0,0,1,9.847,17.839Zm0-14.387a6.394,6.394,0,1,0,6.394,6.394A6.4,6.4,0,0,0,9.847,3.453Z"
                                            transform="translate(-1.854 -1.854)" fill="#91919b" />
                                        <path id="Path_3091" data-name="Path 3091"
                                            d="M24.4,25.2a.8.8,0,0,1-.565-.234l-6.15-6.15a.8.8,0,0,1,1.13-1.13l6.15,6.15A.8.8,0,0,1,24.4,25.2Z"
                                            transform="translate(-5.2 -5.2)" fill="#91919b" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </form>
                    <!-- Search Results Dropdown -->
                    <div class="typed-search-box stop-propagation document-click-d-none d-none bg-white rounded shadow-lg position-absolute left-0 top-100 w-100"
                        style="min-height: 200px">
                        <div class="search-preloader absolute-top-center">
                            <div class="dot-loader">
                                <div></div>
                                <div></div>
                                <div></div>
                            </div>
                        </div>
                        <div class="search-nothing d-none p-3 text-center fs-16"></div>
                        <div id="search-content" class="text-left"></div>
                    </div>
                </div>
            </div>
            <style>
                @media (min-width: 992px) {
                    /* .mr-lg-5, .mx-lg-5 {
                        margin-right: 18rem !important;
                    } */
                }

                @media (min-width: 992px) {

                    .ml-lg-5,
                    .mx-lg-5 {
                        margin-left: 8rem !important;
                    }
                }
            </style>

            <!-- Right Section: Utility Links & User Actions -->
            <div class="d-flex align-items-center ml-auto">
                <!-- Mobile Search Icon (only visible on small screens to activate search bar) -->
                <div class="d-sm-none mr-2">
                    <a class="p-2 d-block text-reset" href="javascript:void(0);" data-toggle="class-toggle"
                        data-target=".front-header-search">
                        <i class="las la-search la-flip-horizontal la-2x"></i>
                    </a>
                </div>

                <!-- Cart Icon -->
                <div class="d-none d-xl-block has-transition" data-hover="dropdown">
                    <div class="nav-cart-box dropdown h-100" id="cart_items">
                        {{-- Mohammad Hassan --}}
                        @include('frontend.partials.cart.cart')
                    </div>
                </div>


                <!-- User Account/Auth Links - Show for all users -->
                <div class="ml-3 d-none d-xl-flex align-items-center position-relative user-dropdown-container">
                    @auth
                        <span class="d-flex align-items-center nav-user-info py-20px px-3 cursor-pointer" id="nav-user-info"
                            onclick="toggleUserDropdown()">
                            <span class="size-40px rounded-circle overflow-hidden border border-transparent nav-user-img">
                                @if (auth()->user()->avatar_original != null)
                                    <img src="{{ uploaded_asset(auth()->user()->avatar_original) }}" class="img-fit h-100"
                                        alt="{{ translate('avatar') }}"
                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                                @else
                                    <img src="{{ static_asset('assets/img/avatar-place.png') }}" class="image"
                                        alt="{{ translate('avatar') }}"
                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                                @endif
                            </span>
                            <div class="ml-2">
                                @if (auth()->user()->name)
                                    <h4 class="h5 fs-14 fw-700 text-white mb-0">{{ auth()->user()->name }}</h4>
                                    <small class="fs-12 text-white-50">{{ auth()->user()->email }}</small>
                                @else
                                    <h4 class="h5 fs-14 fw-700 text-white mb-0">{{ auth()->user()->email }}</h4>
                                    <small class="fs-12 text-white-50">{{ translate('No Name') }}</small>
                                @endif
                            </div>
                        </span>
                        <!-- User Dropdown Menu on Click - For authenticated users -->
                        {{-- এই অংশটি সরানো হয়েছে এবর করা হয়েছে --}}
                        <div class="hover-user-top-menu position-absolute top-100 right-0 z-3"> {{-- 'left-0' সরানো হয়েছে
                            --}}
                            <div class="aiz-user-top-menu bg-white rounded-0 border-top shadow-sm"> {{-- inline style
                                width:220px সরানো হবে CSS থেকে --}}
                                <ul class="list-unstyled no-scrollbar mb-0 text-left">
                                    <li class="user-top-nav-element border border-top-0" data-id="1">
                                        @php
                                            $dashboardRoute = 'dashboard'; // Default customer dashboard
                                            if (auth()->user()->user_type == 'admin') {
                                                $dashboardRoute = 'admin.dashboard';
                                            } elseif (auth()->user()->user_type == 'seller') {
                                                $dashboardRoute = 'seller.dashboard';
                                            } elseif (auth()->user()->user_type == 'wholesaler') {
                                                $dashboardRoute = 'dashboard';
                                            }
                                        @endphp
                                        <a href="{{ route($dashboardRoute) }}"
                                            class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 16 16">
                                                <path id="Path_2916" data-name="Path 2916"
                                                    d="M15.3,5.4,9.561.481A2,2,0,0,0,8.26,0H7.74a2,2,0,0,0-1.3.481L.7,5.4A2,2,0,0,0,0,6.92V14a2,2,0,0,0,2,2H14a2,2,0,0,0,2-2V6.92A2,2,0,0,0,15.3,5.4M10,15H6V9A1,1,0,0,1,7,8H9a1,1,0,0,1,1,1Zm5-1a1,1,0,0,1-1,1H11V9A2,2,0,0,0,9,7H7A2,2,0,0,0,5,9v6H2a1,1,0,0,1-1-1V6.92a1,1,0,0,1,.349-.76l5.74-4.92A1,1,0,0,1,7.74,1h.52a1,1,0,0,1,.651.24l5.74,4.92A1,1,0,0,1,15,6.92Z"
                                                    fill="#b5b5bf" />
                                            </svg>
                                            <span
                                                class="user-top-menu-name has-transition ml-3">{{ translate('Dashboard') }}</span>
                                        </a>
                                    </li>


                                    <li class="user-top-nav-element border border-top-0" data-id="1">
                                        <a href="{{ route('logout') }}"
                                            class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15.999"
                                                viewBox="0 0 16 15.999">
                                                <g id="Group_25503" data-name="Group 25503"
                                                    transform="translate(-24.002 -377)">
                                                    <g id="Group_25265" data-name="Group 25265"
                                                        transform="translate(-216.534 -160)">
                                                        <path id="Subtraction_192" data-name="Subtraction 192"
                                                            d="M12052.535,2920a8,8,0,0,1-4.569-14.567l.721.72a7,7,0,1,0,7.7,0l.721-.72a8,8,0,0,1-4.567,14.567Z"
                                                            transform="translate(-11803.999 -2367)" fill="#d43533" />
                                                    </g>
                                                    <rect id="Rectangle_19022" data-name="Rectangle 19022" width="1"
                                                        height="8" rx="0.5" transform="translate(31.5 377)"
                                                        fill="#d43533" />
                                                </g>
                                            </svg>
                                            <span
                                                class="user-top-menu-name text-primary has-transition ml-3">{{ translate('Logout') }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @else
                        <span class="d-flex align-items-center nav-user-info">
                            <span
                                class="size-40px rounded-circle overflow-hidden border d-flex align-items-center justify-content-center nav-user-img bg-light">
                                <svg xmlns="http://www.w3.org/2000/svg" width="19.902" height="20.012"
                                    viewBox="0 0 19.902 20.012">
                                    <path id="fe2df171891038b33e9624c27e96e367"
                                        d="M15.71,12.71a6,6,0,1,0-7.42,0,10,10,0,0,0-6.22,8.18,1.006,1.006,0,1,0,2,.22,8,8,0,0,1,15.9,0,1,1,0,0,0,1,.89h.11a1,1,0,0,0,.88-1.1,10,10,0,0,0-6.25-8.19ZM12,12a4,4,0,1,1,4-4A4,4,0,0,1,12,12Z"
                                        transform="translate(-2.064 -1.995)" fill="#91919b" />
                                </svg>
                            </span>
                            <a href="javascript:void(0)" onclick="openCustomerLogin()"
                                class="text-reset hov-text-primary fs-14 d-inline-block border-soft-light border-width-2 pr-2 ml-3"
                                style="color: #FFFFFF!important; font-weight: 700;">{{ translate('Login') }}</a>
                            <!-- Mohammad Hassan -->
                        </span>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- User Dropdown Menu on Click - For authenticated users -->
    @if (Auth::check())
        <div class="hover-user-top-menu position-absolute top-100 left-0 right-0 z-3">
            <div class="container">
                <div class="position-static float-right">
                    <div class="aiz-user-top-menu bg-white rounded-0 border-top shadow-sm" style="width:220px;">
                        <ul class="list-unstyled no-scrollbar mb-0 text-left">
                            <li class="user-top-nav-element border border-top-0" data-id="1">
                                @php
                                    $dashboardRoute = 'dashboard'; // Default customer dashboard
                                    if (auth()->user()->user_type == 'admin') {
                                        $dashboardRoute = 'admin.dashboard';
                                    } elseif (auth()->user()->user_type == 'seller') {
                                        $dashboardRoute = 'seller.dashboard';
                                    } elseif (auth()->user()->user_type == 'wholesaler') {
                                        $dashboardRoute = 'dashboard';
                                    }
                                @endphp
                                <a href="{{ route($dashboardRoute) }}"
                                    class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 16 16">
                                        <path id="Path_2916" data-name="Path 2916"
                                            d="M15.3,5.4,9.561.481A2,2,0,0,0,8.26,0H7.74a2,2,0,0,0-1.3.481L.7,5.4A2,2,0,0,0,0,6.92V14a2,2,0,0,0,2,2H14a2,2,0,0,0,2-2V6.92A2,2,0,0,0,15.3,5.4M10,15H6V9A1,1,0,0,1,7,8H9a1,1,0,0,1,1,1Zm5-1a1,1,0,0,1-1,1H11V9A2,2,0,0,0,9,7H7A2,2,0,0,0,5,9v6H2a1,1,0,0,1-1-1V6.92a1,1,0,0,1,.349-.76l5.74-4.92A1,1,0,0,1,7.74,1h.52a1,1,0,0,1,.651.24l5.74,4.92A1,1,0,0,1,15,6.92Z"
                                            fill="#b5b5c0" />
                                    </svg>
                                    <span
                                        class="user-top-menu-name has-transition ml-3">{{ translate('Dashboard') }}</span>
                                </a>
                            </li>


                            <li class="user-top-nav-element border border-top-0" data-id="1">
                                <a href="{{ route('logout') }}"
                                    class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15.999"
                                        viewBox="0 0 16 15.999">
                                        <g id="Group_25503" data-name="Group 25503"
                                            transform="translate(-24.002 -377)">
                                            <g id="Group_25265" data-name="Group 25265"
                                                transform="translate(-216.534 -160)">
                                                <path id="Subtraction_192" data-name="Subtraction 192"
                                                    d="M12052.535,2920a8,8,0,0,1-4.569-14.567l.721.72a7,7,0,1,0,7.7,0l.721-.72a8,8,0,0,1-4.567,14.567Z"
                                                    transform="translate(-11803.999 -2367)" fill="#d43533" />
                                            </g>
                                            <rect id="Rectangle_19022" data-name="Rectangle 19022" width="1"
                                                height="8" rx="0.5" transform="translate(31.5 377)"
                                                fill="#d43533" />
                                        </g>
                                    </svg>
                                    <span
                                        class="user-top-menu-name text-primary has-transition ml-3">{{ translate('Logout') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif
</header>

<!-- Mobile Off-canvas Sidebar -->
<div class="aiz-top-menu-sidebar collapse-sidebar-wrap sidebar-xl sidebar-left d-lg-none z-1035">
    <div class="overlay overlay-fixed dark c-pointer" data-toggle="class-toggle" data-target=".aiz-top-menu-sidebar"
        data-same=".hide-top-menu-bar"></div>
    <div class="collapse-sidebar c-scrollbar-light text-left">
        <button type="button" class="btn btn-sm p-4 hide-top-menu-bar" data-toggle="class-toggle"
            data-target=".aiz-top-menu-sidebar">
            <i class="las la-times la-2x text-primary"></i>
        </button>
        <!-- User Info / Auth Links for Mobile Sidebar - For all users -->
        <div class="py-3 px-4 bg-light mb-3">
            @auth
                <span class="d-flex align-items-center nav-user-info">
                    <span class="size-40px rounded-circle overflow-hidden border border-transparent nav-user-img">
                        @if (auth()->user()->avatar_original != null)
                            <img src="{{ uploaded_asset(auth()->user()->avatar_original) }}" class="img-fit h-100"
                                alt="{{ translate('avatar') }}"
                                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                        @else
                            <img src="{{ static_asset('assets/img/avatar-place.png') }}" class="image"
                                alt="{{ translate('avatar') }}"
                                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                        @endif
                    </span>
                    <div class="ml-2">
                        @if (auth()->user()->name)
                            <h4 class="h5 fs-14 fw-700 text-dark mb-0">{{ auth()->user()->name }}</h4>
                            <small class="fs-12 text-muted">{{ auth()->user()->email }}</small>
                        @else
                            <h4 class="h5 fs-14 fw-700 text-dark mb-0">{{ auth()->user()->email }}</h4>
                            <small class="fs-12 text-muted">{{ translate('No Name') }}</small>
                        @endif
                    </div>
                </span>
            @else
                <span class="d-flex align-items-center nav-user-info">
                    <span
                        class="size-40px rounded-circle overflow-hidden border d-flex align-items-center justify-content-center nav-user-img bg-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="19.902" height="20.012"
                            viewBox="0 0 19.902 20.012">
                            <path id="fe2df171891038b33e9624c27e96e367"
                                d="M15.71,12.71a6,6,0,1,0-7.42,0,10,10,0,0,0-6.22,8.18,1.006,1.006,0,1,0,2,.22,8,8,0,0,1,15.9,0,1,1,0,0,0,1,.89h.11a1,1,0,0,0,.88-1.1,10,10,0,0,0-6.25-8.19ZM12,12a4,4,0,1,1,4-4A4,4,0,0,1,12,12Z"
                                transform="translate(-2.064 -1.995)" fill="#91919b" />
                        </svg>
                    </span>
                    <a href="javascript:void(0)" onclick="openCustomerLogin()"
                        class="text-reset opacity-60 hov-opacity-100 hov-text-primary fs-12 d-inline-block border-right border-soft-light border-width-2 pr-2 ml-3">{{ translate('Login') }}</a>
                    <!-- Mohammad Hassan -->
                    {{-- <a href="{{ route(get_setting('customer_registration_verify') === '1' ? 'registration.verification' : 'user.registration') }}"
                        class="text-reset opacity-60 hov-opacity-100 hov-text-primary fs-12 d-inline-block py-2 pl-2">{{ translate('Registration') }}</a> --}}

                    @if (get_setting('show_language_switcher') == 'on')
                        <div class="dropdown ml-5" id="lang-change-mobile">
                            <a href="javascript:void(0)" class="text-secondary p-2" data-toggle="dropdown">
                                <img src="{{ static_asset('assets/img/flags/' . $system_language->code . '.png') }}"
                                    alt="{{ $system_language->name }}" height="16">
                            </a>
                            <ul class="dropdown-menu dropdown-menu-left">
                                @foreach (get_all_active_language() as $key => $language)
                                    <li>
                                        <a href="javascript:void(0)" data-flag="{{ $language->code }}"
                                            class="dropdown-item @if ($system_language->code == $language->code) active @endif">
                                            <img src="{{ static_asset('assets/img/flags/' . $language->code . '.png') }}"
                                                class="mr-1" alt="{{ $language->name }}" height="11">
                                            <span class="language">{{ $language->name }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (get_setting('show_currency_switcher') == 'on')
                        <div class="dropdown mr-2" id="currency-change-mobile">
                            @php
                                $system_currency = get_system_currency();
                            @endphp
                            <a href="javascript:void(0)" class="text-secondary p-2" data-toggle="dropdown">
                                {{ $system_currency->symbol }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-left">
                                @foreach (get_all_active_currency() as $key => $currency)
                                    <li>
                                        <a class="dropdown-item @if ($system_currency->code == $currency->code) active @endif"
                                            href="javascript:void(0)" data-currency="{{ $currency->code }}">
                                            {{ $currency->symbol }} ({{ $currency->code }})
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </span>
            @endauth
        </div>
        <hr>
        <ul class="mb-0 pl-3 pb-3 h-100">
            @if (get_setting('header_menu_labels') != null)
                @foreach (json_decode(get_setting('header_menu_labels'), true) as $key => $value)
                    <li class="mr-0">
                        <a href="{{ json_decode(get_setting('header_menu_links'), true)[$key] }}"
                            class="fs-13 px-3 py-3 w-100 d-inline-block fw-700 text-dark header_menu_links
                                @if (url()->current() == json_decode(get_setting('header_menu_links'), true)[$key]) active @endif">
                            {{ translate($value) }}
                        </a>
                    </li>
                @endforeach
            @endif
            @if (Auth::check())
                <hr>
                <li class="mr-0">
                    @php
                        $dashboardRoute = 'dashboard'; // Default customer dashboard
                        $accountLabel = 'My Account';
                        if (auth()->user()->user_type == 'admin') {
                            $dashboardRoute = 'admin.dashboard';
                            $accountLabel = 'Admin Dashboard';
                        } elseif (auth()->user()->user_type == 'seller') {
                            $dashboardRoute = 'seller.dashboard';
                            $accountLabel = 'Seller Dashboard';
                        } elseif (auth()->user()->user_type == 'wholesaler') {
                            $dashboardRoute = 'dashboard';
                        }
                    @endphp
                    <a href="{{ route($dashboardRoute) }}"
                        class="fs-13 px-3 py-3 w-100 d-inline-block fw-700 text-dark header_menu_links
                            {{ areActiveRoutes([$dashboardRoute], ' active') }}">
                        {{ translate($accountLabel) }}
                    </a>
                </li>

                <li class="mr-0">
                    <a href="{{ route('logout') }}"
                        class="fs-13 px-3 py-3 w-100 d-inline-block fw-700 text-primary header_menu_links">
                        {{ translate('Logout') }}
                    </a>
                </li>
            @endif
        </ul>
        <br>
        <br>
    </div>
</div>

<div class="modal fade" id="order_details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div id="order-details-modal-body">

            </div>
        </div>
    </div>
</div>

<!-- Include Auth Modals -->
{{-- Mohammad Hassan - Removed deprecated auth.modals include --}}

@section('script')
    <script type="text/javascript">
        function show_order_details(order_id) {
            $('#order-details-modal-body').html(null);

            if (!$('#modal-size').hasClass('modal-lg')) {
                $('#modal-size').addClass('modal-lg');
            }

            $.post('{{ route('orders.details') }}', {
                _token: AIZ.data.csrf,
                order_id: order_id
            }, function(data) {
                $('#order-details-modal-body').html(data);
                $('#order_details').modal();
                $('.c-preloader').hide();
                AIZ.plugins.bootstrapSelect('refresh');
            });
        }

        // Close dropdown when clicking outside
        $(document).ready(function() {
            $(document).on('click', function(event) {
                if (!$(event.target).closest('.user-dropdown-container').length) {
                    $('.hover-user-top-menu').removeClass('show-dropdown');
                }
            });

            // Prevent dropdown from closing when clicking inside it
            $('.hover-user-top-menu').on('click', function(event) {
                event.stopPropagation();
            });
        });
    </script>
@endsection
