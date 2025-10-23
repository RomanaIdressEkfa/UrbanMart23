@extends('backend.layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Wholesale Orders') }}</h5>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ translate('Order Code') }}</th>
                        <th data-breakpoints="md">{{ translate('Wholesaler Name') }}</th>
                        <th data-breakpoints="md">{{ translate('Total Amount') }}</th>
                        <th data-breakpoints="md">{{ translate('Delivery Status') }}</th>
                        <th data-breakpoints="md">{{ translate('Payment Status') }}</th>
                        <th data-breakpoints="lg">{{ translate('Order Date') }}</th>
                        <th class="text-right" width="15%">{{ translate('Options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($wholesale_orders as $key => $order)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $order->code }}</td>
                            <td>
                                @if ($order->user)
                                    {{ $order->user->name }}
                                @else
                                    {{ translate('N/A') }} {{-- যদি কোনো কারণে user না থাকে --}}
                                @endif
                            </td>
                            <td>{{ format_price($order->grand_total) }}</td>
                            <td>{{ translate(ucfirst(str_replace('_', ' ', $order->delivery_status))) }}</td>
                            <td>
                                @if ($order->payment_status == 'paid')
                                    <span class="badge badge-inline badge-success">{{ translate('Paid') }}</span>
                                @else
                                    <span class="badge badge-inline badge-danger">{{ translate('Unpaid') }}</span>
                                @endif
                            </td>
                            <td>{{ $order->created_at->format('d-m-Y h:i A') }}</td>
                            <td class="text-right">
                                <a class="btn btn-soft-info btn-icon btn-circle btn-sm"
                                    href="{{ route('wholesale_orders.show', encrypt($order->id)) }}"
                                    title="{{ translate('View Details') }}">
                                    <i class="las la-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center" colspan="8">{{ translate('No wholesale orders found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="aiz-pagination mt-4">
                {{ $wholesale_orders->links() }}
            </div>
        </div>
    </div>
@endsection

