<!DOCTYPE html>

@php
    $rtl = get_session_language()->rtl;
@endphp

@if ($rtl == 1)
    <html dir="rtl" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@else
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@endif

<head>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ getBaseURL() }}">
    <meta name="file-base-url" content="{{ getFileBaseURL() }}">

    <title>@yield('meta_title', get_setting('website_name') . ' | ' . get_setting('site_motto'))</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">
    <meta name="description" content="@yield('meta_description', get_setting('meta_description'))" />
    <meta name="keywords" content="@yield('meta_keywords', get_setting('meta_keywords'))">

    @yield('meta')

    @if (!isset($detailedProduct) && !isset($customer_product) && !isset($shop) && !isset($page) && !isset($blog))
        @php
            $meta_image = uploaded_asset(get_setting('meta_image'));
        @endphp
        <!-- Schema.org markup for Google+ -->
        <meta itemprop="name" content="{{ get_setting('meta_title') }}">
        <meta itemprop="description" content="{{ get_setting('meta_description') }}">
        <meta itemprop="image" content="{{ $meta_image }}">

        <!-- Twitter Card data -->
        <meta name="twitter:card" content="product">
        <meta name="twitter:site" content="@publisher_handle">
        <meta name="twitter:title" content="{{ get_setting('meta_title') }}">
        <meta name="twitter:description" content="{{ get_setting('meta_description') }}">
        <meta name="twitter:creator" content="@author_handle">
        <meta name="twitter:image" content="{{ $meta_image }}">

        <!-- Open Graph data -->
        <meta property="og:title" content="{{ get_setting('meta_title') }}" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="{{ route('home') }}" />
        <meta property="og:image" content="{{ $meta_image }}" />
        <meta property="og:description" content="{{ get_setting('meta_description') }}" />
        <meta property="og:site_name" content="{{ env('APP_NAME') }}" />
        <meta property="fb:app_id" content="{{ env('FACEBOOK_PIXEL_ID') }}">
    @endif

    <!-- Favicon -->
    @php
        $site_icon = uploaded_asset(get_setting('site_icon'));
    @endphp
    <link rel="icon" href="{{ $site_icon }}">
    <link rel="apple-touch-icon" href="{{ $site_icon }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ static_asset('assets/css/vendors.css') }}">
    @if ($rtl == 1)
        <link rel="stylesheet" href="{{ static_asset('assets/css/bootstrap-rtl.min.css') }}">
    @endif
    <link rel="stylesheet" href="{{ static_asset('assets/css/aiz-core.css?v=') }}{{ rand(1000, 9999) }}">
    <link rel="stylesheet" href="{{ static_asset('assets/css/custom-style.css') }}">

    <script>
        var AIZ = AIZ || {};
        AIZ.local = {
            nothing_selected: '{!! translate('Nothing selected', null, true) !!}',
            nothing_found: '{!! translate('Nothing found', null, true) !!}',
            choose_file: '{{ translate('Choose file') }}',
            file_selected: '{{ translate('File selected') }}',
            files_selected: '{{ translate('Files selected') }}',
            add_more_files: '{{ translate('Add more files') }}',
            adding_more_files: '{{ translate('Adding more files') }}',
            drop_files_here_paste_or: '{{ translate('Drop files here, paste or') }}',
            browse: '{{ translate('Browse') }}',
            upload_complete: '{{ translate('Upload complete') }}',
            upload_paused: '{{ translate('Upload paused') }}',
            resume_upload: '{{ translate('Resume upload') }}',
            pause_upload: '{{ translate('Pause upload') }}',
            retry_upload: '{{ translate('Retry upload') }}',
            cancel_upload: '{{ translate('Cancel upload') }}',
            uploading: '{{ translate('Uploading') }}',
            processing: '{{ translate('Processing') }}',
            complete: '{{ translate('Complete') }}',
            file: '{{ translate('File') }}',
            files: '{{ translate('Files') }}',
        }
    </script>

    <style>
        :root{
            --blue: #3490f3;
            --hov-blue: #2e7fd6;
            --soft-blue: rgba(0, 123, 255, 0.15);
            --secondary-base: {{ get_setting('secondary_base_color', '#ffc519') }};
            --hov-secondary-base: {{ get_setting('secondary_base_hov_color', '#dbaa17') }};
            --soft-secondary-base: {{ hex2rgba(get_setting('secondary_base_color', '#ffc519'), 0.15) }};
            --gray: #9d9da6;
            --gray-dark: #8d8d8d;
            --secondary: #919199;
            --soft-secondary: rgba(145, 145, 153, 0.15);
            --success: #85b567;
            --soft-success: rgba(133, 181, 103, 0.15);
            --warning: #f3af3d;
            --soft-warning: rgba(243, 175, 61, 0.15);
            --light: #f5f5f5;
            --soft-light: #dfdfe6;
            --soft-white: #b5b5bf;
            --dark: #292933;
            --soft-dark: #1b1b28;
            --primary: {{ get_setting('base_color', '#d43533') }};
            --hov-primary: {{ get_setting('base_hov_color', '#9d1b1a') }};
            --soft-primary: {{ hex2rgba(get_setting('base_color', '#d43533'), 0.15) }};
        }
        body{
            font-family: Jost,sans-serif!important;
            font-weight: 400;
        }

        .pagination .page-link,
        .page-item.disabled .page-link {
            min-width: 32px;
            min-height: 32px;
            line-height: 32px;
            text-align: center;
            padding: 0;
            border: 1px solid var(--soft-light);
            font-size: 0.875rem;
            border-radius: 0 !important;
            color: var(--dark);
        }
        .pagination .page-item {
            margin: 0 5px;
        }

        .form-control:focus {
            border-width: 2px !important;
        }
        .iti__flag-container {
            padding: 2px;
        }
        .modal-content {
            border: 0 !important;
            border-radius: 0 !important;
        }

        .tagify.tagify--focus{
            border-width: 2px;
            border-color: var(--primary);
        }

        #map{
            width: 100%;
            height: 250px;
        }
        #edit_map{
            width: 100%;
            height: 250px;
        }

        .pac-container { z-index: 100000; }

