@php
    $shipping_data = \App\Models\ShippingChargeSetting::getSettings();
@endphp

@if($shipping_data && $shipping_data->is_enabled)
<!-- Shipping Charge Section with Enhanced Spacing -->
<div class="">
    <div class="card border-0 shadow-sm" style="margin-bottom: 10px;">
        <div class="card-body">
            <!-- Section Title -->
            <div class="d-flex align-items-center mb-4">
                <i class="las la-shipping-fast text-dark mr-2" style="font-size: 20px;"></i>
                <h6 class="mb-0 text-dark font-weight-bold">
                    {{ $shipping_data->title ?? translate('Shipping Information') }}
                </h6>
            </div>
            
            @if($shipping_data->description && !empty($shipping_data->description))
            <div class="text-dark mb-4" style="font-size: 14px; line-height: 1.5;">
                {!! $shipping_data->description !!}
            </div>
            @endif

            <!-- Shipping Charges -->
            <div class="shipping-charges mb-4">
                @if($shipping_data->inside_dhaka_charge && $shipping_data->inside_dhaka_charge > 0)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="text-dark font-weight-medium" style="font-size: 14px;">{{ translate('Inside Dhaka') }}:</span>
                    <span class="text-dark font-weight-bold" style="font-size: 14px;">৳{{ number_format($shipping_data->inside_dhaka_charge, 0) }}</span>
                </div>
                @endif

                @if($shipping_data->outside_dhaka_charge && $shipping_data->outside_dhaka_charge > 0)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="text-dark font-weight-medium" style="font-size: 14px;">{{ translate('Outside Dhaka') }}:</span>
                    <span class="text-dark font-weight-bold" style="font-size: 14px;">৳{{ number_format($shipping_data->outside_dhaka_charge, 0) }}</span>
                </div>
                @endif

                @if($shipping_data->free_shipping_threshold && $shipping_data->free_shipping_threshold > 0)
                <div class="mt-3 p-2 bg-light rounded" style="border-left: 4px solid #28a745;">
                    <div class="d-flex align-items-center text-success">
                        <i class="las la-gift mr-2"></i>
                        <small class="font-weight-medium">
                            {{ translate('Free shipping on orders over') }} ৳{{ number_format($shipping_data->free_shipping_threshold, 0) }}
                        </small>
                    </div>
                </div>
                @endif
            </div>

            <!-- Delivery Time -->
            @if(($shipping_data->delivery_time_inside && !empty($shipping_data->delivery_time_inside)) || 
                ($shipping_data->delivery_time_outside && !empty($shipping_data->delivery_time_outside)))
            <div class="delivery-time">
                <div class="d-flex align-items-center mb-3">
                    <i class="las la-clock text-dark mr-2" style="font-size: 16px;"></i>
                    <span class="text-dark font-weight-bold" style="font-size: 14px;">{{ translate('Delivery Time') }}</span>
                </div>
                
                @if($shipping_data->delivery_time_inside && !empty($shipping_data->delivery_time_inside))
                <div class="d-flex justify-content-between align-items-center py-1 mb-2">
                    <span class="text-dark" style="font-size: 13px;">{{ translate('Inside Dhaka') }}:</span>
                    <span class="text-dark font-weight-medium" style="font-size: 13px;">{{ $shipping_data->delivery_time_inside }}</span>
                </div>
                @endif

                @if($shipping_data->delivery_time_outside && !empty($shipping_data->delivery_time_outside))
                <div class="d-flex justify-content-between align-items-center py-1">
                    <span class="text-dark" style="font-size: 13px;">{{ translate('Outside Dhaka') }}:</span>
                    <span class="text-dark font-weight-medium" style="font-size: 13px;">{{ $shipping_data->delivery_time_outside }}</span>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endif