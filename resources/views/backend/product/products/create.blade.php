@extends('backend.layouts.app')

@section('content')

    <div class="page-content">
        <div class="aiz-titlebar text-left mt-2 pb-2 px-3 px-md-2rem border-bottom border-gray">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="h3">{{ translate('Add New Product') }}</h1>
                </div>
                <div class="col text-right">
                    <a class="btn btn-xs btn-soft-primary" href="javascript:void(0);" onclick="clearTempdata()">
                        {{ translate('Clear Tempdata') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="d-sm-flex">
            <!-- page side nav -->
            <div class="page-side-nav c-scrollbar-light px-3 py-2">
                <ul class="nav nav-tabs flex-sm-column border-0" role="tablist" aria-orientation="vertical">
                    <!-- General -->
                    <li class="nav-item">
                        <a class="nav-link" id="general-tab" href="#general" data-toggle="tab" data-target="#general"
                            type="button" role="tab" aria-controls="general" aria-selected="true">
                            {{ translate('General') }}
                        </a>
                    </li>
                    <!-- Files & Media -->
                    <li class="nav-item">
                        <a class="nav-link" id="files-and-media-tab" href="#files_and_media" data-toggle="tab"
                            data-target="#files_and_media" type="button" role="tab" aria-controls="files_and_media"
                            aria-selected="false">
                            {{ translate('Files & Media') }}
                        </a>
                    </li>
                    <!-- Price & Stock -->
                    <li class="nav-item">
                        <a class="nav-link" id="price-and-stocks-tab" href="#price_and_stocks" data-toggle="tab"
                            data-target="#price_and_stocks" type="button" role="tab" aria-controls="price_and_stocks"
                            aria-selected="false">
                            {{ translate('Price & Stock') }}
                        </a>
                    </li>
                    <!-- SEO -->
                    <li class="nav-item">
                        <a class="nav-link" id="seo-tab" href="#seo" data-toggle="tab" data-target="#seo"
                            type="button" role="tab" aria-controls="seo" aria-selected="false">
                            {{ translate('SEO') }}
                        </a>
                    </li>
                    <!-- Shipping -->
                    <li class="nav-item">
                        <a class="nav-link" id="shipping-tab" href="#shipping" data-toggle="tab" data-target="#shipping"
                            type="button" role="tab" aria-controls="shipping" aria-selected="false">
                            {{ translate('Shipping') }}
                        </a>
                    </li>
                    <!-- Warranty -->
                    <li class="nav-item">
                        <a class="nav-link" id="warranty-tab" href="#warranty" data-toggle="tab" data-target="#warranty"
                            type="button" role="tab" aria-controls="warranty" aria-selected="false">
                            {{ translate('Warranty') }}
                        </a>
                    </li>
                    <!-- Frequently Bought Product -->
                   
                </ul>
            </div>

            <!-- tab content -->
            <div class="flex-grow-1 p-sm-3 p-lg-2rem mb-2rem mb-md-0">
                <!-- Error Meassages -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Data type -->
                <input type="hidden" id="data_type" value="physical">

                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" id="choice_form">
                    @csrf
                    <div class="tab-content">
                        <!-- General -->
                        <div class="tab-pane fade" id="general" role="tabpanel" aria-labelledby="general-tab">
                            <div class="bg-white p-3 p-sm-2rem">
                                <!-- Product Information -->
                                <h5 class="mb-3 pb-3 fs-17 fw-700" style="border-bottom: 1px dashed #e4e5eb;">
                                    {{ translate('Product Information') }}</h5>
                                <div class="w-100">
                                    <div class="row">
                                        <div class="col-xxl-7 col-xl-6">
                                            <!-- Product Name -->
                                            <div class="form-group row">
                                                <label
                                                    class="col-xxl-3 col-from-label fs-13">{{ translate('Product Name') }}
                                                    <span class="text-danger">*</span></label>
                                                <div class="col-xxl-9">
                                                    <input type="text"
                                                        class="form-control @error('name') is-invalid @enderror"
                                                        name="name" value="{{ old('name') }}"
                                                        placeholder="{{ translate('Product Name') }}"
                                                        onchange="update_sku()" required>
                                                </div>
                                            </div>
                                            <!-- Brand -->
                                            <div class="form-group row" id="brand">
                                                <label
                                                    class="col-xxl-3 col-from-label fs-13">{{ translate('Brand') }}</label>
                                                <div class="col-xxl-9">
                                                    <select class="form-control aiz-selectpicker" name="brand_id"
                                                        id="brand_id" data-live-search="true">
                                                        <option value="">{{ translate('Select Brand') }}</option>
                                                        @foreach (\App\Models\Brand::all() as $brand)
                                                            <option value="{{ $brand->id }}"
                                                                @selected(old('brand_id') == $brand->id)>
                                                                {{ $brand->getTranslation('name') }}</option>
                                                        @endforeach
                                                    </select>
                                                    <small
                                                        class="text-muted">{{ translate("You can choose a brand if you'd like to display your product by brand.") }}</small>
                                                </div>
                                            </div>
                                            <!-- Unit -->
                                            <div class="form-group row">
                                                <label class="col-xxl-3 col-from-label fs-13">{{ translate('Unit') }}
                                                    <span class="text-danger">*</span></label>
                                                <div class="col-xxl-9">
                                                    <input type="text"
                                                        class="form-control @error('unit') is-invalid @enderror"
                                                        name="unit" value="{{ old('unit') }}"
                                                        placeholder="{{ translate('Unit (e.g. KG, Pc etc)') }}" required>
                                                </div>
                                            </div>
                                            <!-- Weight -->
                                            <div class="form-group row">
                                                <label class="col-xxl-3 col-from-label fs-13">{{ translate('Weight') }}
                                                    <small>({{ translate('In Kg') }})</small></label>
                                                <div class="col-xxl-9">
                                                    <input type="number" class="form-control" name="weight"
                                                        value="{{ old('weight') ?? 0.0 }}" step="0.01"
                                                        placeholder="0.00">
                                                </div>
                                            </div>
                                            <!-- Minimum Purchase Qty -->
                                            <div class="form-group row">
                                                <label
                                                    class="col-xxl-3 col-from-label fs-13">{{ translate('Minimum Purchase Qty') }}
                                                    <span class="text-danger">*</span></label>
                                                <div class="col-xxl-9">
                                                    <input type="number" lang="en"
                                                        class="form-control @error('min_qty') is-invalid @enderror"
                                                        name="min_qty" value="{{ old('min_qty') ?? 1 }}" placeholder="1"
                                                        min="1" required>
                                                    <small
                                                        class="text-muted">{{ translate('The minimum quantity needs to be purchased by your customer.') }}</small>
                                                </div>
                                            </div>
                                            <!-- Tags -->
                                            <div class="form-group row">
                                                <label
                                                    class="col-xxl-3 col-from-label fs-13">{{ translate('Tags') }}</label>
                                                <div class="col-xxl-9">
                                                    <input type="text" class="form-control aiz-tag-input"
                                                        name="tags[]"
                                                        placeholder="{{ translate('Type and hit enter to add a tag') }}">
                                                    <small
                                                        class="text-muted">{{ translate('This is used for search. Input those words by which cutomer can find this product.') }}</small>
                                                </div>
                                            </div>

                                            @if (addon_is_activated('pos_system'))
                                                <!-- Barcode -->
                                                <div class="form-group row">
                                                    <label
                                                        class="col-xxl-3 col-from-label fs-13">{{ translate('Barcode') }}</label>
                                                    <div class="col-xxl-9">
                                                        <input type="text" class="form-control" name="barcode"
                                                            value="{{ old('barcode') }}"
                                                            placeholder="{{ translate('Barcode') }}">
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Product Category -->
                                        <div class="col-xxl-5 col-xl-6">
                                            <div
                                                class="card @if ($errors->has('category_ids') || $errors->has('category_id')) border border-danger @endif">
                                                <div class="card-header">
                                                    <h5 class="mb-0 h6">{{ translate('Product Category') }}</h5>
                                                    <h6 class="float-right fs-13 mb-0">
                                                        {{ translate('Select Main') }}
                                                        <span class="position-relative main-category-info-icon">
                                                            <i class="las la-question-circle fs-18 text-info"></i>
                                                            <span
                                                                class="main-category-info bg-soft-info p-2 position-absolute d-none border">{{ translate('This will be used for commission based calculations and homepage category wise product Show.') }}</span>
                                                        </span>
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="h-300px overflow-auto c-scrollbar-light">
                                                        <ul class="hummingbird-treeview-converter list-unstyled"
                                                            data-checkbox-name="category_ids[]"
                                                            data-radio-name="category_id">
                                                            @foreach ($categories as $category)
                                                                <li id="{{ $category->id }}">
                                                                    {{ $category->getTranslation('name') }}</li>
                                                                @foreach ($category->childrenCategories as $childCategory)
                                                                    @include(
                                                                        'backend.product.products.child_category',
                                                                        ['child_category' => $childCategory]
                                                                    )
                                                                @endforeach
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div class="form-group">
                                        <label class="fs-13">{{ translate('Description') }}</label>
                                        <div class="">
                                            <textarea class="aiz-text-editor" name="description">{{ old('description') }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Refund -->
                                @if (addon_is_activated('refund_request'))
                                    <h5 class="mb-3 mt-5 pb-3 fs-17 fw-700" style="border-bottom: 1px dashed #e4e5eb;">
                                        {{ translate('Refund') }}</h5>
                                    <div class="w-100">
                                        <!-- Refundable -->
                                        <div class="form-group row">
                                            <label class="col-md-3 col-from-label">{{ translate('Refundable') }}?</label>
                                            <div class="col-md-9">
                                                <label class="aiz-switch aiz-switch-success mb-0 d-block">
                                                    <input type="checkbox" name="refundable" value="1"
                                                        onchange="isRefundable()"
                                                        @if (get_setting('refund_type') != 'category_based_refund') checked @endif>
                                                    <span></span>
                                                </label>
                                                <small id="refundable-note" class="text-muted d-none"></small>
                                            </div>
                                        </div>
                                        <div class="w-100 refund-block d-none">
                                            <div class="form-group row">
                                                <div class="col-md-12">
                                                    <label class="form-check-label fw-bold" for="flexCheckChecked">
                                                        <b>{{ translate('Note (Add from preset)') }} </b>
                                                    </label>
                                                </div>
                                            </div>

                                            <input type="hidden" name="refund_note_id" id="refund_note_id">
                                            <div id="refund_note" class=""></div>
                                            <button type="button"
                                                class="btn btn-block border border-dashed hov-bg-soft-secondary mt-2 fs-14 rounded-0 d-flex align-items-center justify-content-center"
                                                onclick="noteModal('refund')">
                                                <i class="las la-plus"></i>
                                                <span class="ml-2">{{ translate('Select Refund Note') }}</span>
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                <!-- Status -->
                                <h5 class="mb-3 mt-5 pb-3 fs-17 fw-700" style="border-bottom: 1px dashed #e4e5eb;">
                                    {{ translate('Status') }}</h5>
                                <div class="w-100">
                                    <!-- Featured -->
                                    <div class="form-group row">
                                        <label class="col-md-3 col-from-label">{{ translate('Featured') }}</label>
                                        <div class="col-md-9">
                                            <label class="aiz-switch aiz-switch-success mb-0 d-block">
                                                <input type="checkbox" name="featured" value="1">
                                                <span></span>
                                            </label>
                                            <small
                                                class="text-muted">{{ translate('If you enable this, this product will be granted as a featured product.') }}</small>
                                        </div>
                                    </div>
                                    <!-- Todays Deal -->
                                    <div class="form-group row">
                                        <label class="col-md-3 col-from-label">{{ translate('Todays Deal') }}</label>
                                        <div class="col-md-9">
                                            <label class="aiz-switch aiz-switch-success mb-0 d-block">
                                                <input type="checkbox" name="todays_deal" value="1">
                                                <span></span>
                                            </label>
                                            <small
                                                class="text-muted">{{ translate('If you enable this, this product will be granted as a todays deal product.') }}</small>
                                        </div>
                                    </div>
                                  
                                </div>

                                <!-- Flash Deal -->
                                <h5 class="mb-3 mt-4 pb-3 fs-17 fw-700" style="border-bottom: 1px dashed #e4e5eb;">
                                    {{ translate('Flash Deal') }}
                                    <small
                                        class="text-muted">({{ translate('If you want to select this product as a flash deal, you can use it') }})</small>
                                </h5>
                                <div class="w-100">
                                    <!-- Add To Flash -->
                                    <div class="form-group row">
                                        <label class="col-md-3 col-from-label">{{ translate('Add To Flash') }}</label>
                                        <div class="col-xxl-9">
                                            <select class="form-control aiz-selectpicker" name="flash_deal_id"
                                                id="flash_deal">
                                                <option value="">{{ translate('Choose Flash Title') }}</option>
                                                @foreach (\App\Models\FlashDeal::where('status', 1)->get() as $flash_deal)
                                                    <option value="{{ $flash_deal->id }}">
                                                        {{ $flash_deal->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <!-- Discount -->
                                    <div class="form-group row">
                                        <label class="col-md-3 col-from-label">{{ translate('Discount') }}</label>
                                        <div class="col-xxl-9">
                                            <input type="number" name="flash_discount" value="0" min="0"
                                                step="0.01" class="form-control">
                                        </div>
                                    </div>
                                    <!-- Discount Type -->
                                    <div class="form-group row">
                                        <label class="col-md-3 col-from-label">{{ translate('Discount Type') }}</label>
                                        <div class="col-xxl-9">
                                            <select class="form-control aiz-selectpicker" name="flash_discount_type"
                                                id="flash_discount_type">
                                                <option value="">{{ translate('Choose Discount Type') }}</option>
                                                <option value="amount">{{ translate('Flat') }}</option>
                                                <option value="percent">{{ translate('Percent') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Vat & TAX -->
                                <h5 class="mb-3 mt-4 pb-3 fs-17 fw-700" style="border-bottom: 1px dashed #e4e5eb;">
                                    {{ translate('Vat & TAX') }}</h5>
                                <div class="w-100">
                                    @foreach (\App\Models\Tax::where('tax_status', 1)->get() as $tax)
                                        <label for="name">
                                            {{ $tax->name }}
                                            <input type="hidden" value="{{ $tax->id }}" name="tax_id[]">
                                        </label>

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <input type="number" lang="en" min="0" value="0"
                                                    step="0.01" placeholder="{{ translate('Tax') }}" name="tax[]"
                                                    class="form-control">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <select class="form-control aiz-selectpicker" name="tax_type[]">
                                                    <option value="amount">{{ translate('Flat') }}</option>
                                                    <option value="percent">{{ translate('Percent') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Files & Media -->
                        <div class="tab-pane fade" id="files_and_media" role="tabpanel"
                            aria-labelledby="files-and-media-tab">
                            <div class="bg-white p-3 p-sm-2rem">
                                <!-- Product Files & Media -->
                                <h5 class="mb-3 pb-3 fs-17 fw-700" style="border-bottom: 1px dashed #e4e5eb;">
                                    {{ translate('Product Files & Media') }}</h5>
                                <div class="w-100">
                                    <!-- Gallery Images -->
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label"
                                            for="signinSrEmail">{{ translate('Gallery Images') }}</label>
                                        <div class="col-md-9">
                                            <div class="input-group" data-toggle="aizuploader" data-type="image"
                                                data-multiple="true">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                                        {{ translate('Browse') }}</div>
                                                </div>
                                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                                <input type="hidden" name="photos" class="selected-files">
                                            </div>
                                            <div class="file-preview box sm"></div>
                                            <small
                                                class="text-muted">{{ translate('These images are visible in product details page gallery. Minimum dimensions required: 900px width X 900px height.') }}</small>
                                        </div>
                                    </div>
                                    <!-- Thumbnail Image -->
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label"
                                            for="signinSrEmail">{{ translate('Thumbnail Image') }}</label>
                                        <div class="col-md-9">
                                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                                        {{ translate('Browse') }}</div>
                                                </div>
                                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                                <input type="hidden" name="thumbnail_img" class="selected-files">
                                            </div>
                                            <div class="file-preview box sm"></div>
                                            <small
                                                class="text-muted">{{ translate('This image is visible in all product box. Minimum dimensions required: 195px width X 195px height. Keep some blank space around main object of your image as we had to crop some edge in different devices to make it responsive.') }}</small>
                                        </div>
                                    </div>
                                </div>
                                <!-- Video Provider -->
                                {{-- <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Video Provider') }}</label>
                                    <div class="col-md-9">
                                        <select class="form-control aiz-selectpicker" name="video_provider"
                                            id="video_provider">
                                            <option value="youtube" @selected(old('video_provider') == 'youtube')>
                                                {{ translate('Youtube') }}</option>
                                            <option value="dailymotion" @selected(old('video_provider') == 'dailymotion')>
                                                {{ translate('Dailymotion') }}</option>
                                            <option value="vimeo" @selected(old('video_provider') == 'vimeo')>{{ translate('Vimeo') }}
                                            </option>
                                        </select>
                                    </div>
                                </div> --}}
                                <!-- Video Link -->
                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Video Link') }}</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="video_link"
                                            value="{{ old('video_link') }}" placeholder="{{ translate('Video Link') }}">
                                        <small
                                            class="text-muted">{{ translate("Use proper link without extra parameter. Don't use short share link/embeded iframe code.") }}</small>
                                    </div>
                                </div>
                                <!-- PDF Specification -->
                            
                            </div>
                        </div>

                        <!-- Price & Stock -->
                        <div class="tab-pane fade" id="price_and_stocks" role="tabpanel"
                            aria-labelledby="price-and-stocks-tab">
                            <div class="bg-white p-3 p-sm-2rem">
                                <!-- tab Title -->
                                <h5 class="mb-3 pb-3 fs-17 fw-700" style="border-bottom: 1px dashed #e4e5eb;">
                                    {{ translate('Product price & stock') }}</h5>
                                <div class="w-100">
                                    <!-- Colors -->
                                    <div class="form-group row gutters-5">
                                        <div class="col-md-3">
                                            <input type="text" class="form-control" value="{{ translate('Colors') }}"
                                                disabled>
                                        </div>
                                        <div class="col-md-8">
                                            <select class="form-control aiz-selectpicker" data-live-search="true"
                                                data-selected-text-format="count" name="colors[]" id="colors" multiple
                                                disabled>
                                                @foreach (\App\Models\Color::orderBy('name', 'asc')->get() as $key => $color)
                                                    <option value="{{ $color->code }}"
                                                        data-content="<span><span class='size-15px d-inline-block mr-2 rounded border' style='background:{{ $color->code }}'></span><span>{{ $color->name }}</span></span>">
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-1">
                                            <label class="aiz-switch aiz-switch-success mb-0">
                                                <input value="1" type="checkbox" name="colors_active">
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                    <!-- Attributes -->
                                    <div class="form-group row gutters-5">
                                        <div class="col-md-3">
                                            <input type="text" class="form-control"
                                                value="{{ translate('Attributes') }}" disabled>
                                        </div>
                                        <div class="col-md-8">
                                            <select name="choice_attributes[]" id="choice_attributes"
                                                class="form-control aiz-selectpicker" data-selected-text-format="count"
                                                data-live-search="true" multiple
                                                data-placeholder="{{ translate('Choose Attributes') }}">
                                                @foreach (\App\Models\Attribute::all() as $key => $attribute)
                                                    <option value="{{ $attribute->id }}">
                                                        {{ $attribute->getTranslation('name') }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <p>{{ translate('Choose the attributes of this product and then input values of each attribute') }}
                                        </p>
                                        <br>
                                    </div>

                                    <!-- choice options -->
                                    <div class="customer_choice_options mb-4" id="customer_choice_options"></div>

                                    <!-- Unit price -->
                                    <div class="form-group row">
                                        <label class="col-md-3 col-from-label">{{ translate('Unit price') }} <span
                                                class="text-danger">*</span></label>
                                        <div class="col-md-6">
                                            <input type="number" lang="en" min="0" value="0"
                                                step="0.01" placeholder="{{ translate('Unit price') }}"
                                                name="unit_price"
                                                class="form-control @error('unit_price') is-invalid @enderror" required>
                                        </div>
                                    </div>

                                    <!-- Discount Date Range -->
                                    {{-- <div class="form-group row">
                                        <label class="col-sm-3 control-label"
                                            for="start_date">{{ translate('Discount Date Range') }}</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control aiz-date-range" name="date_range"
                                                placeholder="{{ translate('Select Date') }}" data-time-picker="true"
                                                data-format="DD-MM-Y HH:mm:ss" data-separator=" to " autocomplete="off">
                                        </div>
                                    </div> --}}
                                    <!-- Discount -->
                                    <div class="form-group row">
                                        <label class="col-md-3 col-from-label">{{ translate('Discount') }}</label>
                                        <div class="col-md-6">
                                            <input type="number" lang="en" min="0" value="0"
                                                step="0.01" placeholder="{{ translate('Discount (Optional)') }}"
                                                name="discount"
                                                class="form-control @error('discount') is-invalid @enderror">
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-control aiz-selectpicker" name="discount_type">
                                                <option value="amount" @selected(old('discount_type') == 'amount')>
                                                    {{ translate('Flat') }}</option>
                                                <option value="percent" @selected(old('discount_type') == 'percent')>
                                                    {{ translate('Percent') }}</option>
                                            </select>
                                        </div>
                                    </div>

                              <!--  Pre-order Available -->
                                    <div class="form-group row">
                                        <label class="col-md-3 col-from-label">{{ translate('Pre-order Available') }}</label>
                                        <div class="col-md-9">
                                            <label class="aiz-switch aiz-switch-success mb-0 d-block">
                                                <input type="checkbox" id="is_preorder" name="is_preorder" value="1">
                                                <span></span>
                                            </label>
                                            <small class="text-muted">{{ translate('If you enable this, customers can place pre-orders when this product is out of stock.') }}</small>
                                        </div>
                                    </div>

                                    <!-- Pre-order Advance Payment Percentage -->
                                    <div class="form-group row d-none" id="preorder_percentage_block">
                                        <label class="col-md-3 col-from-label">{{ translate('Pre-order Advance Payment (%)') }}</label>
                                        <div class="col-md-6">
                                            <input type="number"
                                                   name="preorder_payment_percentage"
                                                   id="preorder_payment_percentage"
                                                   min="1" max="100" step="1"
                                                   value="{{ old('preorder_payment_percentage', 50) }}"
                                                   class="form-control @error('preorder_payment_percentage') is-invalid @enderror"
                                                   placeholder="{{ translate('e.g. 30 for 30%') }}">
                                            <small class="text-muted">{{ translate('If empty, defaults to 50%. Advance payment will be calculated using this percentage.') }}</small>
                                        </div>
                                    </div>

                                      <!-- START: DYNAMIC PRICE TIERS -->
                                    <div class="form-group row">
                                        <label
                                            class="col-md-3 col-from-label">{{ translate('Wholesaler Product Price Tiers') }}</label>
                                        <div class="col-md-9">
                                            <div class="mb-3">
                                                <small
                                                    class="text-muted">{{ translate('Add price tiers based on minimum quantity. The base Unit Price will be used if no tiers are added.') }}</small>
                                            </div>
                                            <div id="price-tier-container">
                                                <!-- Price tiers will be added here dynamically -->
                                            </div>
                                            <button type="button" class="btn btn-soft-secondary btn-sm"
                                                onclick="addPriceTier()">
                                                <i class="las la-plus"></i> {{ translate('Add New Tier') }}
                                            </button>
                                        </div>
                                    </div>
                                    <!-- END: DYNAMIC PRICE TIERS -->



                                    <div id="show-hide-div">
                                        <!-- Quantity -->
                                        <div class="form-group row">
                                            <label class="col-md-3 col-from-label">{{ translate('Quantity') }} <span
                                                    class="text-danger">*</span></label>
                                            <div class="col-md-6">
                                                <input type="number" lang="en" min="0" value="0"
                                                    step="1" placeholder="{{ translate('Quantity') }}"
                                                    name="current_stock" class="form-control" required>
                                            </div>
                                        </div>
                                        <!-- SKU -->
                                        <div class="form-group row">
                                            <label class="col-md-3 col-from-label"> {{ translate('SKU') }} </label>
                                            <div class="col-md-6">
                                                <input type="text" placeholder="{{ translate('SKU') }}"
                                                    name="sku" value="{{ old('sku') }}" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- External link -->
                                    <br>
                                    <!-- sku combination -->
                                    <div class="sku_combination" id="sku_combination"></div>
                                </div>

                                <!-- Low Stock Quantity -->
                                <h5 class="mb-3 pb-3 fs-17 fw-700" style="border-bottom: 1px dashed #e4e5eb;">
                                    {{ translate('Low Stock Quantity Warning') }}</h5>
                                <div class="w-100 mb-3">
                                    <div class="form-group row">
                                        <label class="col-md-3 col-from-label"> {{ translate('Quantity') }} </label>
                                        <div class="col-md-9">
                                            <input type="number" name="low_stock_quantity" value="1"
                                                min="0" step="1" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <!-- Stock Visibility State -->
                                <h5 class="mb-3 pb-3 fs-17 fw-700" style="border-bottom: 1px dashed #e4e5eb;">
                                    {{ translate('Stock Visibility State') }}</h5>
                                <div class="w-100">
                                    <!-- Show Stock Quantity -->
                                    <div class="form-group row">
                                        <label
                                            class="col-md-3 col-from-label">{{ translate('Show Stock Quantity') }}</label>
                                        <div class="col-md-9">
                                            <label class="aiz-switch aiz-switch-success mb-0">
                                                <input type="radio" name="stock_visibility_state" value="quantity"
                                                    checked>
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                    <!-- Show Stock With Text Only -->
                                    <div class="form-group row">
                                        <label
                                            class="col-md-3 col-from-label">{{ translate('Show Stock With Text Only') }}</label>
                                        <div class="col-md-9">
                                            <label class="aiz-switch aiz-switch-success mb-0">
                                                <input type="radio" name="stock_visibility_state" value="text">
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                    <!-- Hide Stock -->
                                    <div class="form-group row">
                                        <label class="col-md-3 col-from-label">{{ translate('Hide Stock') }}</label>
                                        <div class="col-md-9">
                                            <label class="aiz-switch aiz-switch-success mb-0">
                                                <input type="radio" name="stock_visibility_state" value="hide">
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SEO -->
                        <div class="tab-pane fade" id="seo" role="tabpanel" aria-labelledby="seo-tab">
                            <div class="bg-white p-3 p-sm-2rem">
                                <!-- tab Title -->
                                <h5 class="mb-3 pb-3 fs-17 fw-700" style="border-bottom: 1px dashed #e4e5eb;">
                                    {{ translate('SEO Meta Tags') }}</h5>
                                <div class="w-100">
                                    <!-- Meta Title -->
                                    <div class="form-group row">
                                        <label class="col-md-3 col-from-label">{{ translate('Meta Title') }}</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" name="meta_title"
                                                value="{{ old('meta_title') }}"
                                                placeholder="{{ translate('Meta Title') }}">
                                        </div>
                                    </div>
                                    <!-- Description -->
                                    <div class="form-group row">
                                        <label class="col-md-3 col-from-label">{{ translate('Description') }}</label>
                                        <div class="col-md-9">
                                            <textarea name="meta_description" rows="8" class="form-control">{{ old('meta_description') }}</textarea>
                                        </div>
                                    </div>
                                    <!--Meta Image -->
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label"
                                            for="signinSrEmail">{{ translate('Meta Image') }}</label>
                                        <div class="col-md-9">
                                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                                        {{ translate('Browse') }}</div>
                                                </div>
                                                <div class="form-control file-amount">{{ translate('Choose File') }}
                                                </div>
                                                <input type="hidden" name="meta_img" class="selected-files">
                                            </div>
                                            <div class="file-preview box sm"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping -->
                        <div class="tab-pane fade" id="shipping" role="tabpanel" aria-labelledby="shipping-tab">
                            <div class="bg-white p-3 p-sm-2rem">
                                <!-- Shipping Configuration -->
                                <h5 class="mb-3 pb-3 fs-17 fw-700" style="border-bottom: 1px dashed #e4e5eb;">
                                    {{ translate('Shipping Configuration') }}</h5>
                                <div class="w-100">
                                    <!-- Cash On Delivery -->
                                    @if (get_setting('cash_payment') == '1')
                                        <div class="form-group row">
                                            <label
                                                class="col-md-3 col-from-label">{{ translate('Cash On Delivery') }}</label>
                                            <div class="col-md-9">
                                                <label class="aiz-switch aiz-switch-success mb-0">
                                                    <input type="checkbox" name="cash_on_delivery" value="1"
                                                        checked="">
                                                    <span></span>
                                                </label>
                                            </div>
                                        </div>
                                    @else
                                        <p>
                                            {{ translate('Cash On Delivery option is disabled. Activate this feature from here') }}
                                            <a href="{{ route('activation.index') }}"
                                                class="aiz-side-nav-link {{ areActiveRoutes(['shipping_configuration.index', 'shipping_configuration.edit', 'shipping_configuration.update']) }}">
                                                <span
                                                    class="aiz-side-nav-text">{{ translate('Cash Payment Activation') }}</span>
                                            </a>
                                        </p>
                                    @endif

                                    @if (get_setting('shipping_type') == 'product_wise_shipping')
                                        <!-- Free Shipping -->
                                        <div class="form-group row">
                                            <label
                                                class="col-md-3 col-from-label">{{ translate('Free Shipping') }}</label>
                                            <div class="col-md-9">
                                                <label class="aiz-switch aiz-switch-success mb-0">
                                                    <input type="radio" name="shipping_type" value="free" checked>
                                                    <span></span>
                                                </label>
                                            </div>
                                        </div>
                                        <!-- Flat Rate -->
                                        <div class="form-group row">
                                            <label class="col-md-3 col-from-label">{{ translate('Flat Rate') }}</label>
                                            <div class="col-md-9">
                                                <label class="aiz-switch aiz-switch-success mb-0">
                                                    <input type="radio" name="shipping_type" value="flat_rate">
                                                    <span></span>
                                                </label>
                                            </div>
                                        </div>
                                        <!-- Shipping cost -->
                                        <div class="flat_rate_shipping_div" style="display: none">
                                            <div class="form-group row">
                                                <label
                                                    class="col-md-3 col-from-label">{{ translate('Shipping cost') }}</label>
                                                <div class="col-md-9">
                                                    <input type="number" lang="en" min="0" value="0"
                                                        step="0.01" placeholder="{{ translate('Shipping cost') }}"
                                                        name="flat_shipping_cost" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Is Product Quantity Mulitiply -->
                                        <div class="form-group row">
                                            <label
                                                class="col-md-3 col-from-label">{{ translate('Is Product Quantity Mulitiply') }}</label>
                                            <div class="col-md-9">
                                                <label class="aiz-switch aiz-switch-success mb-0">
                                                    <input type="checkbox" name="is_quantity_multiplied" value="1">
                                                    <span></span>
                                                </label>
                                            </div>
                                        </div>
                                    @else
                                        <p>
                                            {{ translate('Product wise shipping cost is disable. Shipping cost is configured from here') }}
                                            <a href="{{ route('shipping_configuration.shipping_method') }}"
                                                class="aiz-side-nav-link {{ areActiveRoutes(['shipping_configuration.shipping_method']) }}">
                                                <span class="aiz-side-nav-text">{{ translate('Shipping Method') }}</span>
                                            </a>
                                        </p>
                                    @endif
                                </div>

                                <!-- Estimate Shipping Time -->
                                <h5 class="mb-3 mt-4 pb-3 fs-17 fw-700" style="border-bottom: 1px dashed #e4e5eb;">
                                    {{ translate('Estimate Shipping Time') }}</h5>
                                <div class="w-100">
                                    <div class="form-group row">
                                        <label class="col-md-3 col-from-label">{{ translate('Shipping Days') }}</label>
                                        <div class="col-md-9">
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="est_shipping_days"
                                                    value="{{ old('est_shipping_days') }}" min="1"
                                                    step="1" placeholder="{{ translate('Shipping Days') }}">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"
                                                        id="inputGroupPrepend">{{ translate('Days') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Warranty -->
                        <div class="tab-pane fade" id="warranty" role="tabpanel" aria-labelledby="warranty-tab">
                            <div class="bg-white p-3 p-sm-2rem">
                                <h5 class="mb-3 pb-3 fs-17 fw-700" style="border-bottom: 1px dashed #e4e5eb;">
                                    {{ translate('Warranty') }}</h5>
                                <div class="form-group row">
                                    <label class="col-md-2 col-from-label">{{ translate('Warranty') }}</label>
                                    <div class="col-md-10">
                                        <label class="aiz-switch aiz-switch-success mb-0">
                                            <input type="checkbox" name="has_warranty" onchange="warrantySelection()">
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="w-100 warranty_selection_div d-none">
                                    <div class="form-group row">
                                        <div class="col-md-2"></div>
                                        <div class="col-md-10">
                                            <select class="form-control aiz-selectpicker" name="warranty_id"
                                                id="warranty_id" data-live-search="true">
                                                <option value="">{{ translate('Select Warranty') }}</option>
                                                @foreach (\App\Models\Warranty::all() as $warranty)
                                                    <option value="{{ $warranty->id }}" @selected(old('warranty_id') == $warranty->id)>
                                                        {{ $warranty->getTranslation('text') }}</option>
                                                @endforeach
                                            </select>

                                            <input type="hidden" name="warranty_note_id" id="warranty_note_id">

                                            <h5 class="fs-14 fw-600 mb-3 mt-4 pb-3"
                                                style="border-bottom: 1px dashed #e4e5eb;">
                                                {{ translate('Warranty Note') }}</h5>
                                            <div id="warranty_note" class=""></div>
                                            <button type="button"
                                                class="btn btn-block border border-dashed hov-bg-soft-secondary mt-2 fs-14 rounded-0 d-flex align-items-center justify-content-center"
                                                onclick="noteModal('warranty')">
                                                <i class="las la-plus"></i>
                                                <span class="ml-2">{{ translate('Select Warranty Note') }}</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Save Button -->
                        <div class="mt-4 text-right">
                            <button type="submit" name="button" value="unpublish"
                                class="mx-2 btn btn-light w-230px btn-md rounded-2 fs-14 fw-700 shadow-secondary border-soft-secondary action-btn">{{ translate('Save & Unpublish') }}</button>
                            <button type="submit" name="button" value="publish"
                                class="mx-2 btn btn-success w-230px btn-md rounded-2 fs-14 fw-700 shadow-success action-btn">{{ translate('Save & Publish') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('modal')
    <!-- Frequently Bought Product Select Modal -->
    @include('modals.product_select_modal')
    {{-- Note Modal --}}
    @include('modals.note_modal')
@endsection

@section('script')
    <!-- Treeview js -->
    <script src="{{ static_asset('assets/js/hummingbird-treeview.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $("#treeview").hummingbird();

            var main_id = '{{ old('category_id') }}';
            var selected_ids = [];
            @if (old('category_ids'))
                selected_ids = @json(old('category_ids'));
            @endif
            for (let i = 0; i < selected_ids.length; i++) {
                const element = selected_ids[i];
                $('#treeview input:checkbox#' + element).prop('checked', true);
                $('#treeview input:checkbox#' + element).parents("ul").css("display", "block");
                $('#treeview input:checkbox#' + element).parents("li").children('.las').removeClass("la-plus")
                    .addClass('la-minus');
            }

            if (main_id) {
                $('#treeview input:radio[value=' + main_id + ']').prop('checked', true);
            }

            $('#treeview input:checkbox').on("click", function() {
                let $this = $(this);
                if ($this.prop('checked') && ($('#treeview input:radio:checked').length == 0)) {
                    let val = $this.val();
                    $('#treeview input:radio[value=' + val + ']').prop('checked', true);
                }
            });
        });

        $('form').bind('submit', function(e) {
            if ($(".action-btn").attr('attempted') == 'true') {
                e.preventDefault();
            } else {
                $(".action-btn").attr("attempted", 'true');
            }
        });

        $("[name=shipping_type]").on("change", function() {
            $(".flat_rate_shipping_div").hide();
            if ($(this).val() == 'flat_rate') {
                $(".flat_rate_shipping_div").show();
            }
        });

        function add_more_customer_choice_option(i, name) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '{{ route('products.add-more-choice-option') }}',
                data: {
                    attribute_id: i
                },
                success: function(data) {
                    var obj = JSON.parse(data);
                    $('#customer_choice_options').append(`
                        <div class="form-group row">
                            <div class="col-md-3">
                                <input type="hidden" name="choice_no[]" value="${i}">
                                <input type="text" class="form-control" name="choice[]" value="${name}" placeholder="{{ translate('Choice Title') }}" readonly>
                            </div>
                            <div class="col-md-8">
                                <select class="form-control aiz-selectpicker attribute_choice" data-live-search="true" name="choice_options_${i}[]" data-selected-text-format="count" multiple>
                                    ${obj}
                                </select>
                            </div>
                        </div>`);
                    AIZ.plugins.bootstrapSelect('refresh');
                }
            });
        }

        $('input[name="colors_active"]').on('change', function() {
            if (!$('input[name="colors_active"]').is(':checked')) {
                $('#colors').prop('disabled', true);
            } else {
                $('#colors').prop('disabled', false);
            }
            AIZ.plugins.bootstrapSelect('refresh');
            update_sku();
        });

        $(document).on("change", ".attribute_choice", function() {
            update_sku();
        });
        $('#colors').on('change', function() {
            update_sku();
        });
        $('input[name="unit_price"]').on('keyup', function() {
            update_sku();
        });
        $('input[name="name"]').on('keyup', function() {
            update_sku();
        });

        function delete_row(em) {
            $(em).closest('.form-group.row').remove();
            update_sku();
        }

        function delete_variant(em) {
            $(em).closest('.variant').remove();
        }

        function update_sku() {
            $.ajax({
                type: "POST",
                url: '{{ route('products.sku_combination') }}',
                data: $('#choice_form').serialize(),
                success: function(data) {
                    $('#sku_combination').html(data);
                    AIZ.uploader.previewGenerate();
                    AIZ.plugins.sectionFooTable('#sku_combination');
                    if (data.trim().length > 1) {
                        $('#show-hide-div').hide();
                    } else {
                        $('#show-hide-div').show();
                    }
                }
            });
        }

        $('#choice_attributes').on('change', function() {
            $('#customer_choice_options').html(null);
            $.each($("#choice_attributes option:selected"), function() {
                add_more_customer_choice_option($(this).val(), $(this).text());
            });
            update_sku();
        });

        function fq_bought_product_selection_type() {
            var productSelectionType = $("input[name='frequently_bought_selection_type']:checked").val();
            if (productSelectionType == 'product') {
                $('.fq_bought_select_product_div').removeClass('d-none');
                $('.fq_bought_select_category_div').addClass('d-none');
            } else if (productSelectionType == 'category') {
                $('.fq_bought_select_category_div').removeClass('d-none');
                $('.fq_bought_select_product_div').addClass('d-none');
            }
        }

        function showFqBoughtProductModal() {
            $('#fq-bought-product-select-modal').modal('show', {
                backdrop: 'static'
            });
        }

        function filterFqBoughtProduct() {
            var searchKey = $('input[name=search_keyword]').val();
            var fqBroughCategory = $('select[name=fq_brough_category]').val();
            $.post('{{ route('product.search') }}', {
                _token: AIZ.data.csrf,
                product_id: null,
                search_key: searchKey,
                category: fqBroughCategory,
                product_type: "physical"
            }, function(data) {
                $('#product-list').html(data);
                AIZ.plugins.sectionFooTable('#product-list');
            });
        }

        function addFqBoughtProduct() {
            var selectedProducts = [];
            $("input:checkbox[name=fq_bought_product_id]:checked").each(function() {
                selectedProducts.push($(this).val());
            });
            var fqBoughtProductIds = [];
            $("input[name='fq_bought_product_ids[]']").each(function() {
                fqBoughtProductIds.push($(this).val());
            });
            var productIds = selectedProducts.concat(fqBoughtProductIds.filter((item) => selectedProducts.indexOf(item) <
                0))
            $.post('{{ route('get-selected-products') }}', {
                _token: AIZ.data.csrf,
                product_ids: productIds
            }, function(data) {
                $('#fq-bought-product-select-modal').modal('hide');
                $('#selected-fq-bought-products').html(data);
                AIZ.plugins.sectionFooTable('#selected-fq-bought-products');
            });
        }

        function warrantySelection() {
            if ($('input[name="has_warranty"]').is(':checked')) {
                $('.warranty_selection_div').removeClass('d-none');
                $('#warranty_id').attr('required', true);
            } else {
                $('.warranty_selection_div').addClass('d-none');
                $('#warranty_id').removeAttr('required');
            }
        }

        function isRefundable() {
            const refundType = "{{ get_setting('refund_type') }}";
            const $refundable = $('input[name="refundable"]');
            const $mainCategoryRadio = $('input[name="category_id"]:checked');
            const $note = $('#refundable-note');
            $refundable.off('change.isRefundableLock');
            if (refundType !== 'category_based_refund') {
                $refundable.prop('disabled', false);
                $note.addClass('d-none');
                $('.refund-block').toggleClass('d-none', !$refundable.is(':checked'));
                return;
            }
            if (!$mainCategoryRadio.length) {
                $refundable.prop('checked', false);
                $refundable.prop('disabled', true);
                $('.refund-block').addClass('d-none');
                $note.text('{{ translate('Your refund type is category based. At first select the main category.') }}')
                    .removeClass('d-none');
                return;
            }
            const categoryId = $mainCategoryRadio.val();
            $.ajax({
                type: 'POST',
                url: '{{ route('admin.products.check_refundable_category') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    category_id: categoryId
                },
                success: function(response) {
                    if (response.status === 'success' && response.is_refundable) {
                        $refundable.prop('disabled', false);
                        $note.text('{{ translate('This product allows refunds.') }}').removeClass('d-none');
                        $refundable.on('change.isRefundableLock', function() {
                            $('.refund-block').toggleClass('d-none', !$(this).is(':checked'));
                        });
                    } else {
                        $refundable.prop('checked', false);
                        $refundable.prop('disabled', true);
                        $('.refund-block').addClass('d-none');
                        $note.text(
                            '{{ translate('Selected main category has no refund. Select a refundable category.') }}'
                            ).removeClass('d-none');
                    }
                },
                error: function() {
                    $refundable.prop('checked', false);
                    $refundable.prop('disabled', true);
                    $('.refund-block').addClass('d-none');
                    $note.text('{{ translate('Could not verify category refund status.') }}').removeClass(
                        'd-none');
                }
            });
        }

        function noteModal(noteType) {
            $.post('{{ route('get_notes') }}', {
                _token: '{{ @csrf_token() }}',
                note_type: noteType
            }, function(data) {
                $('#note_modal #note_modal_content').html(data);
                $('#note_modal').modal('show', {
                    backdrop: 'static'
                });
            });
        }

        function addNote(noteId, noteType) {
            var noteDescription = $('#note_description_' + noteId).val();
            $('#' + noteType + '_note_id').val(noteId);
            $('#' + noteType + '_note').html(noteDescription);
            $('#' + noteType + '_note').addClass('border border-gray my-2 p-2');
            $('#note_modal').modal('hide');
        }
    </script>
    <script>
        $(document).ready(function() {
            var hash = document.location.hash;
            if (hash) {
                $('.nav-tabs a[href="' + hash + '"]').tab('show');
            } else {
                $('.nav-tabs a[href="#general"]').tab('show');
            }
            $('.nav-tabs a').on('shown.bs.tab', function(e) {
                window.location.hash = e.target.hash;
            });
        });
    </script>

    @include('partials.product.product_temp_data')

    <script type="text/javascript">
        $(document).ready(function() {
            warrantySelection();
            isRefundable();
            $(document).on('change', 'input[name="category_id"]', function() {
                isRefundable();
            });
            $('input[name="refundable"]').on('change', function() {
                if (!$('input[name="refundable"]').prop('disabled')) {
                    $('.refund-block').toggleClass('d-none', !$(this).is(':checked'));
                }
            });
        });
    </script>

    <script type="text/javascript">
        function addPriceTier() {
            var newTier = `
            <div class="row gutters-5 price-tier-row mb-3">
                <div class="col-md-4">
                    <div class="form-group mb-0">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{{ translate('Min Qty') }}</span>
                            </div>
                            <input type="number" name="price_tiers[min_qty][]" class="form-control" placeholder="1" min="1" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group mb-0">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{{ translate('Price') }}</span>
                            </div>
                            <input type="number" lang="en" name="price_tiers[price][]" class="form-control" placeholder="0.00" min="0" step="0.01" required>
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    <button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" onclick="removePriceTier(this)">
                        <i class="las la-trash"></i>
                    </button>
                </div>
            </div>`;
            $('#price-tier-container').append(newTier);
        }

        function removePriceTier(button) {
            $(button).closest('.price-tier-row').remove();
        }

        // Auto-populate variant discount prices when main discount is set
        function updateVariantDiscountPrices() {
            var discount = parseFloat($('input[name="discount"]').val()) || 0;
            var discountType = $('select[name="discount_type"]').val();
            
            $('.variant-price').each(function() {
                var variantPrice = parseFloat($(this).val()) || 0;
                var discountPrice = 0;
                
                if (discount > 0 && variantPrice > 0) {
                    if (discountType === 'amount') {
                        discountPrice = Math.max(0, variantPrice - discount);
                    } else if (discountType === 'percent') {
                        discountPrice = variantPrice - (variantPrice * discount / 100);
                    }
                }
                
                // Find corresponding discount price input
                var variantName = $(this).attr('name').replace('price_', '');
                $('input[name="discount_price_' + variantName + '"]').val(discountPrice.toFixed(2));
            });
        }

        $(document).ready(function() {
            // Update variant discount prices when main discount changes
            $('input[name="discount"], select[name="discount_type"]').on('change input', function() {
                updateVariantDiscountPrices();
            });
            
            // Update specific variant discount price when variant price changes
            $(document).on('change input', '.variant-price', function() {
                var discount = parseFloat($('input[name="discount"]').val()) || 0;
                var discountType = $('select[name="discount_type"]').val();
                var variantPrice = parseFloat($(this).val()) || 0;
                var discountPrice = 0;
                
                if (discount > 0 && variantPrice > 0) {
                    if (discountType === 'amount') {
                        discountPrice = Math.max(0, variantPrice - discount);
                    } else if (discountType === 'percent') {
                        discountPrice = variantPrice - (variantPrice * discount / 100);
                    }
                }
                
                // Find corresponding discount price input
                var variantName = $(this).attr('name').replace('price_', '');
                $('input[name="discount_price_' + variantName + '"]').val(discountPrice.toFixed(2));
            });
        });
    </script>
    <script>
        // Show/hide preorder percentage when toggle enabled
        (function() {
            function togglePreorderPercentage() {
                var isChecked = document.getElementById('is_preorder') && document.getElementById('is_preorder').checked;
                var block = document.getElementById('preorder_percentage_block');
                if (!block) return;
                if (isChecked) {
                    block.classList.remove('d-none');
                } else {
                    block.classList.add('d-none');
                }
            }
            document.addEventListener('DOMContentLoaded', function() {
                var cb = document.getElementById('is_preorder');
                if (cb) {
                    cb.addEventListener('change', togglePreorderPercentage);
                    togglePreorderPercentage();
                }
            });
        })();
    </script>
@endsection
