@extends('backend.layouts.app')

@section('content')

<div class="card">
    <form class="" action="" id="sort_preorders" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6">{{ translate('Pre-Order Management') }}</h5>
            </div>

            <div class="dropdown mb-2 mb-md-0">
                <button class="btn border dropdown-toggle" type="button" data-toggle="dropdown">
                    {{ translate('Bulk Action') }}
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="javascript:void(0)" onclick="bulk_mark_arrived()">{{ translate('Mark Products as Arrived') }}</a>
                    <a class="dropdown-item" href="javascript:void(0)" onclick="bulk_notify_customers()">{{ translate('Notify Customers') }}</a>
                </div>
            </div>

            <div class="col-lg-2 ml-auto">
                <select class="form-control aiz-selectpicker" name="status" id="status">
                    <option value="">{{ translate('Filter by Status') }}</option>
                    <option value="pending" @if (request('status') == 'pending') selected @endif>{{ translate('Pending') }}</option>
                    <option value="confirmed" @if (request('status') == 'confirmed') selected @endif>{{ translate('Confirmed') }}</option>
                    <option value="product_arrived" @if (request('status') == 'product_arrived') selected @endif>{{ translate('Product Arrived') }}</option>
                    <option value="completed" @if (request('status') == 'completed') selected @endif>{{ translate('Completed') }}</option>
                    <option value="cancelled" @if (request('status') == 'cancelled') selected @endif>{{ translate('Cancelled') }}</option>
                </select>
            </div>

            <div class="col-lg-2">
                <div class="form-group mb-0">
                    <input type="text" class="form-control" id="search" name="search" 
                        value="{{ request('search') }}" placeholder="{{ translate('Search by order code or customer') }}">
                </div>
            </div>

            <div class="col-auto">
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                </div>
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
                        <th>{{ translate('Customer Info') }}</th>
                        <th>{{ translate('Product') }}</th>
                        <th>{{ translate('Quantity') }}</th>
                        <th>{{ translate('City') }}</th>
                        <th>{{ translate('Address/Pickup') }}</th>
                        <th>{{ translate('Total Amount') }}</th>
                        <th>{{ translate('Paid Amount') }}</th>
                        <th>{{ translate('Remaining Amount') }}</th>
                        <th>{{ translate('Payment Method') }}</th>
                        <th>{{ translate('Status') }}</th>
                        <th>{{ translate('Created At') }}</th>
                        <th class="text-right" width="15%">{{ translate('Options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Sample data structure - replace with actual data from controller --}}
                    @forelse($preorders ?? [] as $key => $preorder)
                    <tr>
                        <td>
                            <div class="form-group">
                                <div class="aiz-checkbox-inline">
                                    <label class="aiz-checkbox">
                                        <input type="checkbox" class="check-one" name="id[]" value="{{ $preorder->id }}">
                                        <span class="aiz-square-check"></span>
                                    </label>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="fw-600 text-primary">{{ $preorder->order_code ?? 'PRE-' . str_pad($preorder->id, 6, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td>
                            @if($preorder->shipping_name)
                                <span class="text-muted">{{ $preorder->shipping_name }}</span>
                                <br>
                                <span class="opacity-60 text-muted">{{ $preorder->shipping_email }}</span>
                                <br>
                                <span class="opacity-60 text-muted">{{ $preorder->shipping_phone }}</span>
                            @elseif($preorder->user)
                                <span class="text-muted">{{ $preorder->user->name }}</span>
                                <br>
                                <span class="opacity-60 text-muted">{{ $preorder->user->email ?? '' }}</span>
                                <br>
                                <span class="opacity-60 text-muted">{{ $preorder->user->phone ?? '' }}</span>
                            @else
                                {{ translate('Customer not found') }}
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rounded-2 overflow-hidden" style="min-height: 48px; min-width: 48px; max-height: 48px; max-width: 48px;">
                                    <img src="{{ uploaded_asset($preorder->product->thumbnail ?? '') }}" 
                                         alt="{{ $preorder->product->name ?? 'Product' }}" 
                                         class="h-100 img-fit lazyload" 
                                         onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                </div>
                                <div class="ml-2">
                                    <span class="text-muted text-truncate-1">{{ Str::limit($preorder->product->name ?? 'Product Name', 30, '...') }}</span>
                                </div>
                            </div>
                        </td>
                        <td>{{ $preorder->quantity ?? 1 }}</td>
                        <td>{{ $preorder->shipping_city ?? ($preorder->user && $preorder->user->addresses && $preorder->user->addresses->first() ? $preorder->user->addresses->first()->city->name ?? '' : '') }}</td>
                        <td>
                            @if($preorder->delivery_type == 'pickup' && $preorder->shipping_pickup_point)
                                {{ $preorder->shipping_pickup_point }}
                            @else
                                {{ $preorder->shipping_address ?? ($preorder->user && $preorder->user->addresses && $preorder->user->addresses->first() ? $preorder->user->addresses->first()->address ?? '' : '') }}
                            @endif
                        </td>
                        <td>{{ single_price($preorder->total_amount ?? 0) }}</td>
                        <td>{{ single_price($preorder->paid_amount ?? 0) }}</td>
                        <td>{{ single_price(($preorder->total_amount ?? 0) - ($preorder->paid_amount ?? 0)) }}</td>
                        <td>{{ $preorder->payment_method ? ucfirst($preorder->payment_method) : '-' }}</td>
                        <td>
                            @php
                                $status = $preorder->status ?? 'pending';
                                $statusClass = match($status) {
                                    'pending' => 'badge-warning',
                                    'confirmed' => 'badge-info',
                                    'product_arrived' => 'badge-success',
                                    'completed' => 'badge-success',
                                    'cancelled' => 'badge-danger',
                                    default => 'badge-secondary'
                                };
                            @endphp
                            <span class="badge badge-inline {{ $statusClass }}">{{ translate(ucfirst($status)) }}</span>
                        </td>
                        <td>{{ $preorder->created_at ? $preorder->created_at->format('d M Y') : '-' }}</td>
                        <td class="text-right">
                            <div class="dropdown">
                                <button class="btn btn-soft-primary btn-icon btn-circle btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                                    <i class="las la-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="{{ route('preorders.show', $preorder->id) }}">
                                        <i class="las la-eye"></i> {{ translate('View Details') }}
                                    </a>
                                    @if($status == 'confirmed')
                                        <a class="dropdown-item" href="javascript:void(0)" onclick="mark_product_arrived({{ $preorder->id }})">
                                            <i class="las la-check-circle"></i> {{ translate('Mark as Arrived') }}
                                        </a>
                                    @endif
                                    @if($status == 'product_arrived')
                                        <a class="dropdown-item" href="javascript:void(0)" onclick="notify_customer({{ $preorder->id }})">
                                            <i class="las la-bell"></i> {{ translate('Notify Customer') }}
                                        </a>
                                    @endif
                                    <a class="dropdown-item" href="javascript:void(0)" onclick="update_status({{ $preorder->id }})">
                                        <i class="las la-edit"></i> {{ translate('Update Status') }}
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center">
                            <div class="py-4">
                                <i class="las la-shopping-cart la-3x text-muted"></i>
                                <h4 class="h6 text-muted mt-3">{{ translate('No pre-orders found') }}</h4>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            {{-- Pagination --}}
            @if(isset($preorders) && $preorders->hasPages())
                <div class="aiz-pagination">
                    {{ $preorders->appends(request()->input())->links() }}
                </div>
            @endif
        </div>
    </form>
</div>

{{-- Mark Product as Arrived Modal --}}
<div class="modal fade" id="mark-arrived-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title h6">{{ translate('Mark Product as Arrived') }}</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <p class="mt-1">{{ translate('Are you sure you want to mark this product as arrived? This will notify the customer.') }}</p>
                <button type="button" class="btn btn-light mt-2" data-dismiss="modal">{{ translate('Cancel') }}</button>
                <button type="button" id="confirm-mark-arrived" class="btn btn-primary mt-2">{{ translate('Confirm') }}</button>
            </div>
        </div>
    </div>
</div>

{{-- Update Status Modal --}}
<div class="modal fade" id="update-status-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title h6">{{ translate('Update Pre-order Status') }}</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="update-status-form" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ translate('Status') }}</label>
                        <select class="form-control" name="status" required>
                            <option value="pending">{{ translate('Pending') }}</option>
                            <option value="confirmed">{{ translate('Confirmed') }}</option>
                            <option value="product_arrived">{{ translate('Product Arrived') }}</option>
                            <option value="completed">{{ translate('Completed') }}</option>
                            <option value="cancelled">{{ translate('Cancelled') }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{ translate('Notes (Optional)') }}</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="{{ translate('Add any notes about this status update...') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{ translate('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ translate('Update Status') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script type="text/javascript">
    // Mohammad Hassan
    function mark_product_arrived(id) {
        $('#mark-arrived-modal').modal('show');
        $('#confirm-mark-arrived').off('click').on('click', function() {
            $.post('{{ route('preorders.mark_arrived') }}', {
                _token: '{{ csrf_token() }}',
                id: id
            }, function(data) {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || '{{ translate('Something went wrong') }}');
                }
            });
        });
    }

    // Mohammad Hassan
    function notify_customer(id) {
        $.post('{{ route('preorders.notify_customer') }}', {
            _token: '{{ csrf_token() }}',
            id: id
        }, function(data) {
            if (data.success) {
                alert('{{ translate('Customer notified successfully') }}');
            } else {
                alert(data.message || '{{ translate('Something went wrong') }}');
            }
        });
    }

    // Mohammad Hassan
    function update_status(id) {
        $('#update-status-modal').modal('show');
        $('#update-status-form').attr('action', '{{ route('preorders.update_status', '') }}/' + id);
    }

    // Mohammad Hassan
    function bulk_mark_arrived() {
        var selected = [];
        $('.check-one:checked').each(function() {
            selected.push($(this).val());
        });
        
        if (selected.length === 0) {
            alert('{{ translate('Please select at least one pre-order') }}');
            return;
        }

        if (confirm('{{ translate('Are you sure you want to mark selected products as arrived?') }}')) {
            $.post('{{ route('preorders.bulk_mark_arrived') }}', {
                _token: '{{ csrf_token() }}',
                ids: selected
            }, function(data) {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || '{{ translate('Something went wrong') }}');
                }
            });
        }
    }

    // Mohammad Hassan
    function bulk_notify_customers() {
        var selected = [];
        $('.check-one:checked').each(function() {
            selected.push($(this).val());
        });
        
        if (selected.length === 0) {
            alert('{{ translate('Please select at least one pre-order') }}');
            return;
        }

        if (confirm('{{ translate('Are you sure you want to notify selected customers?') }}')) {
            $.post('{{ route('preorders.bulk_notify_customers') }}', {
                _token: '{{ csrf_token() }}',
                ids: selected
            }, function(data) {
                if (data.success) {
                    alert('{{ translate('Customers notified successfully') }}');
                } else {
                    alert(data.message || '{{ translate('Something went wrong') }}');
                }
            });
        }
    }

    // Mohammad Hassan - Check all functionality
    $(document).ready(function() {
        $('.check-all').on('change', function() {
            $('.check-one').prop('checked', $(this).is(':checked'));
        });
    });
</script>
@endsection

