<div class="row gutters-5">
    @foreach ($addresses as $key => $address)
        <div class="col-md-6 mb-3">
            <label class="aiz-megabox d-block bg-white">
                <input type="radio" name="address_id" value="{{ $address->id }}" @if ($address->set_default) checked @endif onchange="updateDeliveryAddress({{ $address->id }})">
                <span class="d-flex p-3 aiz-megabox-elem">
                    <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                    <span class="flex-grow-1 pl-3 text-left">
                        <div>
                            <span class="opacity-60">{{ translate('Address') }}:</span>
                            <span class="fw-600 ml-2">{{ $address->address }}</span>
                        </div>
                        <div>
                            <span class="opacity-60">{{ translate('City') }}:</span>
                            <span class="fw-600 ml-2">{{ optional($address->city)->name }}</span>
                        </div>
                        <div>
                            <span class="opacity-60">{{ translate('Country') }}:</span>
                            <span class="fw-600 ml-2">{{ optional($address->country)->name }}</span>
                        </div>
                        <div>
                            <span class="opacity-60">{{ translate('Phone') }}:</span>
                            <span class="fw-600 ml-2">{{ $address->phone }}</span>
                        </div>
                    </span>
                </span>
            </label>
            <div class="dropdown position-absolute top-0 right-0">
                <button class="btn bg-gray px-2" type="button" data-toggle="dropdown">
                    <i class="la la-ellipsis-v"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" onclick="edit_address('{{$address->id}}')">
                        {{ translate('Edit') }}
                    </a>
                </div>
            </div>
        </div>
    @endforeach
    
    <div class="col-md-6 mx-auto mb-3">
        <div class="border p-3 rounded mb-3 c-pointer text-center bg-white" onclick="add_new_address()">
            <i class="las la-plus la-2x"></i>
            <div class="alpha-7">{{ translate('Add New Address') }}</div>
        </div>
    </div>
</div>