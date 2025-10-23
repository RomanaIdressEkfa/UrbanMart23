@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Pre-order Product Management') }}</h1>
        </div>
        <div class="col-md-6 text-md-right">
            <a href="{{ route('preorder_management.products.list') }}" class="btn btn-circle btn-info">
                <span>{{ translate('Pre-order Products') }} ({{ $preorder_products_count }})</span>
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-block d-md-flex">
        <h5 class="mb-0 h6">{{ translate('All Products') }}</h5>
        <div class="col-md-3 ml-auto">
            <form class="" id="sort_products" action="" method="GET">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" id="search" name="search"
                        @isset($sort_search) value="{{ $sort_search }}" @endisset
                        placeholder="{{ translate('Type name & Enter') }}">
                </div>
            </form>
        </div>
    </div>
    
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>{{ translate('Product') }}</th>
                    <th data-breakpoints="lg">{{ translate('Category') }}</th>
                    <th data-breakpoints="lg">{{ translate('Current Price') }}</th>
                    <th data-breakpoints="lg">{{ translate('Pre-order Status') }}</th>
                    <th data-breakpoints="lg">{{ translate('Pre-order Price') }}</th>
                    <th data-breakpoints="lg">{{ translate('Payment %') }}</th>
                    <th class="text-right">{{ translate('Options') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $key => $product)
                <tr>
                    <td>
                        <div class="row gutters-5 w-200px w-md-300px">
                            <div class="col-auto">
                                <img src="{{ uploaded_asset($product->thumbnail_img) }}" alt="Image" class="size-50px img-fit">
                            </div>
                            <div class="col">
                                <span class="text-muted text-truncate-2">{{ $product->getTranslation('name') }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($product->main_category)
                            {{ $product->main_category->getTranslation('name') }}
                        @endif
                    </td>
                    <td>{{ single_price($product->unit_price) }}</td>
                    <td>
                        <label class="aiz-switch aiz-switch-success mb-0">
                            <input type="checkbox" onchange="update_preorder_status(this)" value="{{ $product->id }}" 
                                {{ $product->is_preorder == 1 ? 'checked' : '' }}>
                            <span class="slider round"></span>
                        </label>
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm preorder-price" 
                            data-product-id="{{ $product->id }}" 
                            value="{{ $product->preorder_price ?? '' }}" 
                            placeholder="{{ translate('Set price') }}"
                            {{ $product->is_preorder != 1 ? 'disabled' : '' }}>
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm preorder-percentage" 
                            data-product-id="{{ $product->id }}" 
                            value="{{ $product->preorder_payment_percentage ?? 50 }}" 
                            min="1" max="100"
                            {{ $product->is_preorder != 1 ? 'disabled' : '' }}>
                    </td>
                    <td class="text-right">
                        <button type="button" class="btn btn-soft-primary btn-icon btn-circle btn-sm update-preorder-btn" 
                            data-product-id="{{ $product->id }}"
                            {{ $product->is_preorder != 1 ? 'disabled' : '' }}>
                            <i class="las la-save"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $products->appends(request()->input())->links() }}
        </div>
    </div>
</div>

@endsection

@section('script')
<script type="text/javascript">
    function update_preorder_status(el) {
        var product_id = $(el).val();
        var status = $(el).is(':checked') ? 1 : 0;
        var row = $(el).closest('tr');
        
        $.post('{{ route('preorder_management.products.update') }}', {
            _token: '{{ csrf_token() }}',
            product_id: product_id,
            is_preorder: status
        }, function(data) {
            if (data.success) {
                AIZ.plugins.notify('success', data.message);
                
                // Enable/disable inputs based on status
                var priceInput = row.find('.preorder-price');
                var percentageInput = row.find('.preorder-percentage');
                var updateBtn = row.find('.update-preorder-btn');
                
                if (status == 1) {
                    priceInput.prop('disabled', false);
                    percentageInput.prop('disabled', false);
                    updateBtn.prop('disabled', false);
                } else {
                    priceInput.prop('disabled', true);
                    percentageInput.prop('disabled', true);
                    updateBtn.prop('disabled', true);
                }
            } else {
                AIZ.plugins.notify('danger', 'Something went wrong');
            }
        });
    }
    
    $(document).ready(function() {
        $('.update-preorder-btn').click(function() {
            var product_id = $(this).data('product-id');
            var row = $(this).closest('tr');
            var preorder_price = row.find('.preorder-price').val();
            var preorder_percentage = row.find('.preorder-percentage').val();
            
            $.post('{{ route('preorder_management.products.update') }}', {
                _token: '{{ csrf_token() }}',
                product_id: product_id,
                is_preorder: 1,
                preorder_price: preorder_price,
                preorder_payment_percentage: preorder_percentage
            }, function(data) {
                if (data.success) {
                    AIZ.plugins.notify('success', data.message);
                } else {
                    AIZ.plugins.notify('danger', 'Something went wrong');
                }
            });
        });
        
        $('#search').on('keyup', function() {
            var search = $(this).val();
            if (search.length > 2 || search.length == 0) {
                $('#sort_products').submit();
            }
        });
    });
</script>
@endsection