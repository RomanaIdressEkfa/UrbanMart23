@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{translate('Pre-Order Details')}}</h1>
        </div>
        <div class="col-md-6 text-md-right">
            <a href="{{ route('preorder_management.customers') }}" class="btn btn-circle btn-info">
                <span>{{translate('Back to Pre-Orders')}}</span>
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">{{translate('Order')}} #{{ $order->code }}</h5>
        <div class="text-right">
            <span class="badge badge-inline 
                @if($order->preorder_status == 'pending') badge-warning
                @elseif($order->preorder_status == 'confirmed') badge-info
                @elseif($order->preorder_status == 'delivered') badge-success
                @elseif($order->preorder_status == 'cancelled') badge-danger
                @else badge-secondary
                @endif">
                {{ ucfirst($order->preorder_status ?? 'pending') }}
            </span>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Customer Information -->
            <div class="col-lg-6">
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">{{translate('Customer Information')}}</h6>
                    </div>
                    <div class="card-body">
                        @if($order->user_id)
                            <p><strong>{{translate('Name')}}:</strong> {{ $order->user->name }}</p>
                            <p><strong>{{translate('Email')}}:</strong> {{ $order->user->email }}</p>
                            <p><strong>{{translate('Phone')}}:</strong> {{ $order->user->phone ?? 'N/A' }}</p>
                            <p><strong>{{translate('User Type')}}:</strong> 
                                <span class="badge badge-inline 
                                    @if($order->user->user_type == 'customer') badge-primary
                                    @elseif($order->user->user_type == 'seller') badge-warning
                                    @else badge-info
                                    @endif">
                                    {{ ucfirst($order->user->user_type) }}
                                </span>
                            </p>
                        @else
                            <p><strong>{{translate('Guest Order')}}</strong></p>
                            <p><strong>{{translate('Name')}}:</strong> {{ $order->guest_name ?? 'N/A' }}</p>
                            <p><strong>{{translate('Email')}}:</strong> {{ $order->guest_email ?? 'N/A' }}</p>
                            <p><strong>{{translate('Phone')}}:</strong> {{ $order->guest_phone ?? 'N/A' }}</p>
                        @endif
                        <p><strong>{{translate('Order Date')}}:</strong> {{ date('d-m-Y H:i', strtotime($order->date)) }}</p>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="col-lg-6">
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">{{translate('Payment Information')}}</h6>
                    </div>
                    <div class="card-body">
                        @php
                            $remainingAmount = $order->grand_total - $order->paid_amount;
                            $paymentPercentage = $order->grand_total > 0 ? ($order->paid_amount / $order->grand_total) * 100 : 0;
                        @endphp
                        <p><strong>{{translate('Total Amount')}}:</strong> {{ single_price($order->grand_total) }}</p>
                        <p><strong>{{translate('Paid Amount')}}:</strong> {{ single_price($order->paid_amount) }}</p>
                        <p><strong>{{translate('Remaining Amount')}}:</strong> 
                            <span class="text-{{ $remainingAmount > 0 ? 'danger' : 'success' }}">
                                {{ single_price($remainingAmount) }}
                            </span>
                        </p>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ $paymentPercentage }}%" 
                                 aria-valuenow="{{ $paymentPercentage }}" 
                                 aria-valuemin="0" aria-valuemax="100">
                                {{ number_format($paymentPercentage, 1) }}%
                            </div>
                        </div>
                        <p><strong>{{translate('Payment Method')}}:</strong> {{ ucfirst($order->payment_type) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Shipping Information -->
            <div class="col-lg-6">
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">{{translate('Shipping Information')}}</h6>
                    </div>
                    <div class="card-body">
                        @php
                            $shipping_address = null;
                            if (!empty($order->shipping_address)) {
                                $shipping_address = json_decode($order->shipping_address, true);
                            }
                        @endphp
                        
                        @if($shipping_address)
                            <div class="mb-3">
                                <h6 class="text-primary mb-2">{{translate('Contact Information')}}</h6>
                                <p class="mb-1"><strong>{{translate('Full Name')}}:</strong> {{ $shipping_address['name'] ?? 'N/A' }}</p>
                                <p class="mb-1"><strong>{{translate('Email')}}:</strong> {{ $shipping_address['email'] ?? 'N/A' }}</p>
                                <p class="mb-1"><strong>{{translate('Phone')}}:</strong> {{ $shipping_address['phone'] ?? 'N/A' }}</p>
                            </div>
                            
                            <div class="mb-3">
                                <h6 class="text-primary mb-2">{{translate('Address Details')}}</h6>
                                @if(isset($shipping_address['address']) && !empty($shipping_address['address']))
                                    <p class="mb-1"><strong>{{translate('Address')}}:</strong> {{ $shipping_address['address'] }}</p>
                                @endif
                                @if(isset($shipping_address['city']) && !empty($shipping_address['city']))
                                    <p class="mb-1"><strong>{{translate('City')}}:</strong> {{ $shipping_address['city'] }}</p>
                                @endif
                                @if(isset($shipping_address['state']) && !empty($shipping_address['state']))
                                    <p class="mb-1"><strong>{{translate('State')}}:</strong> {{ $shipping_address['state'] }}</p>
                                @endif
                                @if(isset($shipping_address['postal_code']) && !empty($shipping_address['postal_code']))
                                    <p class="mb-1"><strong>{{translate('Postal Code')}}:</strong> {{ $shipping_address['postal_code'] }}</p>
                                @endif
                                @if(isset($shipping_address['country']) && !empty($shipping_address['country']))
                                    <p class="mb-1"><strong>{{translate('Country')}}:</strong> {{ $shipping_address['country'] }}</p>
                                @endif
                            </div>
                            
                            @if(isset($shipping_address['delivery_type']) || $order->delivery_location)
                                <div class="mb-3">
                                    <h6 class="text-primary mb-2">{{translate('Delivery Preferences')}}</h6>
                                    @if(isset($shipping_address['delivery_type']))
                                        <p class="mb-1"><strong>{{translate('Delivery Type')}}:</strong> 
                                            <span class="badge badge-outline-info">{{ ucfirst($shipping_address['delivery_type']) }}</span>
                                        </p>
                                    @endif
                                    @if($order->delivery_location)
                                        <p class="mb-1"><strong>{{translate('Delivery Location')}}:</strong> {{ $order->delivery_location }}</p>
                                    @endif
                                </div>
                            @endif
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{translate('No shipping information available for this order.')}}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Delivery Status -->
            <div class="col-lg-6">
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">{{translate('Delivery Status')}}</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>{{translate('Delivery Date')}}:</strong> 
                            {{ $order->delivery_date ? date('d-m-Y', strtotime($order->delivery_date)) : 'Not Set' }}
                        </p>
                        <p><strong>{{translate('Delivery Notes')}}:</strong> {{ $order->delivery_notes ?? 'No notes' }}</p>
                        @if($order->delivery_date)
                            @php
                                $deliveryDays = \Carbon\Carbon::parse($order->delivery_date)->diffInDays(now(), false);
                            @endphp
                            <p><strong>{{translate('Delivery Status')}}:</strong>
                                @if($deliveryDays < 0)
                                    <span class="text-info">{{ abs($deliveryDays) }} days remaining</span>
                                @elseif($deliveryDays == 0)
                                    <span class="text-warning">Delivery due today</span>
                                @else
                                    <span class="text-danger">{{ $deliveryDays }} days overdue</span>
                                @endif
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Order Timeline -->
            <div class="col-lg-6">
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">{{translate('Order Timeline')}}</h6>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Order Placed</h6>
                                    <p class="timeline-text">{{ date('d-m-Y H:i', strtotime($order->date)) }}</p>
                                </div>
                            </div>
                            @if($order->confirmed_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Order Confirmed</h6>
                                    <p class="timeline-text">{{ date('d-m-Y H:i', strtotime($order->confirmed_at)) }}</p>
                                </div>
                            </div>
                            @endif
                            @if($order->product_arrived_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Product Arrived</h6>
                                    <p class="timeline-text">{{ date('d-m-Y H:i', strtotime($order->product_arrived_at)) }}</p>
                                </div>
                            </div>
                            @endif
                            @if($order->completed_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Order Delivered</h6>
                                    <p class="timeline-text">{{ date('d-m-Y H:i', strtotime($order->completed_at)) }}</p>
                                </div>
                            </div>
                            @endif
                            @if($order->cancelled_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-danger"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Order Cancelled</h6>
                                    <p class="timeline-text">{{ date('d-m-Y H:i', strtotime($order->cancelled_at)) }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Information -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">{{translate('Ordered Products')}}</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{translate('Product')}}</th>
                                <th>{{translate('Variation')}}</th>
                                <th>{{translate('Quantity')}}</th>
                                <th>{{translate('Unit Price')}}</th>
                                <th>{{translate('Total')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderDetails as $detail)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($detail->product && $detail->product->thumbnail_img)
                                            <img src="{{ uploaded_asset($detail->product->thumbnail_img) }}" 
                                                 alt="{{ $detail->product->getTranslation('name') }}" 
                                                 class="size-40px img-fit mr-2">
                                        @endif
                                        <span>{{ $detail->product ? $detail->product->getTranslation('name') : 'Product not found' }}</span>
                                    </div>
                                </td>
                                <td>{{ $detail->variation ?? 'N/A' }}</td>
                                <td>{{ $detail->quantity }}</td>
                                <td>{{ single_price($detail->price) }}</td>
                                <td>{{ single_price($detail->price * $detail->quantity) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Order Notes -->
        @if($order->preorder_notes)
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">{{translate('Order Notes')}}</h6>
            </div>
            <div class="card-body">
                <pre class="mb-0">{{ $order->preorder_notes }}</pre>
            </div>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="text-center">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#updatePaymentModal">
                {{translate('Update Payment')}}
            </button>
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#updateDeliveryModal">
                {{translate('Update Delivery')}}
            </button>
            <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#updateStatusModal">
                {{translate('Update Status')}}
            </button>
        </div>
    </div>
</div>

<!-- Update Payment Modal -->
<div class="modal fade" id="updatePaymentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{translate('Update Payment')}}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('preorder_management.update_payment') }}" method="POST">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{translate('Payment Amount')}}</label>
                        <input type="number" name="payment_amount" class="form-control" step="0.01" min="0" 
                               max="{{ $order->grand_total - $order->paid_amount }}" required>
                        <small class="text-muted">Maximum: {{ single_price($order->grand_total - $order->paid_amount) }}</small>
                    </div>
                    <div class="form-group">
                        <label>{{translate('Payment Notes')}}</label>
                        <textarea name="payment_notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{translate('Close')}}</button>
                    <button type="submit" class="btn btn-primary">{{translate('Update Payment')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Delivery Modal -->
<div class="modal fade" id="updateDeliveryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{translate('Update Delivery Information')}}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.preorder_management.update_delivery') }}" method="POST">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{translate('Delivery Date')}}</label>
                        <input type="date" name="delivery_date" class="form-control" 
                               value="{{ $order->delivery_date ? date('Y-m-d', strtotime($order->delivery_date)) : '' }}">
                    </div>
                    <div class="form-group">
                        <label>{{translate('Delivery Location')}}</label>
                        <input type="text" name="delivery_location" class="form-control" 
                               value="{{ $order->delivery_location }}" placeholder="Enter delivery address">
                    </div>
                    <div class="form-group">
                        <label>{{translate('Delivery Notes')}}</label>
                        <textarea name="delivery_notes" class="form-control" rows="3" 
                                  placeholder="Enter delivery instructions">{{ $order->delivery_notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{translate('Close')}}</button>
                    <button type="submit" class="btn btn-info">{{translate('Update Delivery')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{translate('Update Order Status')}}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.preorder_management.update_status') }}" method="POST">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{translate('Pre-order Status')}}</label>
                        <select name="preorder_status" class="form-control" required>
                            <option value="pending" {{ $order->preorder_status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ $order->preorder_status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="processing" {{ $order->preorder_status == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ $order->preorder_status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ $order->preorder_status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ $order->preorder_status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{translate('Status Notes')}}</label>
                        <textarea name="status_notes" class="form-control" rows="3" 
                                  placeholder="Enter status update notes"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{translate('Close')}}</button>
                    <button type="submit" class="btn btn-warning">{{translate('Update Status')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Enhanced Card Styling */
.card {
    border-radius: 20px;
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    position: relative;
}

.card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.card:hover::before {
    opacity: 1;
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 20px 20px 0 0;
    border-bottom: none;
    padding: 20px 25px;
    position: relative;
}

.card-header h5, .card-header h6 {
    margin: 0;
    font-weight: 700;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-body {
    padding: 25px;
    background: white;
}

/* Enhanced Badge Styling */
.badge-inline {
    padding: 10px 20px;
    border-radius: 25px;
    font-size: 0.85rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}

.badge-inline:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
}

.badge-warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
}

.badge-info {
    background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
}

.badge-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.badge-danger {
    background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
}

.badge-primary {
    background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
}

.badge-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
}

/* Enhanced Timeline */
.timeline {
    position: relative;
    padding-left: 40px;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
    padding: 20px;
    background: linear-gradient(135deg, #f8f9ff 0%, #f0f4ff 100%);
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.timeline-item:hover {
    transform: translateX(10px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.timeline-marker {
    position: absolute;
    left: -45px;
    top: 25px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    border: 4px solid white;
    z-index: 2;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -36px;
    top: 45px;
    width: 2px;
    height: calc(100% + 10px);
    background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
    z-index: 1;
}

.timeline-title {
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 8px;
    color: #495057;
}

.timeline-text {
    font-size: 14px;
    color: #6c757d;
    margin-bottom: 0;
    font-weight: 500;
}

/* Enhanced Progress Bar */
.progress {
    height: 15px;
    border-radius: 20px;
    background: #e9ecef;
    overflow: hidden;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
    position: relative;
}

.progress-bar {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border-radius: 20px;
    transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.progress-bar::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

/* Enhanced Table */
.table {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    background: white;
}

.table th {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: none;
    font-weight: 700;
    color: #495057;
    padding: 18px 15px;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table td {
    padding: 18px 15px;
    border-bottom: 1px solid #f1f3f4;
    vertical-align: middle;
}

.table tbody tr {
    transition: all 0.3s ease;
}

.table tbody tr:hover {
    background: linear-gradient(135deg, #f8f9ff 0%, #f0f4ff 100%);
    transform: scale(1.01);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

/* Enhanced Buttons */
.btn {
    border-radius: 12px;
    font-weight: 700;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 12px 25px;
    position: relative;
    overflow: hidden;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s ease;
}

.btn:hover::before {
    left: 100%;
}

.btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.btn-info {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
}

.btn-warning {
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
}

.btn-success {
    background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
}

.btn-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #545b62 100%);
}

.btn-circle {
    border-radius: 50px;
    padding: 12px 20px;
}

/* Enhanced Modal */
.modal-content {
    border-radius: 20px;
    border: none;
    box-shadow: 0 25px 60px rgba(0,0,0,0.3);
    overflow: hidden;
}

.modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 20px 20px 0 0;
    border-bottom: none;
    padding: 25px 30px;
}

.modal-title {
    font-weight: 700;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.modal-body {
    padding: 30px;
    background: white;
}

.modal-footer {
    border-top: 1px solid #e9ecef;
    padding: 25px 30px;
    background: #f8f9fa;
}

/* Form Controls */
.form-control {
    border-radius: 12px;
    border: 2px solid #e9ecef;
    padding: 15px 18px;
    transition: all 0.3s ease;
    font-size: 0.95rem;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    transform: translateY(-2px);
}

.form-group label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Image Styling */
.size-40px {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    object-fit: cover;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.size-40px:hover {
    transform: scale(1.1);
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}

/* Text Enhancements */
.text-success {
    color: #28a745 !important;
    font-weight: 600;
}

.text-danger {
    color: #dc3545 !important;
    font-weight: 600;
}

.text-info {
    color: #17a2b8 !important;
    font-weight: 600;
}

.text-warning {
    color: #ffc107 !important;
    font-weight: 600;
}

/* Responsive Design */
@media (max-width: 768px) {
    .card {
        margin-bottom: 20px;
    }
    
    .card-body {
        padding: 20px;
    }
    
    .timeline {
        padding-left: 30px;
    }
    
    .timeline-marker {
        left: -35px;
        width: 16px;
        height: 16px;
    }
    
    .timeline-item:not(:last-child)::before {
        left: -28px;
    }
    
    .btn {
        padding: 10px 20px;
        font-size: 0.85rem;
    }
    
    .table {
        font-size: 0.9rem;
    }
}

/* Loading States */
.loading {
    position: relative;
    overflow: hidden;
}

.loading::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { left: -100%; }
    100% { left: 100%; }
}

/* Custom Scrollbar */
*::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

*::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

*::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
}

*::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
}
</style>
@endsection

