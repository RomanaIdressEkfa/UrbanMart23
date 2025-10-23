<!-- Request Preorder -->
<div class="card rounded-0 border shadow-none mb-3">
    <div class="card-header border-bottom-0 py-3" id="headingRequestPreorder" type="button" data-toggle="collapse" data-target="#collapsePreorderRequest" aria-expanded="true" aria-controls="collapsePreorderRequest">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                    <path d="M58,48A10,10,0,1,0,68,58,10,10,0,0,0,58,48ZM56.457,61.543a.663.663,0,0,1-.423.212.693.693,0,0,1-.428-.216l-2.692-2.692.856-.856,2.269,2.269,6-6.043.841.87Z" transform="translate(-48 -48)" fill="{{preorder_fill_color($order->request_preorder_status)}}" />
                </svg>
                <span class="ml-2 fs-16 fw-700">{{ translate('1. Request Preorder') }}</span>
            </div>
            <i class="las la-angle-down fs-20"></i>
        </div>
    </div>

    <div id="collapsePreorderRequest" class="collapse show" aria-labelledby="headingRequestPreorder" data-parent="#accordioncCheckoutInfo">
        <form action="{{route('preorder-order.status_update', $order->id)}}" method="POST" id="preorder_request_form">
            @csrf
            @method('PUT')
            <input type="hidden" name="preorder_request_status" value="1">
            <input type="hidden" name="status" id="preorder-status" value="">
            <div class="card-body">
                @if($order->request_note)
                    <p><strong>{{translate('Customer Note:')}}</strong></p>
                    <blockquote class="blockquote fs-13">{{$order->request_note}}</blockquote>
                @endif

                @if($order->product_owner == 'admin')
                <div class="row">
                    @php $requestPreorderStatus = $order->request_preorder_status; @endphp
                    @if(in_array($requestPreorderStatus, [2, 3]))
                        <div class="col-12">
                            <div class="alert alert-{{ $requestPreorderStatus == 2 ? 'success' : 'danger' }}">
                                {{  $requestPreorderStatus == 2 ? translate('Request was accepted.') : translate('Request was rejected.')  }}
                            </div>
                        </div>
                    @elseif($requestPreorderStatus == 1 && auth()->user()->can('update_preorder_status'))
                        <div class="col-md-6 mt-1">
                            <button type="button" class="btn btn-block btn-danger" onclick="preorderConfirmation(3, this, 'Preorder Request')">{{ translate('Reject')}}</button>
                        </div>
                        <div class="col-md-6 mt-1">
                            <button type="button" class="btn btn-block btn-success" onclick="preorderConfirmation(2, this, 'Preorder Request')">{{  translate('Accept') }}</button>
                        </div>
                    @endif
                </div>
                @endif
            </div>
        </form>
    </div>
</div>
