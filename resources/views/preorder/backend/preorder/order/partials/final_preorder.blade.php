<!-- Final Preorder -->
<div class="card rounded-0 border shadow-none mb-3">
    <div class="card-header border-bottom-0 py-3" id="headingFinalPreorder" type="button" data-toggle="collapse" data-target="#collapseFinalPreorder" aria-expanded="false" aria-controls="collapseFinalPreorder">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                    <path d="M58,48A10,10,0,1,0,68,58,10,10,0,0,0,58,48ZM56.457,61.543a.663.663,0,0,1-.423.212.693.693,0,0,1-.428-.216l-2.692-2.692.856-.856,2.269,2.269,6-6.043.841.87Z" transform="translate(-48 -48)" fill="{{ preorder_fill_color($order->final_order_status) }}" />
                </svg>
                <span class="ml-2 fs-16 fw-700">{{ translate('3. Final Preorder') }}</span>
            </div>
             <i class="las la-angle-down fs-20"></i>
        </div>
    </div>

    <div id="collapseFinalPreorder" class="collapse" aria-labelledby="headingFinalPreorder" data-parent="#accordioncCheckoutInfo">
        <div class="card-body">
            <form action="{{route('preorder-order.status_update', $order->id)}}" method="POST" id="final_order_form">
                @csrf
                @method('PUT')
                <input type="hidden" name="final_order_status" value="1">
                <input type="hidden" name="status" id="final-order-status" value="">

                @if($order->cod_for_final_order)
                    <p><strong>{{translate('Payment Type')}}:</strong> {{translate('Cash on delivery')}}</p>
                @else
                    @if($order->final_payment_proof)
                    <p><strong>{{translate('Proof of payment:')}}</strong> <a href="{{ uploaded_asset($order->final_payment_proof) }}" target="_blank" class="btn btn-sm btn-info">{{ translate('View Proof') }}</a></p>
                    @endif
                    <p><strong>{{translate('Reference No.:')}}</strong> {{ $order->final_payment_reference_no ?? 'N/A' }}</p>
                @endif
                <p><strong>{{translate('Customer Note:')}}</strong> {{ $order->final_payment_confirm_note ?? 'N/A' }}</p>

                @if($order->product_owner == 'admin' && ($order->prepayment_confirm_status == 2 || !$order->preorder_product->is_prepayment) )
                    <div class="row">
                        @if($order->final_order_status == 1 && auth()->user()->can('update_preorder_status'))
                            <div class="col-md-6 mt-1"><button type="button" class="btn btn-block btn-danger" onclick="preorderConfirmation(3, this, 'Final Order Request')">{{ translate('Reject')}}</button></div>
                            <div class="col-md-6 mt-1"><button type="button" class="btn btn-block btn-success" onclick="preorderConfirmation(2, this, 'Final Order Request')">{{ translate('Accept') }}</button></div>
                        @elseif(in_array($order->final_order_status, [2, 3]))
                            <div class="col-12"><div class="alert alert-{{$order->final_order_status == 2 ? 'success':'danger'}}">{{$order->final_order_status == 2 ? translate('Final order accepted.') : translate('Final order rejected.')}}</div></div>
                        @endif
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