/* My Own Style */
        :root {
            --bg-color: #EDE8F5;
            --card-bg-color: #ffffff;
            --primary-dark-blue: #3D52A0;
            --primary-medium-blue: #7091E6;
            --text-muted-blue: #8697C4;
            --border-light-blue: #ADBBDA;
            --font-family: 'Inter', sans-serif;
            /* Skybuybd specific colors */
            --skybuy-blue: #3D52A0;
            /* Dark blue from header */
            --skybuy-green: #38C172;
            /* Example accent color for parachute */
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
           font-family: 'Arial', sans-serif;
            background: #f5f5f5;
            overflow-x: hidden;
            transition: padding-bottom 0.3s ease-in-out;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #7091E6 0%, #8697C4 50%, #ADBBDA 100%);
            color: white;
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        .logo {
            display: flex;
            align-items: center;
            font-size: 28px;
            font-weight: bold;
            flex-shrink: 0;
        }

        .logo::before {
            content: none;
        }

        .logo img {
            height: 50px;
            margin-right: 10px;
        }

        .search-container {
            flex: 1;
            max-width: 600px;
            margin: 0 40px;
            position: relative;
        }

        .search-box {
            width: 100%;
            padding: 15px 60px 15px 25px;
            border: none;
            border-radius: 30px;
            font-size: 16px;
            outline: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        .search-btn {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            color: #333;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 18px;
            left: 10px;
            right: auto;
            color: #999;
            font-size: 20px;
        }

        .search-container input.search-box {
            padding-left: 70px;
            padding-right: 20px;
        }

        .search-container::after {
            content: "🔍";
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #555;
            font-size: 18px;
            pointer-events: none;
        }


        .header-icons {
            display: flex;
            gap: 25px;
            align-items: center;
        }

        .header-icons span {
            font-size: 24px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .header-icons span:hover {
            transform: scale(1.1);
        }

        /* Main Container */
        .main-container {
            margin: 0 auto;
            display: flex;
            min-height: calc(100vh - 80px);
            width: 100%;
        }



        /* Right Content Area */
        .right-content {
            flex: 1;
            padding: 5px;
            overflow-y: auto;
        }

        .hero-section {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            height: 460px;
        }

        /* --- Left Side: Slider --- */
        .slider-container {
            flex: 2;
            position: relative;
            overflow: hidden;
            border-radius: 15px;
            height: 100%;
        }

        .slider {
            display: flex;
            height: 100%;
            transition: transform 0.8s ease-in-out;
        }

        .slide {
            min-width: 100%;
            height: 100%;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            /* padding: 30px 40px; */
            color: white;
            flex-shrink: 0;
        }

        /* .slide.slide-2 {
            background: linear-gradient(110deg, #F87171, #FBBF24, #A7F3D0);
        }

        .slide.slide-1 {
            background: linear-gradient(110deg, #60A5FA, #34D399);
        }

        .slide.slide-3 {
            background: linear-gradient(110deg, #A78BFA, #F472B6);
        } */

        .slide-content {
            z-index: 2;
        }

        .slide h2 {
            font-size: 38px;
            font-weight: bold;
            margin-bottom: 10px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.4);
        }

        .slide p {
            font-size: 18px;
            opacity: 0.95;
        }

        .slide-graphic {
            flex-shrink: 0;
            width: 40%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .slider-nav {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
        }

        .nav-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: background 0.3s;
        }

        .nav-dot.active {
            background: white;
        }

        .phone-display {
            width: 150px;
            height: 280px;
            background: #2d3748;
            border-radius: 20px;
            padding: 10px;
            transform: rotate(15deg);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            z-index: 1;
        }

        .phone-screen {
            width: 100%;
            height: 100%;
            background: white;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            text-align: center;
            color: #333;
            padding: 10px;
        }

        .phone-screen strong {
            font-size: 14px;
            margin-bottom: 4px;
        }

        .float-element {
            position: absolute;
            border-radius: 50%;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            animation: float 4s ease-in-out infinite;
        }

        .float-1 {
            top: 15%;
            right: 0;
            width: 45px;
            height: 45px;
            animation-delay: 0s;
        }

        .float-2 {
            top: 60%;
            right: 15%;
            width: 35px;
            height: 35px;
            animation-delay: 1.5s;
        }

        .float-3 {
            bottom: 20%;
            right: 5%;
            width: 40px;
            height: 40px;
            animation-delay: 0.5s;
        }

        @keyframes float {
            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }
            50% {
                transform: translateY(-20px) rotate(10deg);
            }
        }

        .login-cards-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 20px;
            height: 100%;
        }

        .login-card {
            flex: 1;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .login-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .user-login {
          background: linear-gradient(135deg, #7c71f8, #b116f9);

        }

        .wholesaler-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .login-card h3 {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .login-card p {
            font-size: 14px;
            opacity: 0.9;
            max-width: 250px;
        }

        .login-card .icon {
            margin-bottom: 15px;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.15);
        }
        .featured-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(227px, 1fr));
            gap: 20px;
        }

        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 1px solid #f0f0f0;
            text-decoration: none;
            color: inherit;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .product-image {
            width: 100%;
            height: 210px;
            background: linear-gradient(45deg, #f0f0f0, #e0e0e0);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            position: relative;
            overflow: hidden; /* Ensure image doesn't overflow */
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover; 
            border-radius: 8px; 
        }

        .product-info {
            padding: 15px;
        }

        .product-title {
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
            font-size: 14px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .product-price {
            color: #E53E3E;
            font-weight: bold;
            font-size: 16px;
            display: flex;
            align-items: center;
        }

        .product-sold {
            color: #666;
            font-size: 11px;
            margin-left: 8px;
        }

        .footer {
            background: #1A365D;
            color: white;
            padding: 40px 0 20px;
            margin-top: 40px;
        }

        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .footer-section h4 {
            font-size: 18px;
            margin-bottom: 15px;
            color: #FFFFFF;
        }

        .footer-section p,
        .footer-section a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            margin-bottom: 8px;
            display: block;
        }

        .footer-section a:hover {
            color: white;
        }

        .footer-bottom {
            text-align: center;
            /* padding: 20px 0; */
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 30px;
            color: rgba(255, 255, 255, 0.6);
        }

        /* --- Products Section (LADIES BAGS) --- */
.products-section .product-card {
    background: #f9f9f9;
    border-radius: 12px;
    padding: 0; /* Changed: Remove internal padding from the card */
    transition: all 0.3s;
    text-decoration: none;
    color: inherit;
    position: relative;
    overflow: hidden;
}

.products-section .product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.products-section .product-image {
    width: 100%;
    height: 210px;
    background: #ddd;
    border-radius: 8px;
    margin-bottom: 12px;
    position: relative;
    overflow: hidden;
}

.products-section .product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.discount-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #ff4757;
    color: white;
    padding: 4px 8px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: bold;
}

