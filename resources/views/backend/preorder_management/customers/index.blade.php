@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Pre-Order Customer Management') }}</h1>
        </div>
        <div class="col-md-6 text-md-right">
            <a href="{{ route('preorder_management.dashboard') }}" class="btn btn-circle btn-info">
                <span>{{ translate('Dashboard') }}</span>
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row gutters-16 mb-4">
    <!-- Total Pre-Orders -->
    <div class="col-lg-3 col-md-6">
        <div class="dashboard-box bg-white h-150px mb-2rem overflow-hidden">
            <div class="d-flex flex-column justify-content-between h-100 p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <h1 class="fs-24 fw-600 text-primary mb-1">{{ $stats['total_preorders'] }}</h1>
                        <h3 class="fs-12 fw-600 text-secondary mb-0">{{ translate('Total Pre-Orders') }}</h3>
                    </div>
                    <div class="mt-2">
                        <i class="las la-shopping-cart text-primary fs-30"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Amount -->
    <div class="col-lg-3 col-md-6">
        <div class="dashboard-box bg-white h-150px mb-2rem overflow-hidden">
            <div class="d-flex flex-column justify-content-between h-100 p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <h1 class="fs-24 fw-600 text-success mb-1">{{ single_price($stats['total_amount']) }}</h1>
                        <h3 class="fs-12 fw-600 text-secondary mb-0">{{ translate('Total Amount') }}</h3>
                    </div>
                    <div class="mt-2">
                        <i class="las la-dollar-sign text-success fs-30"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Paid -->
    <div class="col-lg-3 col-md-6">
        <div class="dashboard-box bg-white h-150px mb-2rem overflow-hidden">
            <div class="d-flex flex-column justify-content-between h-100 p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <h1 class="fs-24 fw-600 text-info mb-1">{{ single_price($stats['total_paid']) }}</h1>
                        <h3 class="fs-12 fw-600 text-secondary mb-0">{{ translate('Total Paid') }}</h3>
                    </div>
                    <div class="mt-2">
                        <i class="las la-credit-card text-info fs-30"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Amount -->
    <div class="col-lg-3 col-md-6">
        <div class="dashboard-box bg-white h-150px mb-2rem overflow-hidden">
            <div class="d-flex flex-column justify-content-between h-100 p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <h1 class="fs-24 fw-600 text-warning mb-1">{{ single_price($stats['pending_amount']) }}</h1>
                        <h3 class="fs-12 fw-600 text-secondary mb-0">{{ translate('Pending Amount') }}</h3>
                    </div>
                    <div class="mt-2">
                        <i class="las la-clock text-warning fs-30"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- User Type Statistics -->
