<div class="text-left">
    <!-- Product Name -->
    <p class="mb-4 fs-16 fw-700 text-dark break-word"> {{ $product->product_name }}</p>

    <div class="row align-items-center">
        <div class="col mb-3">
            <div class="d-flex justify-content-between">
                <!-- left -->
                <div class="mr-3 fs-14 text-dark  has-transitiuon hov-opacity-100">
                    <p class="m-0 p-0">
                        <span class="opacity-60"> {{ translate('Category') }} </span>
                        <span class="ml-1 opacity-100">{{$product->category->name}}</span>
                    </p>
                    <p class="m-0 p-0">
                        <span class="opacity-60"> {{ translate('Preorder Received') }} </span>
                        <span class="ml-1 opacity-100"><b>{{$product->preorder->count()}}</b></span>
                    </p>
                </div>

                <!-- right -->
                @if(get_setting('product_query_activation') == 1)
                <div class="fs-14 text-dark  has-transitiuon hov-opacity-100">
                    <a href="#pre_product_queries">
                        <u class="preorder-text-secondary">{{ translate('Ask About This Product') }} </u>
                    </a>
                </div>
                @endif

            </div>
        </div>
    </div>
    <div class="row align-items-center">
        <div class="col-md-12 m-0 p-0">
            <div class="ml-2">
                <span class="text-primary fs-12 fw-700 p-3 bg-light border border-primary rounded m-1 d-inline-block"
                    >{{$product->is_available ? translate( 'Available Now ')  : (strtotime($product->available_date) <= strtotime(date('Y-m-d')) ? translate( 'Available Now ') : translate('Available on ') .' '. $product->available_date .' '. (translate(' estimated')))}}</span>

                    @if($product->discount != null && $product->discount > 0 &&  $product->discount_start_date != null  && (strtotime(date('d-m-Y')) > $product->discount_start_date || strtotime(date('d-m-Y')) < $product->discount_end_date))
                    <span class="text-warning fs-12 fw-700 p-3 bg-light border border-warning rounded m-1 d-inline-block"
                    > {{ translate('Discount ')}} {{ $product->discount_type == 'flat' ? single_price($product->discount) : $product->discount.'%'}}</span>
                    @endif

                @if($product->is_prepayment)
                <span class="text-success fs-12 fw-700 p-3 bg-light border border-success rounded m-1 d-inline-block"
                    >{{ translate('Prepayment Needed') }}</span>
                @endif
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
        function show_conversation_modal(product_id) {
            @if(isCustomer())
                $.post('{{ route('preorder.conversation_modal') }}', {
                    _token: '{{ @csrf_token() }}',
                    product_id: product_id
                }, function(data) {
                    $('#product-conversation-modal-content').html(data);
                    $('#product-conversation-modal').modal('show', {
                        backdrop: 'static'
                    });
                });
            @elseif (Auth::check() && !isCustomer())
                AIZ.plugins.notify('warning', '{{ translate("Sorry, Only customers can ask questions.") }}');
            @else
                // Mohammad Hassan
$('#customerAuthModal').modal('show');
            @endif
        }

// Mohammad Hassan
    function showLoginModal() {
        showUserTypeModal();
    }
</script>

@if($product->variant_product)
<div class="row no-gutters mb-3">
    <div class="col-sm-12">
        @php
            $i = 0;
            $variations = get_combinations($product->choice_options);
        @endphp
        @foreach ($variations as $key => $variation)
            @php
                $variant = '';
                foreach (explode('-', $variation) as $key => $value) {
                    $variant .= $value;
                    if($key !== array_key_last(explode('-', $variation))) {
                        $variant .= ' / ';
                    }
                }
                $stock = $product->stocks->where('variant', $variation)->first();
            @endphp
            <div class="aiz-radio-inline mb-2">
                <label class="aiz-megabox pl-0 mr-2">
                    <input type="radio" name="variant" value="{{ $variation }}" @if($i == 0) checked @endif data-price="{{ $stock->price }}" data-quantity="{{ $stock->qty }}">
                    <span class="aiz-megabox-elem rounded-0 d-flex align-items-center justify-content-center py-1 px-3">
                        {{ $variant }}
                    </span>
                </label>
            </div>
            @php $i++; @endphp
        @endforeach
    </div>
</div>
@endif