.products-section .product-name {
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

.products-section .product-price {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
    padding: 0 15px;
}

.current-price {
    font-size: 16px;
    font-weight: bold;
    color: #e74c3c;
}

.original-price {
    font-size: 14px;
    color: #95a5a6;
    text-decoration: line-through;
}

.products-section .product-meta {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    color: #7f8c8d;
    padding: 0 15px 15px; /* Added: horizontal and bottom padding for meta */
}

        /* Mobile specific horizontal category list (from left-sidebar) */
        .mobile-categories-scroll {
            display: none;
            /* Will be hidden by default on mobile */
            background: white;
            border-radius: 15px;
            padding: 15px 0;
            margin-bottom: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .mobile-categories-scroll::-webkit-scrollbar {
            display: none;
        }

        .mobile-categories-scroll .category-grid {
            display: flex;
            flex-wrap: nowrap;
            gap: 10px;
            padding: 0 15px;
        }

        .mobile-categories-scroll .category-item {
            flex-shrink: 0;
            width: 80px;
            padding: 10px 5px;
            background: #f9f9f9;
            font-size: 10px;
        }

        .mobile-categories-scroll .category-item .category-icon {
            font-size: 20px;
            margin-bottom: 5px;
        }

        .mobile-categories-scroll .category-item .category-name {
            font-size: 10px;
        }

        /* NEW: Bottom Navigation Bar */
        .bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: white;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1100;
            justify-content: space-around;
            align-items: center;
            height: 60px;
            padding-bottom: env(safe-area-inset-bottom);
        }

        .bottom-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #666;
            font-size: 12px;
            width: 20%;
            height: 100%;
            transition: color 0.2s;
        }

        .bottom-nav-item.active,
        .bottom-nav-item:hover {
            color: var(--skybuy-blue);
        }

        .bottom-nav-item .icon {
            font-size: 24px;
            margin-bottom: 2px;
            line-height: 1;
        }

        .bottom-nav-item.parachute-button {
            position: relative;
            background: var(--skybuy-green);
            color: white;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            top: -20px;
            box-shadow: 0 -5px 15px rgba(0, 0, 0, 0.2);
            font-size: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin: 0 -10px;
        }

        .bottom-nav-item.parachute-button .icon {
            margin-bottom: 0;
        }

        .bottom-nav-item.parachute-button span {
            display: none;
        }

        .bottom-nav-item.parachute-button img {
            width: 45px;
            height: auto;
        }


        /* NEW AUTH MODAL CSS */
        .auth-modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 2000;
            /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: auto;
            /* Enable scroll if needed */
            background-color: rgba(0, 0, 0, 0.6);
            /* Black w/ opacity */
            align-items: center;
            /* Center modal vertically */
            justify-content: center;
            /* Center modal horizontally */
        }

        .auth-modal-content {
            background-color: #fefefe;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 500px;
            position: relative;
            text-align: center;
        }

        .auth-close-button {
            color: #aaa;
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .auth-close-button:hover,
        .auth-close-button:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .auth-modal h2 {
            color: var(--skybuy-blue);
            margin-bottom: 25px;
            font-size: 28px;
        }

        .auth-modal-tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 25px;
            border-bottom: 1px solid #eee;
        }

        .auth-tab-button {
            padding: 12px 20px;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 16px;
            color: #777;
            transition: all 0.3s ease;
            position: relative;
            outline: none;
        }

        .auth-tab-button.active {
            color: var(--skybuy-blue);
            font-weight: bold;
        }

        .auth-tab-button.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: var(--skybuy-blue);
        }

        .auth-user-type-tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 25px;
            border-bottom: 1px solid #eee;
        }

        .auth-user-type-tab-button {
            padding: 10px 15px;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 15px;
            color: #777;
            transition: all 0.3s ease;
            position: relative;
            outline: none;
        }

        .auth-user-type-tab-button.active {
            color: var(--skybuy-blue);
            font-weight: bold;
        }

        .auth-user-type-tab-button.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: var(--skybuy-blue);
        }


        .auth-form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .auth-form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 15px;
        }

        .auth-form-group input,
        .auth-form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        .auth-form-group input:focus,
        .auth-form-group textarea:focus {
            border-color: var(--skybuy-blue);
            outline: none;
            box-shadow: 0 0 0 2px rgba(26, 54, 93, 0.2);
        }

        .auth-form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .auth-submit-btn {
            width: 100%;
            padding: 15px;
            background-color: var(--skybuy-blue);
            /* Login primary button */
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 15px;
        }

        .auth-submit-btn.register-btn {
            background-color: var(--skybuy-green);
            /* Register primary button */
        }

        .auth-submit-btn:hover {
            background-color: #2a4c7c;
        }

        .auth-submit-btn.register-btn:hover {
            background-color: #2e9f5d;
        }

        .auth-secondary-link {
            margin-top: 25px;
            font-size: 15px;
            color: #555;
        }

        .auth-secondary-link a {
            color: var(--skybuy-blue);
            text-decoration: none;
            font-weight: bold;
        }

        .auth-secondary-link a.register-link {
            color: var(--skybuy-green);
        }

        .auth-secondary-link a:hover {
            text-decoration: underline;
        }

        .auth-forgot-password {
            display: block;
            text-align: right;
            margin-top: -10px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .auth-forgot-password a {
            color: #777;
            text-decoration: none;
        }

        .auth-forgot-password a:hover {
            text-decoration: underline;
        }

        /* Google Sign-in button for example */
        .google-signin-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 12px;
            background-color: #4285F4;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 15px;
            transition: background-color 0.3s ease;
            text-decoration: none;
            /* For the <a> tag */
        }

        .google-signin-btn img {
            width: 24px;
            height: 24px;
            margin-right: 10px;
            background-color: white;
            /* White background for the G icon */
            padding: 2px;
            border-radius: 3px;
        }

        .google-signin-btn:hover {
            background-color: #357ae8;
        }

        /* NEW: Mobile Category Sidebar */
        .mobile-category-sidebar {
            position: fixed;
            top: 0;
            left: -100%;
            /* Initially off-screen */
            width: 100%;
            height: 100%;
            background: white;
            z-index: 1200;
            overflow-y: auto;
            transition: left 0.3s ease-in-out;
            display: none;
            /* Hidden by default, will be shown on mobile via media query */
            flex-direction: column;
            /* To stack header and grid */
        }

        .mobile-category-sidebar.open {
            left: 0;
        }

        .mobile-category-sidebar .sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background: var(--skybuy-blue);
            color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            flex-shrink: 0;
            /* Prevent header from shrinking */
        }

        .mobile-category-sidebar .sidebar-header h3 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }

        .mobile-category-sidebar .close-sidebar {
            font-size: 30px;
            cursor: pointer;
            line-height: 1;
        }

        .mobile-category-sidebar .category-grid {
            padding: 20px;
            gap: 10px;
            /* Adjust gap for mobile */
            flex: 1;
            /* Allow grid to fill available space and scroll */
        }

        .mobile-category-sidebar .category-item {
            padding: 12px 8px;
            /* Adjust padding for mobile */
        }

        .mobile-category-sidebar .category-item .category-icon {
            font-size: 28px;
            /* Slightly larger icons */
        }

        .mobile-category-sidebar .category-item .category-name {
            font-size: 11px;
            /* Adjust font size for mobile */
        }


        /* ========================================================= */
        /*                          MEDIA QUERIES                    */
        /* ========================================================= */

        @media (max-width: 1024px) {
            .header-content {
                padding: 0 15px;
            }

            .logo {
                font-size: 24px;
            }

            .search-container {
                margin: 0 20px;
            }

            .search-box {
                padding: 12px 50px 12px 40px;
                font-size: 15px;
            }

            .search-btn {
                font-size: 16px;
            }

            .search-container::after {
                font-size: 16px;
            }

            .left-sidebar {
                width: 250px;
                margin-right: 15px;
            }

            .right-content {
                padding: 15px;
            }

            .hero-section {
                height: 350px;
            }

            .slide h2 {
                font-size: 32px;
            }

            .slide p {
                font-size: 16px;
            }

            .login-card h3 {
                font-size: 20px;
            }

            .login-card p {
                font-size: 13px;
            }

            .section-title {
                font-size: 22px;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            }

            .products-section .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            }

            .auth-modal-content {
                max-width: 450px;
            }

            .auth-modal h2 {
                font-size: 24px;
            }

            .auth-tab-button {
                font-size: 15px;
                padding: 10px 18px;
            }

            .auth-user-type-tab-button {
                font-size: 14px;
                padding: 8px 12px;
            }

            .auth-form-group label {
                font-size: 14px;
            }

            .auth-form-group input,
            .auth-form-group textarea {
                padding: 10px 12px;
                font-size: 14px;
            }

            .auth-submit-btn {
                padding: 12px;
                font-size: 16px;
            }

            .auth-secondary-link {
                font-size: 14px;
            }
        }

        @media (max-width: 768px) {
            body {
                padding-bottom: 60px;
                /* Space for bottom nav */
            }

            .header {
                padding: 10px 0;
            }

            .header-content {
                flex-wrap: wrap;
                justify-content: center;
                gap: 10px;
                padding: 0 10px;
            }

            .logo {
                font-size: 24px;
                margin-bottom: 10px;
                width: 100%;
                text-align: center;
                justify-content: center;
            }

            .logo img {
                height: 35px;
            }

            .search-container {
                flex: none;
                width: 100%;
                max-width: 100%;
                margin: 0;
            }

            .search-box {
                padding: 10px 50px 10px 45px;
                font-size: 14px;
                border-radius: 25px;
            }

            .search-btn,
            .search-container::after {
                font-size: 16px;
                top: 50%;
            }

            .search-btn {
                left: 15px;
            }

            .search-container::after {
                right: 15px;
            }

            .header-icons {
                display: none;
            }

            .main-container {
                flex-direction: column;
                min-height: auto;
                /* padding: 0 10px; */
            }

            .left-sidebar {
                display: none;
                /* Hide desktop sidebar on mobile */
            }

            /* Hide the horizontal scrolling category list on mobile, as per user request */
            .mobile-categories-scroll {
                display: none;
            }

            .right-content {
                padding: 10px 0;
                margin-top: -8px;
            }

            .hero-section {
                flex-direction: column;
                height: auto;
                gap: 15px;
                margin-bottom: 15px;
            }

            .slider-container {
                flex: auto;
                height: 250px;
            }

            .slide {
                /* padding: 20px; */
                flex-direction: column;
                text-align: center;
                justify-content: center;
            }

            .slide-content {
                margin-bottom: 15px;
            }

            .slide h2 {
                font-size: 24px;
            }

            .slide p {
                font-size: 14px;
            }

            .slide-graphic {
                width: 100%;
                height: 100px;
                transform: none;
            }

            .phone-display {
                display: none;
            }

            .float-element {
                display: none;
            }

            .login-cards-container {
                flex: auto;
                height: auto;
                gap: 15px;
            }

            .login-card {
                padding: 15px;
            }

            .login-card h3 {
                font-size: 18px;
            }

            .login-card p {
                font-size: 12px;
            }

            .login-card .icon {
                width: 40px;
                height: 40px;
                margin-bottom: 10px;
            }

            .categories-section {
                padding: 15px;
                margin-bottom: 15px;
            }

            .section-title {
                font-size: 20px;
                margin-bottom: 15px;
            }

            .categories-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 10px;
            }

            .category-card {
                padding: 15px 10px;
            }

            .category-card-icon {
                font-size: 28px;
            }

            .category-card-name {
                font-size: 10px;
            }

            .featured-section,
            .products-section {
                padding: 15px;
                margin-bottom: 15px;
            }

            .products-grid,
            .products-section .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
                gap: 15px;
            }

            .product-image {
                height: 120px;
                font-size: 30px;
            }

            .product-info {
                padding: 10px;
            }

            .product-title {
                font-size: 13px;
                margin-bottom: 5px;
            }

            .product-price {
                font-size: 14px;
            }

            .footer-content {
                grid-template-columns: 1fr;
                gap: 20px;
                padding: 0 15px;
            }

            .footer-section h4 {
                font-size: 16px;
                margin-bottom: 10px;
            }

            .footer-section p,
            .footer-section a {
                font-size: 14px;
                margin-bottom: 5px;
            }

            .footer-bottom {
                padding: 15px 0;
                font-size: 12px;
            }

            .bottom-nav {
                display: flex;
                /* Show bottom nav on mobile */
            }

            /* Show mobile category sidebar only on mobile screens */
            .mobile-category-sidebar {
                display: flex;
                /* Initially off-screen, but layout is flex */
            }

            .auth-modal-content {
                width: 95%;
                padding: 15px;
            }

            .auth-modal h2 {
                font-size: 22px;
                margin-bottom: 20px;
            }

            .auth-tab-button {
                font-size: 14px;
                padding: 10px 15px;
            }

            .auth-user-type-tab-button {
                font-size: 13px;
                padding: 7px 10px;
            }

            .auth-form-group input,
            .auth-form-group textarea {
                padding: 10px;
                font-size: 13px;
            }

            .auth-submit-btn {
                padding: 10px;
                font-size: 15px;
            }

            .auth-secondary-link {
                font-size: 13px;
            }

            .auth-forgot-password {
                font-size: 12px;
            }

            .google-signin-btn {
                padding: 10px;
                font-size: 14px;
            }

            .google-signin-btn img {
                width: 20px;
                height: 20px;
            }
        }

        @media (max-width: 480px) {
            .products-section .product-image{
                /* height: 120px; */
            }
            .logo {
                font-size: 20px;
            }

            .search-box {
                padding: 8px 45px 8px 40px;
                font-size: 13px;
            }

            .search-btn,
            .search-container::after {
                font-size: 14px;
            }

            .hero-section {
                height: auto;
            }

            .slider-container {
                height: 200px;
            }

            .slide h2 {
                font-size: 20px;
            }

            .slide p {
                font-size: 12px;
            }

            .login-card h3 {
                font-size: 16px;
            }

            .login-card p {
                font-size: 11px;
            }

            .categories-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            /* .product-card {
                padding: 10px;
            } */

            .products-grid,
            .products-section .products-grid {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }

            .product-image {
                height: 100px;
            }

            .product-title {
                font-size: 12px;
            }

            .product-price {
                font-size: 13px;
            }

            .footer-content {
                padding: 0 10px;
            }

            .bottom-nav-item {
                font-size: 10px;
            }

            .bottom-nav-item .icon {
                font-size: 20px;
            }

            .bottom-nav-item.parachute-button {
                width: 50px;
                height: 50px;
                top: -15px;
                font-size: 24px;
            }

            .auth-modal h2 {
                font-size: 20px;
            }

            .auth-tab-button {
                font-size: 13px;
                padding: 8px 12px;
            }

            .auth-user-type-tab-button {
                font-size: 12px;
                padding: 6px 8px;
            }

            .auth-form-group input,
            .auth-form-group textarea {
                padding: 8px;
                font-size: 12px;
            }

            .auth-submit-btn {
                padding: 8px;
                font-size: 14px;
            }

            .auth-secondary-link {
                font-size: 12px;
            }
        }

    </style>