<div class="row gutters-16 mb-4">
    <!-- Guest Orders -->
    <div class="col-lg-4 col-md-4">
        <div class="dashboard-box bg-white h-120px mb-2rem overflow-hidden">
            <div class="d-flex flex-column justify-content-center h-100 p-3 text-center">
                <h1 class="fs-20 fw-600 text-secondary mb-1">{{ $stats['guest_orders'] }}</h1>
                <h3 class="fs-11 fw-600 text-secondary mb-0">{{ translate('Guest Orders') }}</h3>
            </div>
        </div>
    </div>

    <!-- Customer Orders -->
    <div class="col-lg-4 col-md-4">
        <div class="dashboard-box bg-white h-120px mb-2rem overflow-hidden">
            <div class="d-flex flex-column justify-content-center h-100 p-3 text-center">
                <h1 class="fs-20 fw-600 text-primary mb-1">{{ $stats['customer_orders'] }}</h1>
                <h3 class="fs-11 fw-600 text-secondary mb-0">{{ translate('Customer Orders') }}</h3>
            </div>
        </div>
    </div>

    <!-- Wholesaler Orders -->
    <div class="col-lg-4 col-md-4">
        <div class="dashboard-box bg-white h-120px mb-2rem overflow-hidden">
            <div class="d-flex flex-column justify-content-center h-100 p-3 text-center">
                <h1 class="fs-20 fw-600 text-info mb-1">{{ $stats['wholesaler_orders'] }}</h1>
                <h3 class="fs-11 fw-600 text-secondary mb-0">{{ translate('Wholesaler Orders') }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Filters and Search -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-gradient-primary text-white border-0">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-white">
                <i class="las la-users mr-2"></i>{{ translate('Pre-Order Customers') }}
            </h5>
            <div class="d-flex align-items-center">
                <span class="badge badge-light text-primary mr-2">
                    {{ $preorders->total() }} {{ translate('Total Orders') }}
                </span>
                <button class="btn btn-sm btn-outline-light" type="button" data-toggle="collapse" data-target="#advancedFilters" aria-expanded="false">
                    <i class="las la-filter mr-1"></i>{{ translate('Advanced Filters') }}
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form class="" id="sort_customers" action="" method="GET">
            <!-- Basic Search Row -->
            <div class="row gutters-10 mb-3">
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label class="form-label text-muted small mb-1">
                            <i class="las la-user-tag mr-1"></i>{{ translate('User Type') }}
                        </label>
                        <select class="form-control aiz-selectpicker border-primary" name="user_type" onchange="sort_customers()">
                            <option value="all" @if($user_type == 'all') selected @endif>{{ translate('All Users') }}</option>
                            <option value="guest" @if($user_type == 'guest') selected @endif>{{ translate('Guest Users') }}</option>
                            <option value="customer" @if($user_type == 'customer') selected @endif>{{ translate('Customers') }}</option>
                            <option value="wholesaler" @if($user_type == 'wholesaler') selected @endif>{{ translate('Wholesalers') }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-0">
                        <label class="form-label text-muted small mb-1">
                            <i class="las la-search mr-1"></i>{{ translate('Search') }}
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-primary text-white border-primary">
                                    <i class="las la-search"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control border-primary" id="search" name="search" 
                                   @isset($sort_search) value="{{ $sort_search }}" @endisset 
                                   placeholder="{{ translate('Order Code, Name, Email or Phone') }}">
                            <div class="input-group-append">
                                <button class="btn btn-outline-primary" type="button" onclick="clearSearch()">
                                    <i class="las la-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small mb-1">&nbsp;</label>
                    <div class="d-flex">
                        <button class="btn btn-primary mr-2 flex-fill" type="submit">
                            <i class="las la-filter mr-1"></i>{{ translate('Filter') }}
                        </button>
                        <a href="{{ route('preorder_management.customers') }}" class="btn btn-outline-secondary flex-fill">
                            <i class="las la-redo mr-1"></i>{{ translate('Reset') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Advanced Filters (Collapsible) -->
            <div class="collapse" id="advancedFilters">
                <div class="border-top pt-3">
                    <div class="row gutters-10">
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label class="form-label text-muted small mb-1">
                                    <i class="las la-calendar mr-1"></i>{{ translate('Date Range') }}
                                </label>
                                <select class="form-control aiz-selectpicker" name="date_range">
                                    <option value="">{{ translate('All Time') }}</option>
                                    <option value="today">{{ translate('Today') }}</option>
                                    <option value="yesterday">{{ translate('Yesterday') }}</option>
                                    <option value="last_7_days">{{ translate('Last 7 Days') }}</option>
                                    <option value="last_30_days">{{ translate('Last 30 Days') }}</option>
                                    <option value="this_month">{{ translate('This Month') }}</option>
                                    <option value="last_month">{{ translate('Last Month') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label class="form-label text-muted small mb-1">
                                    <i class="las la-credit-card mr-1"></i>{{ translate('Payment Method') }}
                                </label>
                                <select class="form-control aiz-selectpicker" name="payment_method">
                                    <option value="">{{ translate('All Methods') }}</option>
                                    <option value="bkash">bKash</option>
                                    <option value="sslcommerz">SSLCommerz</option>
                                    <option value="cash_on_delivery">{{ translate('Cash on Delivery') }}</option>
                                    <option value="wallet">{{ translate('Wallet') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label class="form-label text-muted small mb-1">
                                    <i class="las la-sort-amount-down mr-1"></i>{{ translate('Sort By') }}
                                </label>
                                <select class="form-control aiz-selectpicker" name="sort_by">
                                    <option value="newest">{{ translate('Newest First') }}</option>
                                    <option value="oldest">{{ translate('Oldest First') }}</option>
                                    <option value="amount_high">{{ translate('Amount: High to Low') }}</option>
                                    <option value="amount_low">{{ translate('Amount: Low to High') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label class="form-label text-muted small mb-1">
                                    <i class="las la-list mr-1"></i>{{ translate('Per Page') }}
                                </label>
                                <select class="form-control aiz-selectpicker" name="per_page">
                                    <option value="15">15</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Pre-Orders Table -->
<div class="card shadow-sm border-0 mt-4">
    <div class="card-header bg-white border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-dark font-weight-bold">
                <i class="las la-table mr-2 text-primary"></i>{{ translate('Pre-Order Management') }}
            </h6>
            <div class="d-flex align-items-center">
                <span class="badge badge-primary mr-2">{{ $preorders->total() }} {{ translate('Orders') }}</span>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">
                        <i class="las la-download mr-1"></i>{{ translate('Export') }}
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#"><i class="las la-file-excel mr-2"></i>{{ translate('Excel') }}</a>
                        <a class="dropdown-item" href="#"><i class="las la-file-pdf mr-2"></i>{{ translate('PDF') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 modern-table">
                <thead class="thead-light">
                    <tr>
                        <th class="border-0 text-center" style="width: 50px;">#</th>
                        <th class="border-0">{{ translate('Order Info') }}</th>
                        <th class="border-0 text-center">{{ translate('User Type') }}</th>
                        <th class="border-0">{{ translate('Customer Details') }}</th>
                        <th class="border-0 text-center">{{ translate('Products') }}</th>
                        <th class="border-0 text-center">{{ translate('Payment Status') }}</th>
                        <th class="border-0 text-center">{{ translate('Delivery Info') }}</th>
                        <th class="border-0 text-center">{{ translate('Status') }}</th>
                        <th class="border-0 text-center">{{ translate('Actions') }}</th>
                    </tr>
                </thead>
            <tbody>
                @foreach($preorders as $key => $order)
                <tr class="border-bottom">
                    <td class="text-center align-middle">
                        <span class="badge badge-light">{{ ($key+1) + ($preorders->currentPage() - 1)*$preorders->perPage() }}</span>
                    </td>
                    
                    <!-- Order Info -->
                    <td class="align-middle">
                        <div class="d-flex flex-column">
                            <h6 class="mb-1 text-primary font-weight-bold">{{ $order->code }}</h6>
                            <small class="text-muted">
                                <i class="las la-calendar mr-1"></i>{{ $order->created_at->format('d M Y, h:i A') }}
                            </small>
                            <small class="text-info">
                                <i class="las la-clock mr-1"></i>{{ $order->days_since_order }} {{ translate('days ago') }}
                            </small>
                        </div>
                    </td>
                    
                    <!-- User Type -->
                    <td class="text-center align-middle">
                        @if($order->customer_type == 'Guest')
                            <div class="d-flex flex-column align-items-center">
                                <div class="avatar avatar-sm mb-2 bg-secondary">
                                    <i class="las la-user-secret text-white"></i>
                                </div>
                                <span class="badge badge-secondary">
                                    {{ translate('Guest') }}
                                </span>
                            </div>
                        @elseif($order->customer_type == 'Wholesaler')
                            <div class="d-flex flex-column align-items-center">
                                <div class="avatar avatar-sm mb-2 bg-info">
                                    <i class="las la-store text-white"></i>
                                </div>
                                <span class="badge badge-info">
                                    {{ translate('Wholesaler') }}
                                </span>
                            </div>
                        @else
                            <div class="d-flex flex-column align-items-center">
                                <div class="avatar avatar-sm mb-2 bg-success">
                                    <i class="las la-user text-white"></i>
                                </div>
                                <span class="badge badge-success">
                                    {{ translate('Customer') }}
                                </span>
                            </div>
                        @endif
                    </td>
                    
                    <!-- Customer Details -->
                    <td class="align-middle">
                        <div class="d-flex align-items-center">
                            <div class="mr-3">
                                <h6 class="mb-1 font-weight-bold">{{ $order->customer_name }}</h6>
                                <div class="text-muted small">
                                    <i class="las la-envelope mr-1"></i>{{ $order->customer_email }}
                                </div>
                                @if($order->customer_phone)
                                    <div class="text-muted small">
                                        <i class="las la-phone mr-1"></i>{{ $order->customer_phone }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </td>
                    
                    <!-- Products -->
                    <td class="text-center align-middle">
                        <div class="d-flex flex-column align-items-center">
                            <div class="bg-light rounded-circle p-2 mb-2" style="width: 40px; height: 40px;">
                                <i class="las la-box text-primary"></i>
                            </div>
                            <span class="font-weight-bold">{{ $order->product_count }}</span>
                            <small class="text-muted">{{ translate('Products') }}</small>
                            <small class="text-info">{{ translate('Qty') }}: {{ $order->total_quantity }}</small>
                        </div>
                    </td>
                    
                    <!-- Payment Status -->
                    <td class="text-center align-middle">
                        <div class="payment-card bg-light rounded p-3">
                            <div class="mb-2">
                                <h6 class="mb-1 font-weight-bold text-dark">{{ single_price($order->grand_total) }}</h6>
                                <small class="text-muted">{{ translate('Total Amount') }}</small>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-success small">{{ translate('Paid') }}:</span>
                                <span class="text-success font-weight-bold">{{ single_price($order->paid_amount) }}</span>
                            </div>
                            
                            @if($order->remaining_amount > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-warning small">{{ translate('Due') }}:</span>
                                <span class="text-warning font-weight-bold">{{ single_price($order->remaining_amount) }}</span>
                            </div>
                            @endif
                            
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-gradient-success" role="progressbar" style="width: {{ $order->payment_percentage }}%"></div>
                            </div>
                            <small class="text-muted">{{ number_format($order->payment_percentage, 1) }}% {{ translate('Paid') }}</small>
                            
                            @if($order->payment_method)
                            <div class="mt-2">
                                <span class="badge badge-info">
                                    @if($order->payment_method == 'bkash')
                                        bKash
                                    @elseif($order->payment_method == 'sslcommerz')
                                        SSLCommerz
                                    @elseif($order->payment_method == 'cash_on_delivery')
                                        {{ translate('COD') }}
                                    @elseif($order->payment_method == 'wallet')
                                        {{ translate('Wallet') }}
                                    @else
                                        {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}
                                    @endif
                                </span>
                            </div>
                            @endif
                        </div>
                    </td>
                    
                    <!-- Delivery Info -->
                    <td class="text-center align-middle">
                        <div class="delivery-info">
                            @if($order->delivery_date)
                                <div class="mb-2">
                                    <div class="bg-light rounded-circle p-2 mb-2 d-inline-flex">
                                        <i class="las la-calendar text-info"></i>
                                    </div>
                                    <div class="small font-weight-bold">{{ \Carbon\Carbon::parse($order->delivery_date)->format('d M Y') }}</div>
                                </div>
                                @if($order->days_to_delivery !== null)
                                    @if($order->days_to_delivery > 0)
                                        <span class="badge badge-info">{{ $order->days_to_delivery }} {{ translate('days left') }}</span>
                                    @elseif($order->days_to_delivery == 0)
                                        <span class="badge badge-warning">{{ translate('Today') }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ abs($order->days_to_delivery) }} {{ translate('days overdue') }}</span>
                                    @endif
                                @endif
                            @else
                                <div class="text-muted">
                                    <i class="las la-calendar-times text-muted"></i>
                                    <div class="small">{{ translate('Not Set') }}</div>
                                </div>
                            @endif
                            
                            @if($order->delivery_location)
                                <div class="mt-2">
                                    <i class="las la-map-marker text-danger"></i>
                                    <div class="small text-muted">{{ Str::limit($order->delivery_location, 15) }}</div>
                                </div>
                            @endif
                        </div>
                    </td>
                    
                    <!-- Status -->
                    <td class="text-center align-middle">
                        <div class="status-column">
                            @php
                                $statusClass = 'secondary';
                                $statusText = $order->preorder_status ?? 'pending';
                                
                                switch($statusText) {
                                    case 'confirmed':
                                        $statusClass = 'success';
                                        break;
                                    case 'processing':
                                        $statusClass = 'info';
                                        break;
                                    case 'shipped':
                                        $statusClass = 'primary';
                                        break;
                                    case 'delivered':
                                        $statusClass = 'success';
                                        break;
                                    case 'cancelled':
                                        $statusClass = 'danger';
                                        break;
                                }
                            @endphp
                            <span class="badge badge-{{ $statusClass }} badge-pill px-3 py-2 mb-1">
                                {{ translate(ucfirst($statusText)) }}
                            </span>
                            
                            @if($order->delivery_status)
                                <br>
                                <span class="badge badge-outline-primary badge-pill px-3 py-1">
                                    {{ translate(ucfirst($order->delivery_status)) }}
                                </span>
                            @endif
                        </div>
                    </td>
                    
                    <!-- Actions -->
                    <td class="text-center align-middle">
                        <div class="btn-group" role="group">
                            <a href="{{ route('preorders.show', $order->id) }}" class="btn btn-sm btn-outline-primary" title="{{ translate('View Details') }}">
                                <i class="las la-eye"></i>
                            </a>
                            <a href="{{ route('preorder_management.customers.details', $order->id) }}" class="btn btn-sm btn-outline-info" title="{{ translate('Customer Details') }}">
                                <i class="las la-user"></i>
                            </a>
                            @if($order->remaining_amount > 0)
                                <button class="btn btn-sm btn-outline-success" onclick="updatePayment({{ $order->id }})" title="{{ translate('Update Payment') }}">
                                    <i class="las la-credit-card"></i>
                                </button>
                            @endif
                        </div>
                        
                        <div class="dropdown mt-2">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                                <i class="las la-ellipsis-h"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('preorders.show', $order->id) }}">
                                    <i class="las la-eye mr-2"></i>{{ translate('View Details') }}
                                </a>
                                <a class="dropdown-item" href="{{ route('preorder_management.customers.details', $order->id) }}">
                                    <i class="las la-user mr-2"></i>{{ translate('Customer Details') }}
                                </a>
                                @if($order->remaining_amount > 0)
                                    <a class="dropdown-item" href="#" onclick="updatePayment({{ $order->id }})">
                                        <i class="las la-credit-card mr-2"></i>{{ translate('Update Payment') }}
                                    </a>
                                @endif
                                <a class="dropdown-item" href="#" onclick="updateDelivery({{ $order->id }})">
                                    <i class="las la-truck mr-2"></i>{{ translate('Update Delivery') }}
                                </a>
                                <a class="dropdown-item" href="#" onclick="updateStatus({{ $order->id }})">
                                    <i class="las la-edit mr-2"></i>{{ translate('Update Status') }}
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
    
    <!-- Enhanced Pagination Section -->
    <div class="card-footer bg-light border-0">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="d-flex align-items-center text-muted">
                    <i class="las la-info-circle mr-2"></i>
                    <span>
                        {{ translate('Showing') }} 
                        <strong>{{ $preorders->firstItem() ?? 0 }}</strong> 
                        {{ translate('to') }} 
                        <strong>{{ $preorders->lastItem() ?? 0 }}</strong> 
                        {{ translate('of') }} 
                        <strong>{{ $preorders->total() }}</strong> 
                        {{ translate('results') }}
                    </span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    <div class="aiz-pagination">
                        {{ $preorders->appends(request()->input())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Update Modal -->
<div class="modal fade" id="payment-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ translate('Update Payment') }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('preorder_management.update_payment') }}" method="POST">
                @csrf
                <input type="hidden" name="order_id" id="payment_order_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ translate('Additional Payment Amount') }}</label>
                        <input type="number" step="0.01" class="form-control" name="payment_amount" required>
                    </div>
                    <div class="form-group">
                        <label>{{ translate('Payment Notes') }}</label>
                        <textarea class="form-control" name="payment_notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ translate('Update Payment') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delivery Update Modal -->
<div class="modal fade" id="delivery-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ translate('Update Delivery Information') }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('preorder_management.update_delivery') }}" method="POST">
                @csrf
                <input type="hidden" name="order_id" id="delivery_order_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ translate('Delivery Date') }}</label>
                        <input type="date" class="form-control" name="delivery_date">
                    </div>
                    <div class="form-group">
                        <label>{{ translate('Delivery Location') }}</label>
                        <input type="text" class="form-control" name="delivery_location" placeholder="{{ translate('Enter delivery address') }}">
                    </div>
                    <div class="form-group">
                        <label>{{ translate('Delivery Notes') }}</label>
                        <textarea class="form-control" name="delivery_notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ translate('Update Delivery') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="status-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ translate('Update Order Status') }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('preorder_management.update_status') }}" method="POST">
                @csrf
                <input type="hidden" name="order_id" id="status_order_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ translate('Pre-Order Status') }}</label>
                        <select class="form-control" name="preorder_status" required>
                            <option value="pending">{{ translate('Pending') }}</option>
                            <option value="confirmed">{{ translate('Confirmed') }}</option>
                            <option value="processing">{{ translate('Processing') }}</option>
                            <option value="shipped">{{ translate('Shipped') }}</option>
                            <option value="delivered">{{ translate('Delivered') }}</option>
                            <option value="cancelled">{{ translate('Cancelled') }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{ translate('Delivery Status') }}</label>
                        <select class="form-control" name="delivery_status">
                            <option value="">{{ translate('Select Status') }}</option>
                            <option value="pending">{{ translate('Pending') }}</option>
                            <option value="in_transit">{{ translate('In Transit') }}</option>
                            <option value="out_for_delivery">{{ translate('Out for Delivery') }}</option>
                            <option value="delivered">{{ translate('Delivered') }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{ translate('Status Notes') }}</label>
                        <textarea class="form-control" name="status_notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ translate('Update Status') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('style')
<style>
/* Simple Table Styling */
.table {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.table thead th {
    background-color: #f8f9fa;
    color: #495057;
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
    padding: 12px 15px;
    font-size: 14px;
}

.table tbody tr {
    border-bottom: 1px solid #dee2e6;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.table tbody td {
    padding: 12px 15px;
    vertical-align: middle;
    font-size: 14px;
}

/* Simple Badge Styling */
.badge {
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

/* Simple Button Styling */
.btn {
    border-radius: 4px;
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 500;
    border: 1px solid transparent;
}

.btn-sm {
    padding: 4px 8px;
    font-size: 11px;
}

/* Simple Card Styling */
.card {
    border-radius: 8px;
    border: 1px solid #dee2e6;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 15px 20px;
}

.card-body {
    padding: 20px;
}

/* Simple Form Styling */
.form-control {
    border-radius: 4px;
    border: 1px solid #ced4da;
    padding: 8px 12px;
}

.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Simple Avatar */
.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #dee2e6;
}

/* Simple Progress Bar */
.progress {
    height: 6px;
    border-radius: 3px;
    background-color: #e9ecef;
}

.progress-bar {
    border-radius: 3px;
}

/* Filter Section */
.filter-section {
    background-color: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    padding: 20px;
    margin-bottom: 20px;
}

/* Payment Card */
.payment-card {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    background-color: #fff;
}

/* Delivery Info */
.delivery-info {
    padding: 10px 15px;
    border-radius: 4px;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
}

/* Status Column */
.status-column {
    text-align: center;
}

/* Responsive Design */
@media (max-width: 768px) {
    .table {
        font-size: 13px;
    }
    
    .table tbody td {
        padding: 10px 8px;
    }
    
    .btn {
        padding: 4px 8px;
        font-size: 11px;
    }
}
</style>
@endsection

@section('script')
<script type="text/javascript">
    function sort_customers() {
        var url = '{{ route('preorder_management.customers') }}';
        var user_type = $('select[name=user_type]').val();
        var search = $('input[name=search]').val();
        
        if (user_type != '' || search != '') {
            url += '?';
            if (user_type != '') {
                url += 'user_type=' + user_type;
            }
            if (search != '') {
                if (user_type != '') {
                    url += '&';
                }
                url += 'search=' + search;
            }
        }
        
        location.href = url;
    }

    function updatePayment(orderId) {
        $('#payment_order_id').val(orderId);
        $('#payment-modal').modal('show');
    }

    function updateDelivery(orderId) {
        $('#delivery_order_id').val(orderId);
        $('#delivery-modal').modal('show');
    }

    function updateStatus(orderId) {
        $('#status_order_id').val(orderId);
        $('#status-modal').modal('show');
    }

    $('#search').on('keyup', function(e) {
        if (e.keyCode == 13) {
            sort_customers();
        }
    });
</script>


@endsection

