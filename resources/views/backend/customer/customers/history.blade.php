@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3">{{ translate('Customer History') }}</h1>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">{{ translate('Customer Information') }}</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>{{ translate('Name') }}:</strong> {{ $customer->name }}</p>
                <p><strong>{{ translate('Email') }}:</strong> {{ $customer->email }}</p>
                <p><strong>{{ translate('Phone') }}:</strong> {{ $customer->phone }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>{{ translate('Registration Date') }}:</strong> {{ $customer->created_at->format('d M Y') }}</p>
                <p><strong>{{ translate('Status') }}:</strong> 
                    @if($customer->banned)
                        <span class="badge badge-danger">{{ translate('Banned') }}</span>
                    @else
                        <span class="badge badge-success">{{ translate('Active') }}</span>
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">{{ translate('Order History') }}</h5>
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ translate('Order Code') }}</th>
                    <th>{{ translate('Amount') }}</th>
                    <th>{{ translate('Delivery Status') }}</th>
                    <th>{{ translate('Payment Status') }}</th>
                    <th>{{ translate('Date') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $key => $order)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $order->code }}</td>
                        <td>{{ format_price($order->grand_total) }}</td>
                        <td>{{ translate(ucfirst(str_replace('_', ' ', $order->delivery_status))) }}</td>
                        <td>{{ translate(ucfirst($order->payment_status)) }}</td>
                        <td>{{ $order->created_at->format('d-m-Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination mt-4">
            {{ $orders->links() }}
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">{{ translate('Pre-order History') }}</h5>
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ translate('Pre-order Code') }}</th>
                    <th>{{ translate('Amount') }}</th>
                    <th>{{ translate('Delivery Status') }}</th>
                    <th>{{ translate('Payment Status') }}</th>
                    <th>{{ translate('Date') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($preorders as $key => $preorder)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $preorder->code }}</td>
                        <td>{{ format_price($preorder->grand_total) }}</td>
                        <td>{{ translate(ucfirst(str_replace('_', ' ', $preorder->delivery_status))) }}</td>
                        <td>{{ translate(ucfirst($preorder->payment_status)) }}</td>
                        <td>{{ $preorder->created_at->format('d-m-Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination mt-4">
            {{ $preorders->links() }}
        </div>
    </div>
</div>
@endsection