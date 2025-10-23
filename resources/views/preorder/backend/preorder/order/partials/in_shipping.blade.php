<!-- In Shipping -->
<div class="card rounded-0 border shadow-none mb-3">
    <div class="card-header border-bottom-0 py-3" id="headingInShipping" type="button" data-toggle="collapse" data-target="#collapseInShipping" aria-expanded="false" aria-controls="collapseInShipping">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                    <path d="M58,48A10,10,0,1,0,68,58,10,10,0,0,0,58,48ZM56.457,61.543a.663.663,0,0,1-.423.212.693.693,0,0,1-.428-.216l-2.692-2.692.856-.856,2.269,2.269,6-6.043.841.87Z" transform="translate(-48 -48)" fill="{{ preorder_fill_color($order->shipping_status) }}" />
                </svg>
                <span class="ml-2 fs-16 fw-700">{{ translate('4. In Shipping') }}</span>
            </div>
             <i class="las la-angle-down fs-20"></i>
        </div>
    </div>

    <div id="collapseInShipping" class="collapse" aria-labelledby="headingInShipping" data-parent="#accordioncCheckoutInfo">
        <div class="card-body">
            <form action="{{route('preorder-order.status_update', $order->id)}}" method="POST" id="shipping_status_form">
                @csrf
                @method('PUT')
                <input type="hidden" name="shipping_status" value="2">
                <input type="hidden" name="status"  id="preorder_shipping_status" value="">

                @if($order->shipping_note)
                    <p><strong>{{translate('Admin Note:')}}</strong></p>
                    <blockquote class="blockquote fs-13">{{ $order->shipping_note }}</blockquote>
                @endif

                @if(!in_array($order->shipping_status, [2, 3]))
                    <div class="form-group">
                        <label>{{translate('Proof of shipping')}}</label>
                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                            <div class="input-group-prepend"><div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div></div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="shipping_proof" class="selected-files" value="{{$order->shipping_proof}}">
                        </div>
                        <div class="file-preview box sm"></div>
                    </div>
                    <div class="form-group">
                        <label>{{translate('Note')}} <span class="fs-12 text-muted">{{translate('(Upto 200 character)')}}</span></label>
                        <textarea name="shipping_note" rows="3" class="form-control"></textarea>
                    </div>
                @endif

                @if($order->product_owner == 'admin' && $order->final_order_status == 2)
                    <div class="row">
                        @if($order->shipping_status == 0 && auth()->user()->can('update_preorder_status'))
                            <div class="col-md-6 mt-1"><button type="button" class="btn btn-block btn-danger" onclick="preorderConfirmation(3, this, 'Shipping')">{{ translate('Cancel')}}</button></div>
                            <div class="col-md-6 mt-1"><button type="button" class="btn btn-block btn-primary" onclick="preorderConfirmation(2, this, 'Shipping')">{{  translate('Mark as Shipping') }}</button></div>
                        @elseif(in_array($order->shipping_status, [2, 3]))
                            <div class="col-12"><div class="alert alert-{{$order->shipping_status == 2 ? 'success':'danger'}}">{{$order->shipping_status == 2 ? translate('Marked as shipping.') : translate('Shipping was cancelled.')}}</div></div>
                        @endif
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>```

**৫. `delivery.blade.php`** (নতুন ফাইল তৈরি করুন)
```blade
<!-- Delivery -->
<div class="card rounded-0 border shadow-none mb-3">
    <div class="card-header border-bottom-0 py-3" id="headingDelivery" type="button" data-toggle="collapse" data-target="#collapseDelivery" aria-expanded="false" aria-controls="collapseDelivery">
         <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                    <path d="M58,48A10,10,0,1,0,68,58,10,10,0,0,0,58,48ZM56.457,61.543a.663.663,0,0,1-.423.212.693.693,0,0,1-.428-.216l-2.692-2.692.856-.856,2.269,2.269,6-6.043.841.87Z" transform="translate(-48 -48)" fill="{{ preorder_fill_color($order->delivery_status) }}" />
                </svg>
                <span class="ml-2 fs-16 fw-700">{{ translate('5. Delivery') }}</span>
            </div>
             <i class="las la-angle-down fs-20"></i>
        </div>
    </div>

    <div id="collapseDelivery" class="collapse" aria-labelledby="headingDelivery" data-parent="#accordioncCheckoutInfo">
        <div class="card-body">
            <form action="{{route('preorder-order.status_update', $order->id)}}" method="POST" id="delivery_status_form">
                @csrf
                @method('PUT')
                <input type="hidden" name="delivery_status" value="1">
                <input type="hidden" name="status" id="delivery-status" value="">

                @if($order->delivery_note)
                    <p><strong>{{translate('Admin Note:')}}</strong></p>
                    <blockquote class="blockquote fs-13">{{ $order->delivery_note }}</blockquote>
                @endif

                @if(!in_array($order->delivery_status, [2, 3]))
                    <div class="form-group">
                        <label>{{translate('Note')}} <span class="fs-12 text-muted">{{translate(('(Upto 200 character)'))}}</span></label>
                        <textarea name="delivery_note" rows="3" class="form-control"></textarea>
                    </div>
                @endif

                @if($order->product_owner == 'admin' && $order->shipping_status == 2)
                    <div class="row">
                        @if($order->delivery_status == 0 && auth()->user()->can('update_preorder_status'))
                            <div class="col-md-6 mt-1"><button type="button" class="btn btn-block btn-danger" onclick="preorderConfirmation(3, this, 'Delivery')">{{ translate('Cancel')}}</button></div>
                            <div class="col-md-6 mt-1"><button type="button" class="btn btn-block btn-success" onclick="preorderConfirmation(2, this, 'Delivery')">{{ translate('Mark as Delivered') }}</button></div>
                        @elseif(in_array($order->delivery_status, [2, 3]))
                             <div class="col-12"><div class="alert alert-{{$order->delivery_status == 2 ? 'success':'danger'}}">{{$order->delivery_status == 2 ? translate('Marked as delivered.') : translate('Delivery was cancelled.')}}</div></div>
                        @endif
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
