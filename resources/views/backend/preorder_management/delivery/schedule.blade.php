@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Delivery Schedule & Balance') }}</h1>
        </div>
        <div class="col-md-6 text-md-right">
            <a href="#" class="btn btn-primary" onclick="bulkUpdateDelivery()">
                <span>{{ translate('Bulk Update Delivery') }}</span>
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row gutters-10 mb-3">
    <div class="col-lg-3">
        <div class="card card-stats">
            <div class="card-body">
                <div class="row">
                    <div class="col-5">
                        <div class="icon-big text-center">
                            <i class="las la-truck text-primary"></i>
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="numbers">
                            <p class="card-category">{{ translate('Pending Delivery') }}</p>
                            <h4 class="card-title">{{ $stats['pending_delivery'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card card-stats">
            <div class="card-body">
                <div class="row">
                    <div class="col-5">
                        <div class="icon-big text-center">
                            <i class="las la-calendar text-success"></i>
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="numbers">
                            <p class="card-category">{{ translate('Scheduled Today') }}</p>
                            <h4 class="card-title">{{ $stats['scheduled_today'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card card-stats">
            <div class="card-body">
                <div class="row">
                    <div class="col-5">
                        <div class="icon-big text-center">
                            <i class="las la-exclamation-triangle text-warning"></i>
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="numbers">
                            <p class="card-category">{{ translate('Overdue') }}</p>
                            <h4 class="card-title">{{ $stats['overdue'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card card-stats">
            <div class="card-body">
                <div class="row">
                    <div class="col-5">
                        <div class="icon-big text-center">
                            <i class="las la-check-circle text-info"></i>
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="numbers">
                            <p class="card-category">{{ translate('Delivered') }}</p>
                            <h4 class="card-title">{{ $stats['delivered_orders'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-3">
    <div class="card-body">
        <form class="form-inline" method="GET">
            <div class="form-group mr-3">
                <label class="mr-2">{{ translate('Delivery Status') }}:</label>
                <select class="form-control" name="delivery_status">
                    <option value="">{{ translate('All') }}</option>
                    <option value="pending" {{ request('delivery_status') == 'pending' ? 'selected' : '' }}>{{ translate('Pending') }}</option>
                    <option value="scheduled" {{ request('delivery_status') == 'scheduled' ? 'selected' : '' }}>{{ translate('Scheduled') }}</option>
                    <option value="delivered" {{ request('delivery_status') == 'delivered' ? 'selected' : '' }}>{{ translate('Delivered') }}</option>
                    <option value="cancelled" {{ request('delivery_status') == 'cancelled' ? 'selected' : '' }}>{{ translate('Cancelled') }}</option>
                </select>
            </div>
            <div class="form-group mr-3">
                <label class="mr-2">{{ translate('Date Range') }}:</label>
                <input type="date" class="form-control mr-2" name="date_from" value="{{ request('date_from') }}">
                <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
            </div>
            <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
            <a href="{{ route('admin.preorder.delivery.schedule') }}" class="btn btn-secondary ml-2">{{ translate('Reset') }}</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-block d-md-flex">
        <h5 class="mb-0 h6">{{ translate('Pre-Order Delivery Schedule') }}</h5>
        <div class="col-md-3 ml-auto">
            <form class="" id="sort_orders" action="" method="GET">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" id="search" name="search" @isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type order code & Enter') }}">
                </div>
            </form>
        </div>
    </div>
    
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>
                        <div class="form-group">
                            <div class="aiz-checkbox-inline">
                                <label class="aiz-checkbox">
                                    <input type="checkbox" class="check-all">
                                    <span class="aiz-square-check"></span>
                                </label>
                            </div>
                        </div>
                    </th>
                    <th>{{ translate('Order Code') }}</th>
                    <th>{{ translate('Customer') }}</th>
                    <th>{{ translate('Products') }}</th>
                    <th>{{ translate('Delivery Date') }}</th>
                    <th>{{ translate('Remaining Balance') }}</th>
                    <th>{{ translate('Delivery Status') }}</th>
                    <th>{{ translate('Payment Status') }}</th>
                    <th class="text-right">{{ translate('Options') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $key => $order)
                    @php
                        $advance_amount = $order->advance_payment_amount ?? ($order->grand_total * 0.3);
                        $remaining_balance = $order->grand_total - $advance_amount;
                        $delivery_date = $order->delivery_date ?? $order->created_at->addDays(7);
                        $is_overdue = $delivery_date < now() && $order->delivery_status != 'delivered';
                    @endphp
                    <tr class="{{ $is_overdue ? 'table-warning' : '' }}">
                        <td>
                        <div class="form-group">
                            <div class="aiz-checkbox-inline">
                                <label class="aiz-checkbox">
                                    <input type="checkbox" class="check-one" name="id[]" value="{{ $order->id }}">
                                    <span class="aiz-square-check"></span>
                                </label>
                            </div>
                        </div>
                    </td>
                    <td>{{ $order->code ?: 'N/A' }}</td>
                        <td>
                            @if($order->user)
                                {{ $order->user->name }}
                                <br>
                                <small class="text-muted">{{ $order->user->phone }}</small>
                            @else
                                {{ $order->guest_name ?? $order->shipping_address->name ?? translate('Guest') }}
                                <br>
                                <small class="text-muted">{{ $order->guest_phone ?? $order->shipping_address->phone ?? '' }}</small>
                            @endif
                        </td>
                        <td>
                            @if($order->products)
                                @foreach($order->products as $product)
                                    {{ $product->name }} (Qty: {{ $product->pivot->quantity }})
                                    <br>
                                @endforeach
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-center align-items-center h-100 p-2 bg-{{ $is_overdue ? 'danger' : 'info' }} text-white rounded">
                                {{ date('d M Y', strtotime($delivery_date)) }}
                            </div>
                        </td>
                        <td>
                            @if($remaining_balance > 0)
                                <div class="d-flex justify-content-center align-items-center p-1 bg-warning text-white rounded">
                                    {{ single_price($remaining_balance) }}
                                </div>
                            @else
                                <div class="d-flex justify-content-center align-items-center p-1 bg-success text-white rounded">
                                    {{ translate('Fully Paid') }}
                                </div>
                            @endif
                        </td>
                        <td>
                            @switch($order->delivery_status)
                                @case('pending')
                                    <div class="d-flex justify-content-center align-items-center p-1 bg-secondary text-white rounded">
                                        {{ translate('Pending') }}
                                    </div>
                                    @break
                                @case('scheduled')
                                    <div class="d-flex justify-content-center align-items-center p-1 bg-info text-white rounded">
                                        {{ translate('Scheduled') }}
                                    </div>
                                    @break
                                @case('delivered')
                                    <div class="d-flex justify-content-center align-items-center p-1 bg-success text-white rounded">
                                        {{ translate('Delivered') }}
                                    </div>
                                    @break
                                @case('cancelled')
                                    <div class="d-flex justify-content-center align-items-center p-1 bg-danger text-white rounded">
                                        {{ translate('Cancelled') }}
                                    </div>
                                    @break
                                @default
                                    <div class="d-flex justify-content-center align-items-center p-1 bg-secondary text-white rounded">
                                        {{ translate('Pending') }}
                                    </div>
                            @endswitch
                        </td>
                        <td>
                            @if($order->payment_status == 'paid')
                                <div class="d-flex justify-content-center align-items-center p-1 bg-success text-white rounded">
                                    {{ translate('Paid') }}
                                </div>
                            @else
                                <div class="d-flex justify-content-center align-items-center p-1 bg-danger text-white rounded">
                                    {{ translate('Unpaid') }}
                                </div>
                            @endif
                        </td>
                        <td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('orders.show', encrypt($order->id)) }}" title="{{ translate('View') }}">
                                <i class="las la-eye"></i>
                            </a>
                            <a class="btn btn-soft-info btn-icon btn-circle btn-sm" href="#" onclick="updateDeliverySchedule({{ $order->id }})" title="{{ translate('Update Schedule') }}">
                                <i class="las la-calendar-alt"></i>
                            </a>
                            <a class="btn btn-soft-success btn-icon btn-circle btn-sm" href="#" onclick="markAsDelivered({{ $order->id }})" title="{{ translate('Mark as Delivered') }}">
                                <i class="las la-check"></i>
                            </a>
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

<!-- Update Delivery Schedule Modal -->
<div class="modal fade" id="delivery-schedule-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h6">{{ translate('Update Delivery Schedule') }}</h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <form id="delivery-schedule-form">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="order_id" name="order_id">
                    
                    <div class="form-group">
                        <label>{{ translate('Delivery Date') }}</label>
                        <input type="date" class="form-control" name="delivery_date" required>
                    </div>
                    
                    <div class="form-group">
                        <label>{{ translate('Delivery Status') }}</label>
                        <select class="form-control" name="delivery_status" required>
                            <option value="pending">{{ translate('Pending') }}</option>
                            <option value="scheduled">{{ translate('Scheduled') }}</option>
                            <option value="delivered">{{ translate('Delivered') }}</option>
                            <option value="cancelled">{{ translate('Cancelled') }}</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>{{ translate('Delivery Notes') }}</label>
                        <textarea class="form-control" name="delivery_notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ translate('Update Schedule') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Update Modal -->
<div class="modal fade" id="bulk-delivery-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h6">{{ translate('Bulk Update Delivery') }}</h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <form id="bulk-delivery-form">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ translate('Delivery Date') }}</label>
                        <input type="date" class="form-control" name="bulk_delivery_date" required>
                    </div>
                    
                    <div class="form-group">
                        <label>{{ translate('Delivery Status') }}</label>
                        <select class="form-control" name="bulk_delivery_status" required>
                            <option value="scheduled">{{ translate('Scheduled') }}</option>
                            <option value="delivered">{{ translate('Delivered') }}</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ translate('Update Selected') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script type="text/javascript">
        function updateDeliverySchedule(orderId) {
            $('#order_id').val(orderId);
            $('#delivery-schedule-modal').modal('show');
        }

        function markAsDelivered(orderId) {
            if(confirm('{{ translate('Are you sure you want to mark this order as delivered?') }}')) {
                $.ajax({
                    url: '{{ route('admin.preorder.delivery.mark_delivered') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        order_id: orderId
                    },
                    success: function(response) {
                        if(response.success) {
                            AIZ.plugins.notify('success', response.message);
                            location.reload();
                        } else {
                            AIZ.plugins.notify('danger', response.message);
                        }
                    },
                    error: function() {
                        AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                    }
                });
            }
        }

        function bulkUpdateDelivery() {
            var selectedOrders = [];
            $('.check-one:checked').each(function() {
                selectedOrders.push($(this).val());
            });
            
            if(selectedOrders.length === 0) {
                AIZ.plugins.notify('warning', '{{ translate('Please select at least one order') }}');
                return;
            }
            
            $('#bulk-delivery-modal').modal('show');
        }

        $('#delivery-schedule-form').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: '{{ route('admin.preorder.delivery.update_schedule') }}',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if(response.success) {
                        AIZ.plugins.notify('success', response.message);
                        $('#delivery-schedule-modal').modal('hide');
                        location.reload();
                    } else {
                        AIZ.plugins.notify('danger', response.message);
                    }
                },
                error: function() {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        });

        $('#bulk-delivery-form').on('submit', function(e) {
            e.preventDefault();
            
            var selectedOrders = [];
            $('.check-one:checked').each(function() {
                selectedOrders.push($(this).val());
            });
            
            var formData = $(this).serialize() + '&order_ids=' + selectedOrders.join(',');
            
            $.ajax({
                url: '{{ route('admin.preorder.delivery.bulk_update') }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if(response.success) {
                        AIZ.plugins.notify('success', response.message);
                        $('#bulk-delivery-modal').modal('hide');
                        location.reload();
                    } else {
                        AIZ.plugins.notify('danger', response.message);
                    }
                },
                error: function() {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        });

        // Check all functionality
        $('.check-all').on('change', function() {
            $('.check-one').prop('checked', $(this).is(':checked'));
        });

        $('#sort_orders').on('submit', function(e){
            e.preventDefault();
            $('#sort_orders').submit();
        });
    </script>
@endsection