@if (get_setting('google_analytics') == 1)
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ env('TRACKING_ID') }}"></script>

    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ env('TRACKING_ID') }}');
    </script>
@endif

@if (get_setting('facebook_pixel') == 1)
    <!-- Facebook Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ env('FACEBOOK_PIXEL_ID') }}');
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ env('FACEBOOK_PIXEL_ID') }}&ev=PageView&noscript=1"/>
    </noscript>
    <!-- End Facebook Pixel Code -->
@endif

@php
    echo get_setting('header_script');
@endphp

</head>
<body>
    <!-- aiz-main-wrapper -->
    <div class="aiz-main-wrapper d-flex flex-column bg-white">
        @php
            $user = auth()->user();
            $user_avatar = null;
            $carts = [];
            if ($user && $user->avatar_original != null) {
                $user_avatar = uploaded_asset($user->avatar_original);
            }

            $system_language = get_system_language();
        @endphp
        <!-- Header -->
        @include('frontend.inc.nav')

        {{-- NEW: Wrap @yield('content') within the main-container / right-content structure --}}
        {{-- This ensures the dynamic content is loaded into the correct place on both initial load and AJAX calls --}}
        <div class="main-container" style="background-color: #F2F4F8;">
            <!-- Left Sidebar - Sticky (Desktop Only) -->
            @include('frontend.classic.partials.category_menu')

            <!-- Right Content Area -->
            <main class="right-content">
                @yield('content')
            </main>
        </div>

        <!-- footer -->
        {{-- @include('frontend.inc.footer') --}}

    </div>

    {{-- @if(get_setting('use_floating_buttons') == 1)
        <!-- Floating Buttons -->
        @include('frontend.inc.floating_buttons')
    @endif --}}

    {{-- <div class="aiz-refresh">
        <div class="aiz-refresh-content"><div></div><div></div><div></div></div>
    </div> --}}


    @if (env("DEMO_MODE") == "On")
        <!-- demo nav -->
        @include('frontend.inc.demo_nav')
    @endif

    <!-- cookies agreement -->
    @php
        $alert_location = get_setting('custom_alert_location');
        $order = in_array($alert_location, ['top-left', 'top-right']) ? 'asc' : 'desc';
        $custom_alerts = App\Models\CustomAlert::where('status', 1)->orderBy('id', $order)->get();
    @endphp

    <div class="aiz-custom-alert {{ get_setting('custom_alert_location') }}">
        @foreach ($custom_alerts as $custom_alert)
            @if($custom_alert->id == 1)
                <div class="aiz-cookie-alert mb-3" style="box-shadow: 0px 6px 10px rgba(0, 0, 0, 0.24);">
                    <div class="p-3 px-lg-2rem rounded-0" style="background: {{ $custom_alert->background_color }};">
                        <div class="text-{{ $custom_alert->text_color }} mb-3">
                            {!! $custom_alert->description !!}
                        </div>
                        <button class="btn btn-block btn-primary rounded-0 aiz-cookie-accept">
                            {{ translate('Ok. I Understood') }}
                        </button>
                    </div>
                </div>
            @else
                <div class="mb-3 custom-alert-box removable-session d-none" data-key="custom-alert-box-{{ $custom_alert->id }}" data-value="removed" style="box-shadow: 0px 6px 10px rgba(0, 0, 0, 0.24);">
                    <div class="rounded-0 position-relative" style="background: {{ $custom_alert->background_color }};">
                        <a href="{{ $custom_alert->link }}" class="d-block h-100 w-100">
                            <div class="@if ($custom_alert->type == 'small') d-flex @endif">
                                <img class="@if ($custom_alert->type == 'small') h-140px w-120px img-fit @else w-100 @endif" src="{{ uploaded_asset($custom_alert->banner) }}" alt="custom_alert">
                                <div class="text-{{ $custom_alert->text_color }} p-2rem">
                                    {!! $custom_alert->description !!}
                                </div>
                            </div>
                        </a>
                        <button class="absolute-top-right bg-transparent btn btn-circle btn-icon d-flex align-items-center justify-content-center text-{{ $custom_alert->text_color }} hov-text-primary set-session" data-key="custom-alert-box-{{ $custom_alert->id }}" data-value="removed" data-toggle="remove-parent" data-parent=".custom-alert-box">
                            <i class="la la-close fs-20"></i>
                        </button>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <!-- website popup -->
    @php
        $dynamic_popups = App\Models\DynamicPopup::where('status', 1)->orderBy('id', 'asc')->get();
        use App\Models\Order;
        use App\Models\OrderDetail;
    @endphp
    @foreach ($dynamic_popups as $key => $dynamic_popup)
        @php
        $showPopup = true;
        if ($dynamic_popup->id == 100 ) {
            if(auth()->user()){
            $userOrderIds = Order::where('user_id', auth()->user()->id)->pluck('id');
            $hasUnreviewed = OrderDetail::whereIn('order_id', $userOrderIds)
            ->where('delivery_status', 'delivered')
                            ->where('reviewed', 0)
                            ->exists();
            $showPopup = $hasUnreviewed;
            }else{
              $showPopup= false;
            }
        }
        @endphp

        {{-- @if($dynamic_popup->id == 1)
            <div class="modal website-popup removable-session d-none" data-key="website-popup" data-value="removed">
                <div class="absolute-full bg-black opacity-60"></div>
                <div class="modal-dialog modal-dialog-centered modal-dialog-zoom modal-md mx-4 mx-md-auto">
                    <div class="modal-content position-relative border-0 rounded-0">
                        <div class="aiz-editor-data">
                            <div class="d-block">
                                <img class="w-100" src="{{ uploaded_asset($dynamic_popup->banner) }}" alt="dynamic_popup">
                            </div>
                        </div>
                        <div class="pb-5 pt-4 px-3 px-md-2rem">
                            <h1 class="fs-30 fw-700 text-dark">{{ $dynamic_popup->title }}</h1>
                            <p class="fs-14 fw-400 mt-3 mb-4">{{ $dynamic_popup->summary }}</p>
                            @if ($dynamic_popup->show_subscribe_form == 'on')
                                <form class="" method="POST" action="{{ route('subscribers.store') }}">
                                    @csrf
                                    <div class="form-group mb-0">
                                        <input type="email" class="form-control" placeholder="{{ translate('Your Email Address') }}" name="email" required>
                                    </div>
                                    <button type="submit" class="btn btn-block mt-3 rounded-0 text-{{ $dynamic_popup->btn_text_color }}" style="background: {{ $dynamic_popup->btn_background_color }};">
                                        {{ $dynamic_popup->btn_text }}
                                    </button>
                                </form>
                            @endif
                        </div>
                        <button class="absolute-top-right bg-white shadow-lg btn btn-circle btn-icon mr-n3 mt-n3 set-session" data-key="website-popup" data-value="removed" data-toggle="remove-parent" data-parent=".website-popup">
                            <i class="la la-close fs-20"></i>
                        </button>
                    </div>
                </div>
            </div>
        @else
            @if($showPopup)
            <div class="modal website-popup removable-session d-none" data-key="website-popup-{{ $dynamic_popup->id }}" data-value="removed">
                <div class="absolute-full bg-black opacity-60"></div>
                <div class="modal-dialog modal-dialog-centered modal-dialog-zoom modal-md mx-4 mx-md-auto">
                    <div class="modal-content position-relative border-0 rounded-0">
                        <div class="aiz-editor-data">
                            <div class="d-block">
                                <img class="w-100" src="{{ uploaded_asset($dynamic_popup->banner) }}" alt="dynamic_popup">
                            </div>
                        </div>
                        <div class="pb-5 pt-4 px-3 px-md-2rem">
                            <h1 class="fs-30 fw-700 text-dark">{{ $dynamic_popup->title }}</h1>
                            <p class="fs-14 fw-400 mt-3 mb-4">{{ $dynamic_popup->summary }}</p>
                            <a href="{{ $dynamic_popup->btn_link }}" class="btn btn-block mt-3 rounded-0 text-{{ $dynamic_popup->btn_text_color }} set-session" style="background: {{ $dynamic_popup->btn_background_color }};"data-key="website-popup-{{ $dynamic_popup->id }}" data-value="removed" data-toggle="remove-parent" data-parent=".website-popup">
                                {{ $dynamic_popup->btn_text }}
                            </a>
                        </div>
                        <button class="absolute-top-right bg-white shadow-lg btn btn-circle btn-icon mr-n3 mt-n3 set-session" data-key="website-popup-{{ $dynamic_popup->id }}" data-value="removed" data-toggle="remove-parent" data-parent=".website-popup">
                            <i class="la la-close fs-20"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endif
        @endif --}}
    @endforeach

    @include('frontend.partials.modal')

    @include('frontend.partials.account_delete_modal')

    {{-- Mohammad Hassan --}}
    @include('frontend.partials.user_type_modal')

    <div class="modal fade" id="addToCart">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size" role="document">
            <div class="modal-content position-relative">
                <div class="c-preloader text-center p-3">
                    <i class="las la-spinner la-spin la-3x"></i>
                </div>
                <button type="button" class="close absolute-top-right btn-icon close z-1 btn-circle bg-gray mr-2 mt-2 d-flex justify-content-center align-items-center" data-dismiss="modal" aria-label="Close" style="background: #ededf2; width: calc(2rem + 2px); height: calc(2rem + 2px);">
                    <span aria-hidden="true" class="fs-24 fw-700" style="margin-left: 2px;">&times;</span>
                </button>
                <div id="addToCart-modal-body">

                </div>
            </div>
        </div>
    </div>

     {{-- NEW: This is the container for product-specific modals loaded via AJAX --}}
     <div id="product-modals-container"></div>
     {{-- ORIGINAL: Keep this if you have other global modals yielded here --}}
    @yield('modal')

    <!-- SCRIPTS -->
    <script src="{{ static_asset('assets/js/vendors.js') }}"></script>
    <script>
        // Safe fallback: ensure `$` maps to jQuery even if another script alters it
        if (!window.$ && window.jQuery) {
            window.$ = window.jQuery;
        }
    </script>
    <script src="{{ static_asset('assets/js/aiz-core.js?v=') }}{{ rand(1000, 9999) }}"></script>

    {{-- WhatsaApp Chat --}}
    @if (get_setting('whatsapp_chat') == 1)
        <script type="text/javascript">
            (function () {
                var options = {
                    whatsapp: "{{ env('WHATSAPP_NUMBER') }}",
                    call_to_action: "{{ translate('Message us') }}",
                    position: "right", // Position may be 'right' or 'left'
                };
                var proto = document.location.protocol, host = "getbutton.io", url = proto + "//static." + host;
                var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = url + '/widget-send-button/js/init.js';
                s.onload = function () { WhWidgetSendButton.init(host, proto, options); };
                var x = document.getElementsByTagName('script')[0]; x.parentNode.insertBefore(s, x);
            })();
        </script>
    @endif

    <style>
    .sc-q8c6tt-3 {
        bottom: 54px !important;
    }

    a[aria-label="Go to GetButton.io website"] {
        display: none !important;
    }

