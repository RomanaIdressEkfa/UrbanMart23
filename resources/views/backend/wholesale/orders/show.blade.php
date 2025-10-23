@extends('backend.layouts.app')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 h6">{{ translate('Wholesale Order Details') }} - {{ $order->code }}</h5>
            <div class="d-flex align-items-center">
                @php
                    $ds = $order->delivery_status;
                    $ps = $order->payment_status;
                    $dsClass = $ds == 'delivered' ? 'badge-success' : ($ds == 'cancelled' ? 'badge-danger' : ($ds == 'confirmed' ? 'badge-info' : 'badge-secondary'));
                    $psClass = $ps == 'paid' ? 'badge-success' : 'badge-warning';
                @endphp
                <span class="badge badge-inline {{ $dsClass }} mr-2">{{ translate('Delivery') }}: {{ ucwords(str_replace('_',' ', $ds)) }}</span>
                <span class="badge badge-inline {{ $psClass }}">{{ translate('Payment') }}: {{ ucfirst($ps) }}</span>
            </div>
        </div>
        <div class="card-body">
            @php
                $delivery_status = $order->delivery_status;
                $payment_status = $order->payment_status;
            @endphp
            <div class="row">
                <div class="col-lg-6">
                    <h6>{{ translate('Order Information') }}</h6>
                    <p><strong>{{ translate('Order ID') }}:</strong> {{ $order->id }}</p>
                    <p><strong>{{ translate('Order Code') }}:</strong> {{ $order->code }}</p>
                    <p><strong>{{ translate('Order Date') }}:</strong> {{ $order->created_at->format('d-m-Y h:i A') }}</p>
                    <p><strong>{{ translate('Wholesaler Name') }}:</strong>
                        @if ($order->user)
                            {{ $order->user->name }} ({{ $order->user->email }})
                            <br>
                            <strong>{{ translate('Phone') }}:</strong> {{ $order->user->phone ?? translate('N/A') }}
                        @else
                            {{ translate('N/A') }}
                        @endif
                    </p>
                    <p><strong>{{ translate('Total Amount') }}:</strong> {{ format_price($order->grand_total) }}</p>
                    <p><strong>{{ translate('Payment Method') }}:</strong> {{ $order->payment_type ?? translate('N/A') }}</p>
                    @if (!empty($order->manual_payment_data))
                        @php
                            $manual = is_array($order->manual_payment_data) ? $order->manual_payment_data : json_decode($order->manual_payment_data, true);
                        @endphp
                        @if(!empty($manual))
                            <div class="mt-2">
                                <strong>{{ translate('Offline Payment Info') }}:</strong>
                                <ul class="mb-0">
                                    @foreach($manual as $k => $v)
                                        <li><span class="text-capitalize">{{ str_replace('_',' ', $k) }}</span>: {{ $v }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    @endif
                    <div class="row gutters-5">
                        <div class="col-md-6">
                            <label for="update_payment_status">{{ translate('Payment Status') }}</label>
                            @if (auth()->user()->can('update_order_payment_status') && $payment_status == 'unpaid')
                                <select class="form-control aiz-selectpicker" data-minimum-results-for-search="Infinity" id="update_payment_status" onchange="confirm_payment_status()">
                                    <option value="unpaid" @if ($payment_status == 'unpaid') selected @endif>{{ translate('Unpaid') }}</option>
                                    <option value="paid" @if ($payment_status == 'paid') selected @endif>{{ translate('Paid') }}</option>
                                </select>
                                <small class="text-muted d-block mt-1">{{ translate('Marking as paid will lock this field.') }}</small>
                            @else
                                <input type="text" class="form-control" value="{{ ucfirst($payment_status) }}" disabled>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label for="update_delivery_status">{{ translate('Delivery Status') }}</label>
                            @if (auth()->user()->can('update_order_delivery_status') && $delivery_status != 'delivered' && $delivery_status != 'cancelled')
                                <select class="form-control aiz-selectpicker" data-minimum-results-for-search="Infinity" id="update_delivery_status">
                                    <option value="pending" @if ($delivery_status == 'pending') selected @endif>{{ translate('Pending') }}</option>
                                    <option value="confirmed" @if ($delivery_status == 'confirmed') selected @endif>{{ translate('Confirmed') }}</option>
                                    <option value="cancelled" @if ($delivery_status == 'cancelled') selected @endif>{{ translate('Cancel') }}</option>
                                </select>
                                <small class="text-muted d-block mt-1">{{ translate('On Confirmed, wholesaler gets email with delivery timeline.') }}</small>
                            @else
                                <input type="text" class="form-control" value="{{ $delivery_status }}" disabled>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <h6>{{ translate('Shipping Address') }}</h6>
                    @if ($order->shipping_address)
                        <p>{{ $order->shipping_address->address ?? translate('N/A') }}</p>
                        <p>{{ $order->shipping_address->city ?? translate('N/A') }}, {{ $order->shipping_address->state ?? translate('N/A') }}</p>
                        <p>{{ $order->shipping_address->country ?? translate('N/A') }} - {{ $order->shipping_address->postal_code ?? translate('N/A') }}</p>
                        <p><strong>{{ translate('Phone') }}:</strong> {{ $order->shipping_address->phone ?? translate('N/A') }}</p>
                    @else
                        <p>{{ translate('No shipping address provided.') }}</p>
                    @endif
                </div>
            </div>

            <hr>

            <h6>{{ translate('Products') }}</h6>
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ translate('Product') }}</th>
                        <th>{{ translate('Quantity') }}</th>
                        <th>{{ translate('Unit Price') }}</th>
                        <th>{{ translate('Total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->orderDetails as $key => $orderDetail)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                @if ($orderDetail->product)
                                    {{ $orderDetail->product->getTranslation('name') }}
                                @else
                                    {{ translate('Product Not Found') }}
                                @endif
                            </td>
                            <td>{{ $orderDetail->quantity }}</td>
                            <td>{{ format_price($orderDetail->price) }}</td>
                            <td>{{ format_price($orderDetail->total) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    <a href="{{ route('invoice.download', $order->id) }}" class="btn btn-light">
                        <i class="las la-download mr-1"></i>{{ translate('Download Invoice') }}
                    </a>
                </div>
                <div>
                    <a href="{{ route('wholesale_orders.index') }}" class="btn btn-info">{{ translate('Back to Wholesale Orders') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modal')
    <!-- confirm payment Status Modal -->
    <div id="confirm-payment-status" class="modal fade">
        <div class="modal-dialog modal-md modal-dialog-centered" style="max-width: 540px;">
            <div class="modal-content p-2rem">
                <div class="modal-body text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="72" height="64" viewBox="0 0 72 64">
                        <g id="Octicons" transform="translate(-0.14 -1.02)">
                          <g id="alert" transform="translate(0.14 1.02)">
                            <path id="Shape" d="M40.159,3.309a4.623,4.623,0,0,0-7.981,0L.759,58.153a4.54,4.54,0,0,0,0,4.578A4.718,4.718,0,0,0,4.75,65.02H67.587a4.476,4.476,0,0,0,3.945-2.289,4.773,4.773,0,0,0,.046-4.578Zm.6,52.555H31.582V46.708h9.173Zm0-13.734H31.582V23.818h9.173Z" transform="translate(-0.14 -1.02)" fill="#ffc700" fill-rule="evenodd"/>
                          </g>
                        </g>
                    </svg>
                    <p class="mt-3 mb-3 fs-16 fw-700">{{translate('Are you sure you want to change the payment status?')}}</p>
                    <button type="button" class="btn btn-light rounded-2 mt-2 fs-13 fw-700 w-150px" data-dismiss="modal">{{ translate('Cancel') }}</button>
                    <button type="button" onclick="update_payment_status()" class="btn btn-success rounded-2 mt-2 fs-13 fw-700 w-150px">{{translate('Confirm')}}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $('#update_delivery_status').on('change', function() {
            var order_id = {{ $order->id }};
            var status = $('#update_delivery_status').val();
            $.post('{{ route('orders.update_delivery_status') }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                status: status
            }, function(data) {
                AIZ.plugins.notify('success', '{{ translate('Delivery status has been updated') }}');
                location.reload();
            });
        });

        function confirm_payment_status(){
            $('#confirm-payment-status').modal('show');
        }

        function update_payment_status(){
            $('#confirm-payment-status').modal('hide');
            var order_id = {{ $order->id }};
            $.post('{{ route('orders.update_payment_status') }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                status: 'paid'
            }, function(data) {
                $('#update_payment_status').prop('disabled', true);
                AIZ.plugins.bootstrapSelect('refresh');
                AIZ.plugins.notify('success', '{{ translate('Payment status has been updated') }}');
                location.reload();
            });
        }
    </script>
@endsection

