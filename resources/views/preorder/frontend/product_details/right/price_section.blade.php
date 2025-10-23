<div class=" mt-3 rounded-2 p-2 preorder-border-dashed-blue">

<div class="ml-3 my-2 d-flex justify-content-between">
    <div>
        @php
            $preorder_price = preorder_discount_price($product);
            $regular_price = $product->unit_price;
            $has_preorder_price = isset($product->preorder_price) && $product->preorder_price > 0;
        @endphp
        
        <p class="fs-20 fw-700 m-0 p-0 text-primary">
            <b>{{format_price($preorder_price)}}</b>
            @if($has_preorder_price && $preorder_price < $regular_price)
                <span class="badge badge-success ml-2">{{translate('Pre-order Discount')}}</span>
            @endif
        </p>
        
        @if($has_preorder_price)
            <p class="opacity-60 m-0 p-0">
                {{translate('Pre-order Price') .' '. format_price($product->preorder_price)}}
            </p>
        @endif
        
        <p class="opacity-60 m-0 p-0">
            {{translate('Regular price') .' '. format_price($regular_price)}}
            @if($preorder_price < $regular_price)
                <span class="text-success ml-1">({{translate('Save')}} {{format_price($regular_price - $preorder_price)}})</span>
            @endif
        </p>
        {{-- <p class="">{{translate('Minimum order quantity ') .' '. $product->min_qty}}</p> --}}
    </div>

    @if($product->is_prepayment)
    <div class="pr-3">
        <p class="text-capitalize m-0 p-0"><b>{{translate('Prepayment')}}</b></p>
        @if($product->is_prepayment_nedded)
        <p class="opacity-60 m-0 p-0">{{translate('Prepayment needed for Cash on Delivery')}}</p>
        @endif
        <p class="m-0 p-0 fs-20 fw-700 text-info">{{format_price($product->preorder_prepayment?->prepayment_amount)}}</p>
    </div>
    @endif

</div>
</div>

