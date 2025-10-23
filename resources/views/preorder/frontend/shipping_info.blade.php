@extends('frontend.layouts.app')

@section('content')
<section class="pt-5 mb-4">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 mx-auto">
                <div class="row aiz-steps arrow-divider">
                    <div class="col done">
                        <div class="text-center text-success">
                            <i class="la-3x mb-2 las la-shopping-cart"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block">{{ translate('1. My Cart') }}</h3>
                        </div>
                    </div>
                    <div class="col active">
                        <div class="text-center text-primary">
                            <i class="la-3x mb-2 las la-map"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block text-primary">{{ translate('2. Shipping info') }}</h3>
                        </div>
                    </div>
                    <div class="col">
                        <div class="text-center">
                            <i class="la-3x mb-2 opacity-50 las la-truck"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block opacity-50">{{ translate('3. Delivery info') }}</h3>
                        </div>
                    </div>
                    <div class="col">
                        <div class="text-center">
                            <i class="la-3x mb-2 opacity-50 las la-credit-card"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block opacity-50">{{ translate('4. Payment') }}</h3>
                        </div>
                    </div>
                    <div class="col">
                        <div class="text-center">
                            <i class="la-3x mb-2 opacity-50 las la-check-circle"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block opacity-50">{{ translate('5. Confirmation') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="mb-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header">
                        <h1 class="fs-16 fw-600 mb-0">{{ translate('Shipping Information') }}</h1>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('preorder.save_shipping_info') }}" method="POST">
                            @csrf
                            
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if(Auth::check())
                                @if($addresses->count() > 0)
                                    <h6 class="fs-15 fw-600">{{ translate('Select an address') }}</h6>
                                    @foreach ($addresses as $key => $address)
                                        <div class="border mb-4">
                                            <label class="aiz-megabox d-block bg-white mb-0">
                                                <input type="radio" name="address_id" value="{{ $address->id }}" 
                                                    @if ($address->id == $address_id) checked @endif required>
                                                <span class="d-flex p-3 aiz-megabox-elem border-0">
                                                    <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                                    <span class="flex-grow-1 pl-3 text-left">
                                                        <div class="row">
                                                            <span class="fs-14 text-secondary col-3">{{ translate('Address') }}</span>
                                                            <span class="fs-14 text-dark fw-500 col">{{ $address->address }}</span>
                                                        </div>
                                                        <div class="row">
                                                            <span class="fs-14 text-secondary col-3">{{ translate('Postal Code') }}</span>
                                                            <span class="fs-14 text-dark fw-500 col">{{ $address->postal_code }}</span>
                                                        </div>
                                                        <div class="row">
                                                            <span class="fs-14 text-secondary col-3">{{ translate('City') }}</span>
                                                            <span class="fs-14 text-dark fw-500 col">{{ optional($address->city)->name }}</span>
                                                        </div>
                                                        <div class="row">
                                                            <span class="fs-14 text-secondary col-3">{{ translate('Phone') }}</span>
                                                            <span class="fs-14 text-dark fw-500 col">{{ $address->phone }}</span>
                                                        </div>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endforeach
                                    
                                    <div class="border p-3 c-pointer text-center bg-light has-transition hov-bg-soft-light h-100 d-flex flex-column justify-content-center mb-4"
                                        onclick="add_new_address()">
                                        <i class="las la-plus mb-1 fs-20 text-gray"></i>
                                        <div class="alpha-7 fw-700">{{ translate('Add New Address') }}</div>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        {{ translate('No saved addresses found. Please add a new address.') }}
                                    </div>
                                    <div class="border p-3 c-pointer text-center bg-light has-transition hov-bg-soft-light h-100 d-flex flex-column justify-content-center mb-4"
                                        onclick="add_new_address()">
                                        <i class="las la-plus mb-1 fs-20 text-gray"></i>
                                        <div class="alpha-7 fw-700">{{ translate('Add New Address') }}</div>
                                    </div>
                                @endif
                                <input type="hidden" name="checkout_type" value="logged">
                            @else
                                <!-- Guest Shipping Info -->
                                @include('frontend.partials.cart.guest_shipping_info')
                            @endif

                            <div class="pt-3">
                                <button type="submit" class="btn btn-primary fw-600">
                                    {{ translate('Continue to Payment') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header">
                        <h3 class="fs-16 fw-600 mb-0">{{ translate('Order Summary') }}</h3>
                    </div>
                    <div class="card-body">
                        @php
                            $preorder_ids = session('preorder_ids', []);
                            $preorders = \App\Models\Preorder::whereIn('id', $preorder_ids)->get();
                            $total = session('preorder_total', 0);
                        @endphp
                        
                        @foreach($preorders as $preorder)
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ $preorder->product->name ?? 'Product' }}</span>
                                <span>৳{{ number_format($preorder->prepayment, 2) }}</span>
                            </div>
                        @endforeach
                        
                        <hr>
                        <div class="d-flex justify-content-between fw-600">
                            <span>{{ translate('Total') }}</span>
                            <span>৳{{ number_format($total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('modals.address_modal')

@endsection

@section('script')
<script type="text/javascript">
    function add_new_address(){
        $('#new-address-modal').modal('show');
    }

    function edit_address(address_id){
        var url = '{{ route("addresses.edit", ":id") }}';
        url = url.replace(':id', address_id);
        
        $.get(url, function(data) {
            $('#edit_modal_body').html(data);
            $('#edit-address-modal').modal('show');
        });
    }
</script>
@endsection

