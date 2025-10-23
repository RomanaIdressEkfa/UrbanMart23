@extends('backend.layouts.app')

@section('content')

@php
    $order_items = \App\Models\Preorder::where('order_code', $order->order_code)->get();
    $total_subtotal = $order_items->sum('grand_total');
    $total_prepayment = $order_items->sum('prepayment');
    $grand_total = $total_subtotal;
    $remaining_amount = $grand_total - $total_prepayment;
@endphp

<div class="card">
    <div class="card-header row gutters-5">
        <div class="col text-center text-md-left">
            <h5 class="mb-md-0 h6">{{ translate('Pre-Order Details') }}</h5>
        </div>
        <div class="col-md-auto ml-auto">
             <a href="{{ route('preorder.invoice_download', $order->id) }}" class="btn btn-sm btn-light" type="button" title="Download Invoice"><i class="las la-download"></i></a>
             <a href="{{ route('preorder.invoice_preview', $order->id) }}" target="_blank" class="btn btn-sm btn-light" type="button" title="Print Invoice"><i class="las la-print"></i></a>
        </div>
    </div>

    <div class="card-body">
        <!-- Order Header -->
        <div class="row">
            <div class="col-md-6">
                <h5 class="fw-600">{{ translate('Customer Info') }}</h5>
                <address>
                    @if ($order->user)
                        <strong class="text-main">{{ $order->user->name }}</strong><br>
                        {{ $order->user->email }}<br>
                        {{ $order->user->phone }}
                    @else
                        <strong class="text-main">{{ $order->shipping_name ?? 'Guest' }}</strong><br>
                        {{ $order->shipping_email }}<br>
                        {{ $order->shipping_phone }}
                    @endif
                </address>
                <p>{{ $order->shipping_address }}, {{ $order->shipping_city }}</p>
                 @if($order->user)
                    <span class="badge badge-inline badge-success">{{ $order->user->user_type }}</span>
                @else
                    <span class="badge badge-inline badge-secondary">{{ translate('Guest') }}</span>
                @endif
            </div>
            <div class="col-md-6 text-md-right">
                <table class="table-borderless">
                    <tbody>
                        <tr>
                            <td class="text-main text-bold">{{ translate('Order #')}}</td>
                            <td class="text-right text-info text-bold">{{ $order->order_code }}</td>
                        </tr>
                        <tr>
                            <td class="text-main text-bold">{{ translate('Order Date')}}</td>
                            <td class="text-right">{{ date('d-m-Y h:i A', strtotime($order->created_at)) }}</td>
                        </tr>
                        <tr>
                            <td class="text-main text-bold">{{ translate('Total Amount')}}</td>
                            <td class="text-right">{{ single_price($grand_total) }}</td>
                        </tr>
                        <tr>
                            <td class="text-main text-bold">{{ translate('Payment Method')}}</td>
                            <td class="text-right">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($order->user)
            <div class="mt-3 d-flex" style="gap: 10px;">
                <button class="btn btn-sm btn-soft-info" onclick="customer_history()">{{ translate("Customer’s History") }}</button>
                <a href="{{ route('customers.suspicious', $order->user->id) }}" class="btn btn-sm btn-soft-warning">{{ translate("Mark as suspicious") }}</a>
                <a href="{{ route('customers.ban', $order->user->id) }}" class="btn btn-sm btn-soft-danger">{{ translate("Ban this Customer") }}</a>
            </div>
        @endif
        
        <hr>

        <!-- Order Items -->
        <h5 class="fw-600 mb-3">{{ translate('Order Items') }}</h5>
        <table class="table table-bordered aiz-table">
            <thead>
                <tr class="bg-light">
                    <th>#</th>
                    <th width="40%">{{ translate('Product')}}</th>
                    <th class="text-center">{{ translate('QTY')}}</th>
                    <th class="text-right">{{ translate('Unit Price')}}</th>
                    <th class="text-right">{{ translate('Total')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order_items as $key => $item)
                    @php $product = \App\Models\Product::find($item->product_id); @endphp
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>
                            @if ($product)
                                <a href="{{ route('product', $product->slug) }}" target="_blank" class="text-reset">
                                    {{ $product->getTranslation('name') }}
                                    <small class="d-block text-muted">{{ $item->variant_name }}</small>
                                </a>
                            @else
                                <strong class="text-danger">{{ translate('Product Not Found') }}</strong>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">{{ single_price($item->unit_price) }}</td>
                        <td class="text-right fw-600">{{ single_price($item->grand_total) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Order Summary -->
        <div class="row mt-4">
            <div class="col-md-6 ml-auto">
                <table class="table">
                    <tbody>
                        <tr>
                            <th>{{ translate('Subtotal')}}</th>
                            <td class="text-right">{{ single_price($total_subtotal) }}</td>
                        </tr>
                        <tr>
                            <th class="text-success">{{ translate('Prepayment (Paid)')}}</th>
                            <td class="text-right text-success">{{ single_price($total_prepayment) }}</td>
                        </tr>
                        <tr>
                            <th class="text-danger">{{ translate('Remaining')}}</th>
                            <td class="text-right text-danger">{{ single_price($remaining_amount) }}</td>
                        </tr>
                        <tr class="table-active">
                            <th class="text-bold">{{ translate('Total')}}</th>
                            <td class="text-right text-bold"><strong>{{ single_price($grand_total) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <hr>

        <!-- Update Status Section -->
        <div class="row">
            <div class="col-md-8">
                <h5 class="fw-600 mb-3">{{ translate('Update Order Status') }}</h5>
                <form action="{{ route('preorders.update_status', $order->order_code) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="status">{{ translate('Status') }}</label>
                                <select name="status" id="status" class="form-control aiz-selectpicker" required>
                                    <option value="payment_processing" @if($order->status == 'payment_processing') selected @endif disabled>{{ translate('Pending') }}</option>
                                    <option value="confirmed" @if($order->status == 'confirmed') selected @endif>{{ translate('Accept Order (Confirmed)') }}</option>
                                    <option value="shipped" @if($order->status == 'shipped') selected @endif>{{ translate('Mark as Shipped') }}</option>
                                    <option value="completed" @if($order->status == 'completed') selected @endif>{{ translate('Mark as Delivered (Completed)') }}</option>
                                    <option value="cancelled" @if($order->status == 'cancelled') selected @endif>{{ translate('Cancel Order') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-7">
                             <div class="form-group">
                                <label for="email_message">{{ translate('Message for Customer') }} (Optional)</label>
                                <textarea name="email_message" id="email_message" class="form-control" rows="1" placeholder="{{ translate('Write a custom message...') }}"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="aiz-checkbox">
                            <input type="checkbox" name="notify_customer" value="1" checked>
                            <span class="aiz-square-check"></span>
                            <span>{{ translate('Notify customer by email') }}</span>
                        </label>
                    </div>
                    <div class="text-left">
                        <button type="submit" class="btn btn-primary">{{ translate('Update Status') }}</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection

@section('modal')
    @if($order->user)
        @include('preorder.backend.orders.customer_history_modal')
    @endif
@endsection

@section('script')
<script>
    function customer_history() {
        $('#customer-history-modal').modal('show');
    }

    $(document).ready(function(){
        $('form').on('submit', function(){
            $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="las la-spinner la-spin"></i> {{ translate("Updating...") }}');
        });
    });
</script>
@endsection