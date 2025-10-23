<!-- Prepayment Request -->
<div class="card rounded-0 border shadow-none mb-3">
    <div class="card-header border-bottom-0 py-3" id="headingPrepaymentRequest" type="button" data-toggle="collapse" data-target="#collapsePrepaymentRequest" aria-expanded="false" aria-controls="collapsePrepaymentRequest">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                    <path d="M58,48A10,10,0,1,0,68,58,10,10,0,0,0,58,48ZM56.457,61.543a.663.663,0,0,1-.423.212.693.693,0,0,1-.428-.216l-2.692-2.692.856-.856,2.269,2.269,6-6.043.841.87Z" transform="translate(-48 -48)" fill="{{ preorder_fill_color($order->prepayment_confirm_status) }}" />
                </svg>
                <span class="ml-2 fs-16 fw-700">{{ translate('2. Prepayment Request') }}</span>
            </div>
            <i class="las la-angle-down fs-20"></i>
        </div>
    </div>

    <div id="collapsePrepaymentRequest" class="collapse" aria-labelledby="headingPrepaymentRequest" data-parent="#accordioncCheckoutInfo">
        <div class="card-body">
            <form action="{{route('preorder-order.status_update', $order->id)}}" method="POST" id="prepayment_confirm_form">
                @csrf
                @method('PUT')
                <input type="hidden" name="prepayment_confirm_status" value="1">
                <input type="hidden" name="status"  id="prepayment-status" value="">

                @if($order->payment_proof)
                    <p><strong>{{translate('Proof of payment:')}}</strong> <a href="{{ uploaded_asset($order->payment_proof) }}" target="_blank" class="btn btn-sm btn-info">{{ translate('View Proof') }}</a></p>
                @endif
                <p><strong>{{translate('Reference No:')}}</strong> {{ $order->reference_no ?? 'N/A' }}</p>
                <p><strong>{{translate('Customer Note:')}}</strong> {{ $order->confirm_note ?? 'N/A' }}</p>

                @if($order->product_owner == 'admin' && $order->request_preorder_status == 2)
                    <div class="row">
                        @if($order->prepayment_confirm_status == 1 && auth()->user()->can('update_preorder_status'))
                            <div class="col-md-6 mt-1"><button type="button" class="btn btn-block btn-danger" onclick="preorderConfirmation(3, this, 'Prepayment Request')">{{ translate('Reject')}}</button></div>
                            <div class="col-md-6 mt-1"><button type="button" class="btn btn-block btn-success" onclick="preorderConfirmation(2, this, 'Prepayment Request')">{{  translate('Accept') }}</button></div>
                        @elseif(in_array($order->prepayment_confirm_status, [2, 3]))
                            <div class="col-12"><div class="alert alert-{{$order->prepayment_confirm_status == 2 ? 'success':'danger'}}">{{$order->prepayment_confirm_status == 2 ? translate('Payment was accepted.') : translate('Payment was rejected.')}}</div></div>
                        @endif
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
