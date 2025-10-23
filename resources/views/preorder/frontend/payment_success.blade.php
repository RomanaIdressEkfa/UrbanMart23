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
                    <div class="col done">
                        <div class="text-center text-success">
                            <i class="la-3x mb-2 las la-truck"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block">{{ translate('3. Pre-order Payment') }}</h3>
                        </div>
                    </div>
                    <div class="col done">
                        <div class="text-center text-success">
                            <i class="la-3x mb-2 las la-credit-card"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block">{{ translate('4. Confirmation') }}</h3>
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
                            <i class="las la-check-circle text-success" style="font-size: 5rem;"></i>
                        </div>
                        
                        <h2 class="fw-600 mb-3 text-success">{{ translate('Pre-order Confirmed!') }}</h2>
                        
                        <p class="fs-16 mb-4 text-muted">
                            {{ translate('Thank you for your pre-order. Your payment has been processed successfully.') }}
                        </p>

                        <div class="alert alert-info mb-4">
                            <div class="row align-items-center">
                                <div class="col-md-2 text-center">
                                    <i class="las la-info-circle fs-24"></i>
                                </div>
                                <div class="col-md-10 text-left">
                                    <h6 class="mb-2">{{ translate('What happens next?') }}</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-1">
                                            <i class="las la-check text-success mr-2"></i>
                                            {{ translate('We will notify you via email and SMS when your products arrive') }}
                                        </li>
                                        <li class="mb-1">
                                            <i class="las la-check text-success mr-2"></i>
                                            {{ translate('You can track your pre-order status in your account') }}
                                        </li>
                                        <li class="mb-1">
                                            <i class="las la-check text-success mr-2"></i>
                                            {{ translate('Complete the remaining payment when products are ready') }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <a href="{{ route('preorder.order_list') }}" class="btn btn-primary btn-block">
                                    <i class="las la-list mr-2"></i>{{ translate('View My Pre-orders') }}
                                </a>
                            </div>
                            <div class="col-md-6 mb-3">
                                <a href="{{ route('home') }}" class="btn btn-outline-primary btn-block">
                                    <i class="las la-home mr-2"></i>{{ translate('Continue Shopping') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ translate('Pre-order Process Timeline') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item completed">
                                <div class="timeline-marker bg-success">
                                    <i class="las la-check text-white"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">{{ translate('Pre-order Placed') }}</h6>
                                    <p class="text-muted mb-0">{{ translate('Your pre-order has been confirmed and payment processed') }}</p>
                                    <small class="text-success">{{ translate('Completed') }}</small>
                                </div>
                            </div>
                            
                            <div class="timeline-item pending">
                                <div class="timeline-marker bg-warning">
                                    <i class="las la-clock text-white"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">{{ translate('Waiting for Product Arrival') }}</h6>
                                    <p class="text-muted mb-0">{{ translate('We are working to get your products in stock') }}</p>
                                    <small class="text-warning">{{ translate('In Progress') }}</small>
                                </div>
                            </div>
                            
                            <div class="timeline-item pending">
                                <div class="timeline-marker bg-secondary">
                                    <i class="las la-bell text-white"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">{{ translate('Product Arrival Notification') }}</h6>
                                    <p class="text-muted mb-0">{{ translate('You will receive notification when products are available') }}</p>
                                    <small class="text-muted">{{ translate('Pending') }}</small>
                                </div>
                            </div>
                            
                            <div class="timeline-item pending">
                                <div class="timeline-marker bg-secondary">
                                    <i class="las la-credit-card text-white"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">{{ translate('Complete Final Payment') }}</h6>
                                    <p class="text-muted mb-0">{{ translate('Pay the remaining amount and receive your products') }}</p>
                                    <small class="text-muted">{{ translate('Pending') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid #fff;
    box-shadow: 0 0 0 3px #e9ecef;
}

.timeline-item.completed .timeline-marker {
    box-shadow: 0 0 0 3px #28a745;
}

.timeline-content {
    padding-left: 20px;
}

.timeline-item:last-child {
    margin-bottom: 0;
}
</style>
@endsection

