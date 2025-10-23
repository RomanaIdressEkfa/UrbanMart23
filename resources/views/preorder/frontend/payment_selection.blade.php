@extends('frontend.layouts.app')

@section('content')

{{-- পেজের শিরোনাম এবং ব্রেডক্রাম্ব --}}
<section class="pt-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 text-center text-lg-left">
                <h1 class="fw-600 h4">{{ translate('Pre-order Payment') }}</h1>
            </div>
            <div class="col-lg-6">
                <ul class="breadcrumb bg-transparent p-0 justify-content-center justify-content-lg-end">
                    <li class="breadcrumb-item opacity-50">
                        <a class="text-reset" href="{{ route('home') }}">{{ translate('Home')}}</a>
                    </li>
                    <li class="text-dark fw-600 breadcrumb-item">
                        "{{ translate('Pre-order Payment') }}"
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="mb-4">
    <div class="container">
        <form class="form-default" action="{{ route('preorder.process_payment') }}" role="form" method="POST" id="preorder-checkout-form">
            @csrf
            <div class="row">
                {{-- বাম কলাম: প্রি-অর্ডার আইটেম এবং শিপিং তথ্য --}}
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 rounded-0 mb-4">
                        <div class="card-header p-3">
                            <h3 class="fs-16 fw-600 mb-0">{{ translate('Pre-order Items') }}</h3>
                        </div>
                        <div class="card-body">
                            @include('preorder.frontend.partials.preorder_items', ['preorders' => $preorders])
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 rounded-0">
                        <div class="card-header p-3">
                            <h3 class="fs-16 fw-600 mb-0">{{ translate('Shipping Information') }}</h3>
                        </div>
                        <div class="card-body">
                            @include('preorder.frontend.partials.modern_shipping_info')
                        </div>
                    </div>
                </div>

                {{-- ডান কলাম: সামারি এবং পেমেন্ট --}}
                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div class="card shadow-sm border-0 rounded-0">
                        {{-- Order Summary --}}
                        <div id="preorder_summary_container">
                            @include('preorder.frontend.partials.preorder_summary', ['preorders_for_summary' => $preorders, 'advance_amount' => $total_amount])
                        </div>

                        {{-- Payment Method --}}
                        <div class="card-header p-3">
                            <h3 class="fs-16 fw-600 mb-0">{{ translate('Select Payment Method') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="aiz-megabox d-block">
                                    <input value="sslcommerz" class="online_payment" type="radio" name="payment_option" checked>
                                    <span class="d-flex p-3 aiz-megabox-elem">
                                        <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                        <span class="flex-grow-1 pl-3 fw-600">SSLCommerz</span>
                                    </span>
                                </label>
                            </div>
                            <div class="mb-4">
                                <label class="aiz-megabox d-block">
                                    <input value="bkash" class="online_payment" type="radio" name="payment_option">
                                    <span class="d-flex p-3 aiz-megabox-elem">
                                        <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                        <span class="flex-grow-1 pl-3 fw-600">bKash</span>
                                    </span>
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary fw-600 btn-block">{{ translate('Complete Checkout') }} →</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

@endsection
