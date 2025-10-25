@php
    $subtotal = 0;
    $tax = 0;
    $shipping = 0;

    if (isset($preorders_for_summary)) {
        foreach ($preorders_for_summary as $preorder) {
            $subtotal += $preorder->subtotal;
            $tax += $preorder->tax;
            $shipping += $preorder->shipping_cost;
        }
    }
    $grand_total = $subtotal + $tax + $shipping;
@endphp

<div class="card-header p-3">
    <h3 class="fs-16 fw-600 mb-0">{{ translate('Pre-order Summary') }}</h3>
</div>
<div class="card-body">
    <table class="table">
        <tfoot>
            <tr class="cart-subtotal">
                <th class="pl-0 fs-14 fw-400">{{ translate('Items Subtotal') }}</th>
                <td class="text-right pr-0 fs-14">
                    <span class="fw-600">{{ single_price($subtotal) }}</span>
                </td>
            </tr>
            <tr class="cart-shipping">
                <th class="pl-0 fs-14 fw-400">{{ translate('Total Shipping') }}</th>
                <td class="text-right pr-0 fs-14">
                    <span class="fw-600">{{ single_price($shipping) }}</span>
                </td>
            </tr>
            <tr class="cart-tax">
                <th class="pl-0 fs-14 fw-400">{{ translate('Total Tax') }}</th>
                <td class="text-right pr-0 fs-14">
                    <span class="fw-600">{{ single_price($tax) }}</span>
                </td>
            </tr>
            <tr class="cart-total">
                <th class="pl-0 fs-14 fw-600">{{ translate('Total Order Value') }}</th>
                <td class="text-right pr-0 fs-14">
                    <span class="fw-700">{{ single_price($grand_total) }}</span>
                </td>
            </tr>
            <tr class="cart-payment">
                <th class="pl-0 fs-14 fw-600 text-success">{{ translate('Advance Payment (50%)') }}</th>
                <td class="text-right pr-0 fs-14 text-success">
                    <span class="fw-700">{{ single_price($advance_amount ?? 0) }}</span>
                </td>
            </tr>
             <tr class="cart-due">
                <th class="pl-0 fs-14 fw-600 text-danger">{{ translate('Due on Delivery (50%)') }}</th>
                <td class="text-right pr-0 fs-14 text-danger">
                    <span class="fw-700">{{ single_price($grand_total - ($advance_amount ?? 0)) }}</span>
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="mt-4">
        <h4 class="text-center">{{ translate('Pay Now') }}:</h4>
        <h2 class="text-center fw-700 text-primary">{{ single_price($advance_amount ?? 0) }}</h2>
    </div>
</div>
