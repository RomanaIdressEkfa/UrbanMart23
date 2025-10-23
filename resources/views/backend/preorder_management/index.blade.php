@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Pre-Order Dashboard') }}</h1>
        </div>
    </div>
</div>

<!-- Main Statistics Cards -->
<div class="row gutters-10">
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="las la-shopping-cart la-3x opacity-60"></i>
                    <div class="ml-3">
                        <h3 class="mb-0 h2 fw-600">{{ $stats['total_preorders'] }}</h3>
                        <h6 class="mb-0">{{ translate('Total Pre-orders') }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="las la-dollar-sign la-3x opacity-60"></i>
                    <div class="ml-3">
                        <h3 class="mb-0 h4 fw-600">{{ single_price($stats['total_revenue']) }}</h3>
                        <h6 class="mb-0">{{ translate('Total Revenue') }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="las la-hand-holding-usd la-3x text-success opacity-60"></i>
                    <div class="ml-3">
                        <h3 class="mb-0 h4 fw-600 text-success">{{ single_price($stats['total_prepayment_received']) }}</h3>
                        <h6 class="mb-0">{{ translate('Prepayment Received') }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="las la-file-invoice-dollar la-3x text-danger opacity-60"></i>
                    <div class="ml-3">
                        <h3 class="mb-0 h4 fw-600 text-danger">{{ single_price($stats['total_remaining_due']) }}</h3>
                        <h6 class="mb-0">{{ translate('Total Remaining Due') }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Overview -->
<div class="row gutters-10">
    <div class="col">
        <div class="card"><div class="card-body text-center"><p class="mb-1 h3 fw-600 text-warning">{{ $stats['pending_orders'] }}</p><h6 class="mb-0 fs-14">{{ translate('Pending') }}</h6></div></div>
    </div>
    <div class="col">
        <div class="card"><div class="card-body text-center"><p class="mb-1 h3 fw-600 text-primary">{{ $stats['confirmed_orders'] }}</p><h6 class="mb-0 fs-14">{{ translate('Confirmed') }}</h6></div></div>
    </div>
    <div class="col">
        <div class="card"><div class="card-body text-center"><p class="mb-1 h3 fw-600 text-info">{{ $stats['shipped_orders'] }}</p><h6 class="mb-0 fs-14">{{ translate('Shipped') }}</h6></div></div>
    </div>
    <div class="col">
        <div class="card"><div class="card-body text-center"><p class="mb-1 h3 fw-600 text-success">{{ $stats['completed_orders'] }}</p><h6 class="mb-0 fs-14">{{ translate('Completed') }}</h6></div></div>
    </div>
    <div class="col">
        <div class="card"><div class="card-body text-center"><p class="mb-1 h3 fw-600 text-danger">{{ $stats['cancelled_orders'] }}</p><h6 class="mb-0 fs-14">{{ translate('Cancelled') }}</h6></div></div>
    </div>
</div>

<!-- Other Info Cards -->
<div class="row gutters-10">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="las la-users la-3x opacity-60"></i>
                    <div class="ml-3">
                        <h3 class="mb-0 h2 fw-600">{{ $stats['total_customers'] }}</h3>
                        <h6 class="mb-0">{{ translate('Total Customers') }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="las la-box-open la-3x opacity-60"></i>
                    <div class="ml-3">
                        <h3 class="mb-0 h2 fw-600">{{ $stats['total_preorder_products'] }}</h3>
                        <h6 class="mb-0">{{ translate('Total Pre-order Products') }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Pre-Orders Table -->
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0 h6">{{ translate('Recent Pre-Orders') }}</h5>
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            {{-- আপনার টেবিলের বাকি কোড অপরিবর্তিত থাকবে --}}
             <thead>
                <tr>
                    <th>{{ translate('Order Code') }}</th>
                    <th>{{ translate('Customer Info') }}</th>
                    <th>{{ translate('Products & Quantity') }}</th>
                    <th>{{ translate('City') }}</th>
                    <th>{{ translate('Status') }}</th>
                    <th>{{ translate('Amount') }}</th>
                    <th>{{ translate('Payment Status') }}</th>
                    <th class="text-right">{{ translate('Action') }}</th>
                </tr>
            </thead>
         <tbody>
    @forelse($recent_preorders as $order_code => $products_in_order)
        @php
            $order = $products_in_order->first();
            $total_grand_for_group = $products_in_order->sum('grand_total');
            $total_paid_for_group = $products_in_order->sum('prepayment');
        @endphp
        <tr>
            <td><a href="{{ route('preorders.show', $order->id) }}" class="text-reset">{{ $order->order_code }}</a></td>
            <td>
                <div class="fs-12">
                    @if($order->user)
                        <strong>{{ $order->user->name }}</strong>
                        @if($order->user->user_type == 'wholesaler')
                            <span class="badge badge-inline badge-success ml-1">{{ translate('Wholesaler') }}</span>
                        @else
                            <span class="badge badge-inline badge-info ml-1">{{ translate('Customer') }}</span>
                        @endif
                        <br>
                        <span class="text-primary">{{ $order->user->email }}</span><br>
                        <span class="text-info">{{ $order->user->phone }}</span>
                    @else
                        {{-- পরিবর্তন: যদি নাম খালি থাকে, তাহলে 'Guest Order' দেখানো হবে --}}
                        <strong>{{ $order->shipping_name ?? translate('Guest Order') }}</strong>
                        <span class="badge badge-inline badge-secondary ml-1">{{ translate('Guest') }}</span>
                        <br>
                        <span class="text-primary">{{ $order->shipping_email }}</span><br>
                        <span class="text-info">{{ $order->shipping_phone }}</span>
                    @endif
                </div>
            </td>
            <td>
                <div class="fs-12 mt-1">
                    @foreach($products_in_order as $item)
                        <div class="mb-1">
                            <span>P: {{ Str::limit($item->variant_name, 20) }}</span>,
                            <span class="fw-600">Q: {{ $item->quantity }}</span>
                        </div>
                    @endforeach
                </div>
            </td>
            <td>{{ $order->shipping_city ?? 'N/A' }}</td>
            <td>
                 @php
                    $status_class = 'secondary';
                    if($order->status == 'payment_processing') $status_class = 'info';
                    if($order->status == 'confirmed') $status_class = 'primary';
                    if($order->status == 'shipped') $status_class = 'warning';
                    if($order->status == 'completed') $status_class = 'success';
                    if($order->status == 'cancelled') $status_class = 'danger';
                @endphp
                <span class="badge badge-inline badge-{{$status_class}}">{{ Str::title(str_replace('_', ' ', $order->status)) }}</span>
            </td>
            <td>{{ single_price($total_grand_for_group) }}</td>
            <td>
                @php
                    $paid_amount = $total_paid_for_group;
                    $remaining_amount = $total_grand_for_group - $paid_amount;
                @endphp
                 @if($paid_amount >= $total_grand_for_group)
                    <span class="badge badge-inline badge-success">{{ translate('Paid') }}</span>
                @elseif($paid_amount > 0)
                    <span class="badge badge-inline badge-info">{{ translate('Partially Paid') }}</span>
                @else
                    <span class="badge badge-inline badge-danger">{{ translate('Unpaid') }}</span>
                @endif
                <div class="fs-12 mt-1">
                    <span class="text-success">P: {{ single_price($paid_amount) }}</span><br>
                    <span class="text-danger">R: {{ single_price($remaining_amount) }}</span>
                </div>
            </td>
            <td class="text-right">
                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('preorders.show', $order->id) }}" title="{{ translate('View') }}">
                    <i class="las la-eye"></i>
                </a>
                {{-- নতুন ডিলিট বাটন এখানে যোগ করা হয়েছে --}}
                <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{ route('preorder.destroy', $order->order_code) }}" title="{{ translate('Delete') }}">
                    <i class="las la-trash"></i>
                </a>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="8" class="text-center">{{ translate('No recent pre-orders found') }}</td>
        </tr>
    @endforelse
</tbody>
        </table>
        <div class="aiz-pagination">
            {{ $recent_preorders->links() }}
        </div>
    </div>
</div>

@endsection
@section('modal')
    @include('modals.delete_modal')
@endsection