</style>

    <script>
        @foreach (session('flash_notification', collect())->toArray() as $message)
            AIZ.plugins.notify('{{ $message['level'] }}', '{{ $message['message'] }}');
        @endforeach
    </script>

    <script>
        // NEW: Initial scripts for the homepage content, to be run on initial load
        function initHomepageScripts() {

            $.post('{{ route('home.section.featured') }}', {
                _token: '{{ csrf_token() }}'
            }, function(data) {
                $('#section_featured').html(data);
                AIZ.plugins.slickCarousel();
            });

            $.post('{{ route('home.section.todays_deal') }}', {
                _token: '{{ csrf_token() }}'
            }, function(data) {
                $('#todays_deal').html(data);
                AIZ.plugins.slickCarousel();
            });

            $.post('{{ route('home.section.best_selling') }}', {
                _token: '{{ csrf_token() }}'
            }, function(data) {
                $('#section_best_selling').html(data);
                AIZ.plugins.slickCarousel();
            });

            $.post('{{ route('home.section.newest_products') }}', {
                _token: '{{ csrf_token() }}'
            }, function(data) {
                $('#section_newest').html(data);
                AIZ.plugins.slickCarousel();
            });

            $.post('{{ route('home.section.auction_products') }}', {
                _token: '{{ csrf_token() }}'
            }, function(data) {
                $('#auction_products').html(data);
                AIZ.plugins.slickCarousel();
            });

            var isPreorderEnabled = @json(addon_is_activated('preorder'));

            if (isPreorderEnabled) {
                $.post('{{ route('home.section.preorder_products') }}', {
                    _token: '{{ csrf_token() }}'
                }, function(data) {
                    $('#section_featured_preorder_products').html(data);
                    AIZ.plugins.slickCarousel();
                });
            }

            $.post('{{ route('home.section.home_categories') }}', {
                _token: '{{ csrf_token() }}'
            }, function(data) {
                $('#section_home_categories').html(data);
                AIZ.plugins.slickCarousel();
            });

        // Slider JavaScript (Make these functions global or re-define them here for homepage)
        let currentSlideIndex = 0;
            const slides = document.querySelectorAll('.slide');
            const dots = document.querySelectorAll('.nav-dot');
            const totalSlides = slides.length;
            let autoSlideInterval;
            window.showSlide = function(index) { // Made global
                currentSlideIndex = (index + totalSlides) % totalSlides;
                const slider = document.getElementById('autoSlider');
                if (slider) {
                    slider.style.transform = `translateX(-${currentSlideIndex * 100}%)`;
                }
                dots.forEach(dot => dot.classList.remove('active'));
                if (dots[currentSlideIndex]) {
                    dots[currentSlideIndex].classList.add('active');
                }
            }
            window.nextSlide = function() { // Made global
                window.showSlide(currentSlideIndex + 1);
            }
            window.changeSlide = function(index) { // Made global
                window.showSlide(index);
                window.resetAutoSlide();
            }
            window.startAutoSlide = function() { // Made global
                if (autoSlideInterval) clearInterval(autoSlideInterval);
                autoSlideInterval = setInterval(window.nextSlide, 5000);
            }
            window.resetAutoSlide = function() { // Made global
                clearInterval(autoSlideInterval);
                window.startAutoSlide();
            }

            // Initial call for slider
            if (slides.length > 0) {
                window.showSlide(0);
                window.startAutoSlide();
                dots.forEach((dot, index) => {
                    dot.onclick = () => window.changeSlide(index);
                });
            }
            // Other general DOMContentLoaded scripts from index.blade.php
            const searchBox = document.querySelector('.search-box');
            if (searchBox) {
                searchBox.addEventListener('keyup', function (e) {
                    if (e.key === 'Enter') {
                        const searchTerm = this.value.toLowerCase();
                        // This search is for the current content, not AJAX
                        // You might want to remove it or update to use AJAX for search
                        const products = document.querySelectorAll('#dynamic-content-wrapper .product-card');
                        products.forEach(product => {
                            const title = product.querySelector('.product-title');
                            if (title && title.textContent.toLowerCase().includes(searchTerm)) {
                                product.style.display = 'block';
                            } else if (title) {
                                product.style.display = 'none';
                            }
                        });
                    }
                });
            }
        }
        // Call homepage scripts only on initial page load (not on AJAX content changes)
        if (window.location.pathname === '{{ route('home') }}' || window.location.pathname === '/') {
            document.addEventListener('DOMContentLoaded', initHomepageScripts);
        }

        $(document).ready(function() {
            $('.category-nav-element').each(function(i, el) {

                $(el).on('mouseover', function(){
                    if(!$(el).find('.sub-cat-menu').hasClass('loaded')){
                        $.post('{{ route('category.elements') }}', {
                            _token: AIZ.data.csrf,
                            id:$(el).data('id'
                            )}, function(data){
                            $(el).find('.sub-cat-menu').addClass('loaded').html(data);
                        });
                    }
                });
            });

            if ($('#lang-change').length > 0) {
                $('#lang-change .dropdown-menu a').each(function() {
                    $(this).on('click', function(e){
                        e.preventDefault();
                        var $this = $(this);
                        var locale = $this.data('flag');
                        $.post('{{ route('language.change') }}',{_token: AIZ.data.csrf, locale:locale}, function(data){
                            location.reload();
                        });

                    });
                });
            }

            if ($('#currency-change').length > 0) {
                $('#currency-change .dropdown-menu a').each(function() {
                    $(this).on('click', function(e){
                        e.preventDefault();
                        var $this = $(this);
                        var currency_code = $this.data('currency');
                        $.post('{{ route('currency.change') }}',{_token: AIZ.data.csrf, currency_code:currency_code}, function(data){
                            location.reload();
                        });

                    });
                });
            }
        });

        $('#search').on('keyup', function(){
            search();
        });

        $('#search').on('focus', function(){
            search();
        });

        function search(){
            var searchKey = $('#search').val();
            if(searchKey.length > 0){
                $('body').addClass("typed-search-box-shown");

                $('.typed-search-box').removeClass('d-none');
                $('.search-preloader').removeClass('d-none');
                $.post('{{ route('search.ajax') }}', { _token: AIZ.data.csrf, search:searchKey}, function(data){
                    if(data == '0'){
                        // $('.typed-search-box').addClass('d-none');
                        $('#search-content').html(null);
                        $('.typed-search-box .search-nothing').removeClass('d-none').html('{{ translate('Sorry, nothing found for') }} <strong>"'+searchKey+'"</strong>');
                        $('.search-preloader').addClass('d-none');

                    }
                    else{
                        $('.typed-search-box .search-nothing').addClass('d-none').html(null);
                        $('#search-content').html(data);
                        $('.search-preloader').addClass('d-none');
                    }
                });
            }
            else {
                $('.typed-search-box').addClass('d-none');
                $('body').removeClass("typed-search-box-shown");
            }
        }

        $(".aiz-user-top-menu").on("mouseover", function (event) {
            $(".hover-user-top-menu").addClass('active');
        })
        .on("mouseout", function (event) {
            $(".hover-user-top-menu").removeClass('active');
        });

        $(document).on("click", function(event){
            var $trigger = $("#category-menu-bar");
            if($trigger !== event.target && !$trigger.has(event.target).length){
                $("#click-category-menu").slideUp("fast");;
                $("#category-menu-bar-icon").removeClass('show');
            }
        });

        function updateNavCart(view,count){
            $('.cart-count').html(count);
            $('#cart_items').html(view);
        }

        function removeFromCart(key){
            $.post('{{ route('cart.removeFromCart') }}', {
                _token  : AIZ.data.csrf,
                id      :  key
            }, function(data){
                updateNavCart(data.nav_cart_view,data.cart_count);
                $('#cart-details').html(data.cart_view);
                AIZ.plugins.notify('success', "{{ translate('Item has been removed from cart') }}");
                $('#cart_items_sidenav').html(parseInt($('#cart_items_sidenav').html())-1);
            });
        }

        // Mohammad Hassan
        function showLoginModal() {
            showUserTypeModal();
        }

        function addToCompare(id){
            $.post('{{ route('compare.addToCompare') }}', {_token: AIZ.data.csrf, id:id}, function(data){
                $('#compare').html(data);
                AIZ.plugins.notify('success', "{{ translate('Item has been added to compare list') }}");
                $('#compare_items_sidenav').html(parseInt($('#compare_items_sidenav').html())+1);
            });
        }

        function addToWishList(id){
            @if (Auth::check() && Auth::user()->user_type == 'customer' && Auth::user()->user_type != 'wholesaler')
                $.post('{{ route('wishlists.store') }}', {_token: AIZ.data.csrf, id:id}, function(data){
                    if(data != 0){
                        $('#wishlist').html(data);
                        AIZ.plugins.notify('success', "{{ translate('Item has been added to wishlist') }}");
                    }
                    else{
                        AIZ.plugins.notify('warning', "{{ translate('Please login first') }}");
                    }
                });
            @elseif(Auth::check() && Auth::user()->user_type != 'customer' && Auth::user()->user_type != 'wholesaler')
                AIZ.plugins.notify('warning', "{{ translate('Please Login as a customer to add products to the WishList.') }}");
            @else
                AIZ.plugins.notify('warning', "{{ translate('Please login first') }}");
            @endif
        }

        function showAddToCartModal(id){
            if(!$('#modal-size').hasClass('modal-lg')){
                $('#modal-size').addClass('modal-lg');
            }
            $('#addToCart-modal-body').html(null);
                $('#addToCart').modal();
            $('.c-preloader').show();
            $.post('{{ route('cart.showCartModal') }}', {_token: AIZ.data.csrf, id:id}, function(data){
                $('.c-preloader').hide();
                $('#addToCart-modal-body').html(data);
                AIZ.plugins.slickCarousel();
                AIZ.plugins.zoom();
                AIZ.extra.plusMinus();
                getVariantPrice();
            });
        }



        function showReviewImageModal(imageUrl, imagesJson) {
            try {
                var images = JSON.parse(imagesJson);
                var currentIndex = images.indexOf(imageUrl);

                $('#modalReviewImage').attr('src', imageUrl);
                $('#reviewImageModal').modal('show');

                $('#prevImageBtn').off('click').on('click', function() {
                    currentIndex = (currentIndex - 1 + images.length) % images.length;
                    $('#modalReviewImage').attr('src', images[currentIndex]);
                });

                $('#nextImageBtn').off('click').on('click', function() {
                    currentIndex = (currentIndex + 1) % images.length;
                    $('#modalReviewImage').attr('src', images[currentIndex]);
                });
            } catch (error) {
                console.error("Error parsing JSON:", error);
            }
        }

        $('#option-choice-form input').on('change', function(){
            getVariantPrice();
        });

        function getVariantPrice(){
            if($('#option-choice-form input[name=quantity]').val() > 0 && checkAddToCartValidity()){
                $.ajax({
                    type:"POST",
                    url: '{{ route('products.variant_price') }}',
                    data: $('#option-choice-form').serializeArray(),
                    success: function(data){
                        $('.product-gallery-thumb .carousel-box').each(function (i) {
                            if($(this).data('variation') && data.variation == $(this).data('variation')){
                                $('.product-gallery-thumb').slick('slickGoTo', i);
                            }
                        })

                        $('#option-choice-form #chosen_price_div').removeClass('d-none');
                        $('#option-choice-form #chosen_price_div #chosen_price').html(data.price);
                        // Mohammad Hassan
                        $('#available-quantity').html(data.quantity);
                        $('.input-number').prop('max', data.max_limit);
                        if(parseInt(data.in_stock) == 0 && data.digital  == 0){
                           $('.buy-now').addClass('d-none');
                           $('.add-to-cart').addClass('d-none');
                           // Show pre-order button if product allows pre-orders
                           if(data.allow_preorder && data.allow_preorder == 1) {
                               $('.out-of-stock[disabled]').addClass('d-none');
                               $('.out-of-stock[data-toggle="modal"]').removeClass('d-none');
                           } else {
                               $('.out-of-stock').removeClass('d-none');
                           }
                        }
                        else{
                           $('.buy-now').removeClass('d-none');
                           $('.add-to-cart').removeClass('d-none');
                           $('.out-of-stock').addClass('d-none');
                        }

                        AIZ.extra.plusMinus();
                    }
                });
            }
        }

        function checkAddToCartValidity(){
            var names = {};
            $('#option-choice-form input:radio').each(function() { // find unique names
                names[$(this).attr('name')] = true;
            });
            var count = 0;
            $.each(names, function() { // then count them
                count++;
            });

            if($('#option-choice-form input:radio:checked').length == count){
                return true;
            }

            return false;
        }

        function addToCart(){
            @if (Auth::check() && Auth::user()->user_type != 'customer' && Auth::user()->user_type != 'wholesaler')
                AIZ.plugins.notify('warning', "{{ translate('Please Login as a customer to add products to the Cart.') }}");
                return false;
            @endif

            if(checkAddToCartValidity()) {
                $('#addToCart').modal();
                $('.c-preloader').show();
                $.ajax({
                    type:"POST",
                    url: '{{ route('cart.addToCart') }}',
                    data: $('#option-choice-form').serializeArray(),
                    success: function(data){
                       $('#addToCart-modal-body').html(null);
                       $('.c-preloader').hide();
                       $('#modal-size').removeClass('modal-lg');
                       $('#addToCart-modal-body').html(data.modal_view);
                       AIZ.extra.plusMinus();
                       AIZ.plugins.slickCarousel();
                       updateNavCart(data.nav_cart_view,data.cart_count);
                    },
                    error: function(xhr, status, error) {
                        $('.c-preloader').hide();
                        console.error('Add to cart error:', xhr.responseText);
                        AIZ.plugins.notify('danger', "{{ translate('Something went wrong. Please try again.') }}");
                        $('#addToCart').modal('hide');
                    }
                });

                if ("{{ get_setting('facebook_pixel') }}" == 1){
                    // Facebook Pixel AddToCart Event
                    fbq('track', 'AddToCart', {content_type: 'product'});
                    // Facebook Pixel AddToCart Event
                }
            }
            else{
                AIZ.plugins.notify('warning', "{{ translate('Please choose all the options') }}");
            }
        }

        function buyNow(){
            // Allow guests to buy now when guest checkout is enabled
            var guestCheckoutEnabled = {{ (int) (get_setting('guest_checkout_active') ?? 0) === 1 || (int) (function_exists('get_Setting') ? (get_Setting('guest_checkout_activation') ?? 0) : 0) === 1 ? 1 : 0 }};
            @if (Auth::check() && Auth::user()->user_type != 'customer' && Auth::user()->user_type != 'wholesaler')
                AIZ.plugins.notify('warning', "{{ translate('Please Login as a customer to add products to the Cart.') }}");
                return false;
            @endif

            if(checkAddToCartValidity()) {
                $('#addToCart-modal-body').html(null);
                $('#addToCart').modal();
                $('.c-preloader').show();
                
                // Add buy_now parameter to distinguish from regular add to cart
                var formData = $('#option-choice-form').serializeArray();
                formData.push({name: 'buy_now', value: '1'});
                
                $.ajax({
                    type:"POST",
                    url: '{{ route('cart.addToCart') }}',
                    data: formData,
                    success: function(data){
                        if(data.status == 1){
                            $('#addToCart-modal-body').html(data.modal_view);
                            updateNavCart(data.nav_cart_view,data.cart_count);
                            // Redirect to checkout directly for buy now
                            window.location.replace("{{ route('checkout') }}");
                        }
                        else{
                            $('#addToCart-modal-body').html(null);
                            $('.c-preloader').hide();
                            $('#modal-size').removeClass('modal-lg');
                            $('#addToCart-modal-body').html(data.modal_view);
                        }
                    },
                    error: function(xhr, status, error) {
                        $('.c-preloader').hide();
                        console.error('Buy now error:', xhr.responseText);
                        AIZ.plugins.notify('danger', "{{ translate('Something went wrong. Please try again.') }}");
                        $('#addToCart').modal('hide');
                    }
               });
            }
            else{
                AIZ.plugins.notify('warning', "{{ translate('Please choose all the options') }}");
            }
        }

        function bid_single_modal(bid_product_id, min_bid_amount){
            @if (Auth::check() && (isCustomer() || isSeller()))
                var min_bid_amount_text = "({{ translate('Min Bid Amount: ') }}"+min_bid_amount+")";
                $('#min_bid_amount').text(min_bid_amount_text);
                $('#bid_product_id').val(bid_product_id);
                $('#bid_amount').attr('min', min_bid_amount);
                $('#bid_for_product').modal('show');
            @elseif (Auth::check() && isAdmin())
                AIZ.plugins.notify('warning', '{{ translate('Sorry, Only customers & Sellers can Bid.') }}');
            @else
                // Mohammad Hassan
                $('#customerAuthModal').modal('show');
            @endif
        }

        function clickToSlide(btn,id){
            $('#'+id+' .aiz-carousel').find('.'+btn).trigger('click');
            $('#'+id+' .slide-arrow').removeClass('link-disable');
            var arrow = btn=='slick-prev' ? 'arrow-prev' : 'arrow-next';
            if ($('#'+id+' .aiz-carousel').find('.'+btn).hasClass('slick-disabled')) {
                $('#'+id).find('.'+arrow).addClass('link-disable');
            }
        }

        function goToView(params) {
            document.getElementById(params).scrollIntoView({behavior: "smooth", block: "center"});
        }

        function copyCouponCode(code){
            navigator.clipboard.writeText(code);
            AIZ.plugins.notify('success', "{{ translate('Coupon Code Copied') }}");
        }

        $(document).ready(function(){
            $('.cart-animate').animate({margin : 0}, "slow");

            $({deg: 0}).animate({deg: 360}, {
                duration: 2000,
                step: function(now) {
                    $('.cart-rotate').css({
                        transform: 'rotate(' + now + 'deg)'
                    });
                }
            });

            setTimeout(function(){
                $('.cart-ok').css({ fill: '#d43533' });
            }, 2000);

        });

        function nonLinkableNotificationRead(){
            $.get('{{ route('non-linkable-notification-read') }}',function(data){
                $('.unread-notification-count').html(data);
            });
        }
    </script>


    <script type="text/javascript">
        if ($('input[name=country_code]').length > 0){
            // Country Code
            var isPhoneShown = true,
                countryData = window.intlTelInputGlobals.getCountryData(),
                input = document.querySelector("#phone-code");

            for (var i = 0; i < countryData.length; i++) {
                var country = countryData[i];
                if (country.iso2 == 'bd') {
                    country.dialCode = '88';
                }
            }

            var iti = intlTelInput(input, {
                separateDialCode: true,
                utilsScript: "{{ static_asset('assets/js/intlTelutils.js') }}?1590403638580",
                onlyCountries: @php echo get_active_countries()->pluck('code') @endphp,
                customPlaceholder: function(selectedCountryPlaceholder, selectedCountryData) {
                    if (selectedCountryData.iso2 == 'bd') {
                        return "01xxxxxxxxx";
                    }
                    return selectedCountryPlaceholder;
                }
            });

            var country = iti.getSelectedCountryData();
            $('input[name=country_code]').val(country.dialCode);

            input.addEventListener("countrychange", function(e) {
                // var currentMask = e.currentTarget.placeholder;
                var country = iti.getSelectedCountryData();
                $('input[name=country_code]').val(country.dialCode);

            });

            function toggleEmailPhone(el) {
                if (isPhoneShown) {
                    $('.phone-form-group').addClass('d-none');
                    $('.email-form-group').removeClass('d-none');
                    $('input[name=phone]').val(null);
                    isPhoneShown = false;
                    $(el).html('*{{ translate('Use Phone Number Instead') }}');
                } else {
                    $('.phone-form-group').removeClass('d-none');
                    $('.email-form-group').addClass('d-none');
                    $('input[name=email]').val(null);
                    isPhoneShown = true;
                    $(el).html('<i>*{{ translate('Use Email Instead') }}</i>');
                }
            }
        }
    </script>

    <script>
        var acc = document.getElementsByClassName("aiz-accordion-heading");
        var i;
        for (i = 0; i < acc.length; i++) {
            acc[i].addEventListener("click", function() {
                this.classList.toggle("active");
                var panel = this.nextElementSibling;
                if (panel.style.maxHeight) {
                    panel.style.maxHeight = null;
                } else {
                    panel.style.maxHeight = panel.scrollHeight + "px";
                }
            });
        }
    </script>

    <script>
        function showFloatingButtons() {
            document.querySelector('.floating-buttons-section').classList.toggle('show');;
        }
    </script>

    @if (env("DEMO_MODE") == "On")
        <script>
            var demoNav = document.querySelector('.aiz-demo-nav');
            var menuBtn = document.querySelector('.aiz-demo-nav-toggler');
            var lineOne = document.querySelector('.aiz-demo-nav-toggler .aiz-demo-nav-btn .line--1');
            var lineTwo = document.querySelector('.aiz-demo-nav-toggler .aiz-demo-nav-btn .line--2');
            var lineThree = document.querySelector('.aiz-demo-nav-toggler .aiz-demo-nav-btn .line--3');
            menuBtn.addEventListener('click', () => {
                toggleDemoNav();
            });

            function toggleDemoNav() {
                // demoNav.classList.toggle('show');
                demoNav.classList.toggle('shadow-none');
                lineOne.classList.toggle('line-cross');
                lineTwo.classList.toggle('line-fade-out');
                lineThree.classList.toggle('line-cross');
                if ($('.aiz-demo-nav-toggler').hasClass('show')) {
                    $('.aiz-demo-nav-toggler').removeClass('show');
                    demoHideOverlay();
                }else{
                    $('.aiz-demo-nav-toggler').addClass('show');
                    demoShowOverlay();
                }
            }

            $('.aiz-demos').click(function(e){
                if (!e.target.closest('.aiz-demos .aiz-demo-content')) {
                    toggleDemoNav();
                }
            });

            function demoShowOverlay(){
                $('.top-banner').removeClass('z-1035').addClass('z-1');
                $('.top-navbar').removeClass('z-1035').addClass('z-1');
                $('header').removeClass('z-1020').addClass('z-1');
                $('.aiz-demos').addClass('show');
            }

            function demoHideOverlay(cls=null){
                if($('.aiz-demos').hasClass('show')){
                    $('.aiz-demos').removeClass('show');
                    $('.top-banner').delay(800).removeClass('z-1').addClass('z-1035');
                    $('.top-navbar').delay(800).removeClass('z-1').addClass('z-1035');
                    $('header').delay(800).removeClass('z-1').addClass('z-1020');
                }
            }
        </script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.0/nouislider.min.js" integrity="sha512-HtgITRKzMMQyqL8sM+uxKqjmU/V8A/3LtmC5YcMlpzJ0j/jF5o/rY+T42pYJ5Q3m4s/0i+5K/1R+O45pC/yA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    @endif



    <script>
        // Global function for user dropdown toggle - ensure it's always available
        window.toggleUserDropdown = function() {
            const dropdown = document.querySelector('.hover-user-top-menu');
            if (dropdown) {
                dropdown.classList.toggle('show-dropdown');
            }
        }
    </script>

    @yield('script')

    @php
        echo get_setting('footer_script');
    @endphp

</body>
</html>

