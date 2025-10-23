@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <h1 class="h3">{{ translate('Pre-Orders & Payments') }}</h1>
</div>

<div class="card">
    <div class="card-header row gutters-5">
        <div class="col">
            <h5 class="mb-0 h6">{{ translate('All Pre-Orders') }}</h5>
        </div>
        
        {{-- ফিল্টার এবং সার্চের জন্য একটি মাত্র ফর্ম --}}
        <div class="col-md-auto ml-auto">
            <form action="" method="GET">
                <div class="input-group">
                    <select class="form-control form-control-sm aiz-selectpicker" name="status" onchange="this.form.submit()">
                        <option value="">{{ translate('Filter by Status') }}</option>
                        <option value="payment_processing" @if($status == 'payment_processing') selected @endif>{{ translate('Pending Confirmation') }}</option>
                        <option value="confirmed" @if($status == 'confirmed') selected @endif>{{ translate('Confirmed') }}</option>
                        <option value="shipped" @if($status == 'shipped') selected @endif>{{ translate('Shipped') }}</option>
                        <option value="completed" @if($status == 'completed') selected @endif>{{ translate('Completed') }}</option>
                        <option value="cancelled" @if($status == 'cancelled') selected @endif>{{ translate('Cancelled') }}</option>
                        <option value="incomplete" @if($status == 'incomplete') selected @endif>{{ translate('Incomplete Info') }}</option>
                    </select>
                    <input type="text" class="form-control form-control-sm" id="search" name="search" @isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type order code or name') }}">
                    <button type="submit" class="btn btn-primary btn-sm">{{ translate('Filter') }}</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ translate('Order Code') }}</th>
                    <th data-breakpoints="md">{{ translate('Customer') }}</th>
                    <th data-breakpoints="md">{{ translate('Amount') }}</th>
                    <th data-breakpoints="md">{{ translate('Payment Status') }}</th>
                    <th>{{ translate('Order Status') }}</th>
                    <th class="text-right">{{ translate('Options') }}</th>
                </tr>
            </thead>
        <tbody>
    @foreach($orders as $key => $order)
        <tr>
            <td>{{ ($key+1) + ($orders->currentPage() - 1) * $orders->perPage() }}</td>
            <td>
                <a href="{{ route('preorders.show', $order->id) }}">{{ $order->order_code }}</a>
                @if($order->is_viewed == 0) <span class="badge badge-inline badge-danger">{{ translate('New') }}</span> @endif
            </td>
            <td>{{ $order->shipping_name ?? translate('Guest') }}</td>
            <td>{{ single_price($order->total_grand) }}</td>
            <td>
                @php
                    // যদি অর্ডার স্ট্যাটাস 'completed' হয়, তাহলে পেমেন্টকে 'Paid' হিসেবে ধরা হবে
                    if($order->status == 'completed'){
                        $payment_status_text = translate('Paid');
                        $payment_status_class = 'badge-success';
                    } else {
                        // অন্যথায়, পার্সেন্টেজ অনুযায়ী গণনা করা হবে
                        $payment_percentage = $order->total_grand > 0 ? ($order->total_prepayment / $order->total_grand) * 100 : 0;
                        if($payment_percentage >= 100){
                            $payment_status_text = translate('Paid');
                            $payment_status_class = 'badge-success';
                        } else {
                            $payment_status_text = translate('Partially Paid');
                            $payment_status_class = 'badge-info';
                        }
                    }
                @endphp
                <span class="badge badge-inline {{ $payment_status_class }}">{{ $payment_status_text }}</span>
            </td>
            <td>
                @php
                    // অর্ডার স্ট্যাটাস অনুযায়ী ভিন্ন ভিন্ন রঙের জন্য ক্লাস নির্ধারণ
                    $status_class = 'badge-secondary'; // ডিফল্ট রঙ
                    if($order->status == 'payment_processing') $status_class = 'badge-info';
                    if($order->status == 'pending_payment') $status_class = 'badge-warning';
                    if($order->status == 'confirmed') $status_class = 'badge-primary';
                    if($order->status == 'shipped') $status_class = 'badge-warning';
                    if($order->status == 'completed') $status_class = 'badge-success';
                    if($order->status == 'cancelled') $status_class = 'badge-danger';
                @endphp
                <span class="badge badge-inline {{ $status_class }}">{{ Str::title(str_replace('_', ' ', $order->status)) }}</span>
            </td>
            <td class="text-right">
            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('preorders.show', $order->id) }}" title="{{ translate('View') }}">
                <i class="las la-eye"></i>
            </a>
            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{ route('preorder.destroy', $order->order_code) }}" title="{{ translate('Delete') }}">
                <i class="las la-trash"></i>
            </a>
            </td>
        </tr>
    @endforeach
</tbody>
        </table>
        <div class="aiz-pagination">
            {{ $orders->appends(request()->input())->links() }}
        </div>
    </div>
</div>
@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection