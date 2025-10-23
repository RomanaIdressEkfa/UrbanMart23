<div class="p-2 container-full">
    {{-- @include('preorder.frontend.partials.modern_shipping_info') --}}
    <div class=" pt-3">
        <div class="col-md-2 mt-md-2"></div>
        <div class="col-md-10">
            <div class="bg-soft-info p-2">
                {{ translate('If you have already used the same email address or phone number before, please ') }}
                <a href="javascript:void(0);" onclick="showUserTypeModal()" class="fw-700 animate-underline-primary">{{ translate('Login') }}</a>
                {{ translate(' first to continue') }}
            </div>
        </div>
    </div>
</div>

