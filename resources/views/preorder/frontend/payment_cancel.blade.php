@extends('frontend.layouts.app')

@section('content')
<section class="pt-5 mb-4">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 mx-auto">
                <div class="row aiz-steps arrow-divider">
                    <div class="col done">
                        <div class="text-center text-success">
                            <i class="la-3x mb-2 las la-shopping-cart"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block">{{ translate('1. My Cart') }}</h3>
                        </div>
                    </div>
                    <div class="col done">
                        <div class="text-center text-success">
                            <i class="la-3x mb-2 las la-map"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block">{{ translate('2. Shipping info') }}</h3>
                        </div>
                    </div>
                    <div class="col active">
                        <div class="text-center text-primary">
                            <i class="la-3x mb-2 las la-truck"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block">{{ translate('3. Pre-order Payment') }}</h3>
                        </div>
                    </div>
                    <div class="col">
                        <div class="text-center">
                            <i class="la-3x mb-2 opacity-50 las la-credit-card"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block opacity-50">{{ translate('4. Confirmation') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mohammad Hassan -->
<section class="mb-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="las la-times-circle text-danger" style="font-size: 5rem;"></i>
                        </div>
                        
                        <h2 class="fw-600 mb-3 text-danger">{{ translate('Payment Cancelled') }}</h2>
                        
                        <p class="fs-16 mb-4 text-muted">
                            {{ translate('Your pre-order payment was cancelled. Don\'t worry, you can try again or choose a different payment method.') }}
                        </p>

                        <div class="alert alert-warning mb-4">
                            <div class="row align-items-center">
                                <div class="col-md-2 text-center">
                                    <i class="las la-exclamation-triangle fs-24"></i>
                                </div>
                                <div class="col-md-10 text-left">
                                    <h6 class="mb-2">{{ translate('What can you do now?') }}</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-1">
                                            <i class="las la-redo text-primary mr-2"></i>
                                            {{ translate('Try the payment again with the same method') }}
                                        </li>
                                        <li class="mb-1">
                                            <i class="las la-credit-card text-primary mr-2"></i>
                                            {{ translate('Choose a different payment method') }}
                                        </li>
                                        <li class="mb-1">
                                            <i class="las la-phone text-primary mr-2"></i>
                                            {{ translate('Contact our support team for assistance') }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('preorder.payment_selection') }}" class="btn btn-primary btn-block">
                                    <i class="las la-redo mr-2"></i>{{ translate('Try Again') }}
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('home') }}" class="btn btn-outline-primary btn-block">
                                    <i class="las la-home mr-2"></i>{{ translate('Go Home') }}
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('support_ticket.index') }}" class="btn btn-outline-secondary btn-block">
                                    <i class="las la-question-circle mr-2"></i>{{ translate('Get Help') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ translate('Common Payment Issues') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <h6 class="text-primary mb-2">
                                        <i class="las la-credit-card mr-2"></i>{{ translate('Card Issues') }}
                                    </h6>
                                    <ul class="list-unstyled text-muted">
                                        <li class="mb-1">• {{ translate('Insufficient funds') }}</li>
                                        <li class="mb-1">• {{ translate('Expired card') }}</li>
                                        <li class="mb-1">• {{ translate('Incorrect card details') }}</li>
                                        <li class="mb-1">• {{ translate('Card blocked by bank') }}</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <h6 class="text-primary mb-2">
                                        <i class="las la-wifi mr-2"></i>{{ translate('Technical Issues') }}
                                    </h6>
                                    <ul class="list-unstyled text-muted">
                                        <li class="mb-1">• {{ translate('Poor internet connection') }}</li>
                                        <li class="mb-1">• {{ translate('Browser compatibility') }}</li>
                                        <li class="mb-1">• {{ translate('Payment gateway timeout') }}</li>
                                        <li class="mb-1">• {{ translate('Session expired') }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <h6 class="mb-2">{{ translate('Need Help?') }}</h6>
                            <p class="mb-2">{{ translate('If you continue to experience issues, please contact our support team:') }}</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1">
                                        <i class="las la-phone mr-2"></i>
                                        <strong>{{ translate('Phone:') }}</strong> {{ get_setting('contact_phone') }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1">
                                        <i class="las la-envelope mr-2"></i>
                                        <strong>{{ translate('Email:') }}</strong> {{ get_setting('contact_email') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ translate('Alternative Payment Methods') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if(get_setting('cash_payment') == 1)
                            <div class="col-md-4 mb-3">
                                <div class="text-center p-3 border rounded">
                                    <i class="las la-money-bill-wave fs-24 text-success mb-2"></i>
                                    <h6>{{ translate('Cash on Delivery') }}</h6>
                                    <p class="text-muted small mb-0">{{ translate('Pay when you receive') }}</p>
                                </div>
                            </div>
                            @endif
                            
                            @if(get_setting('paypal_payment_activation') == 1)
                            <div class="col-md-4 mb-3">
                                <div class="text-center p-3 border rounded">
                                    <i class="lab la-paypal fs-24 text-primary mb-2"></i>
                                    <h6>{{ translate('PayPal') }}</h6>
                                    <p class="text-muted small mb-0">{{ translate('Secure online payment') }}</p>
                                </div>
                            </div>
                            @endif
                            
                            @if(get_setting('stripe_payment_activation') == 1)
                            <div class="col-md-4 mb-3">
                                <div class="text-center p-3 border rounded">
                                    <i class="lab la-stripe fs-24 text-info mb-2"></i>
                                    <h6>{{ translate('Stripe') }}</h6>
                                    <p class="text-muted small mb-0">{{ translate('Credit/Debit cards') }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

