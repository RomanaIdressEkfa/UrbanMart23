@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Pre-order Products') }}</h1>
        </div>
        <div class="col-md-6 text-md-right">
            <a href="{{ route('preorder_management.products') }}" class="btn btn-circle btn-info">
                <span>{{ translate('Manage Products') }}</span>
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-block d-md-flex">
        <h5 class="mb-0 h6">{{ translate('Pre-order Enabled Products') }}</h5>
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
                    <th data-breakpoints="lg">{{ translate('Seller') }}</th>
                    <th data-breakpoints="lg">{{ translate('Original Price') }}</th>
                    <th data-breakpoints="lg">{{ translate('Pre-order Price') }}</th>
                    <th data-breakpoints="lg">{{ translate('Payment %') }}</th>
                    <th data-breakpoints="lg">{{ translate('Stock') }}</th>
                    <th data-breakpoints="lg">{{ translate('Orders') }}</th>
                    <th class="text-right">{{ translate('Actions') }}</th>
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
                                <br>
                                <small class="text-muted">SKU: {{ $product->sku ?? 'N/A' }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($product->main_category)
                            {{ $product->main_category->getTranslation('name') }}
                        @endif
                    </td>
                    <td>
                        @if($product->user)
                            {{ $product->user->name }}
                            @if($product->user->user_type == 'seller')
                                <br><small class="text-muted">{{ translate('Seller') }}</small>
                            @else
                                <br><small class="text-muted">{{ translate('Admin') }}</small>
                            @endif
                        @endif
                    </td>
                    <td>
                        <strong>{{ single_price($product->unit_price) }}</strong>
                    </td>
                    <td>
                        @if($product->preorder_price)
                            <span class="badge badge-success">{{ single_price($product->preorder_price) }}</span>
                        @else
                            <span class="badge badge-info">{{ single_price($product->getPreorderPriceByPercentage()) }}</span>
                            <br><small class="text-muted">{{ translate('Auto calculated') }}</small>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-primary">{{ $product->getPreorderPaymentPercentage() }}%</span>
                    </td>
                    <td>
                        @php
                            $total_stock = 0;
                            foreach($product->stocks as $stock) {
                                $total_stock += $stock->qty;
                            }
                        @endphp
                        @if($total_stock > 0)
                            <span class="badge badge-success">{{ $total_stock }}</span>
                        @else
                            <span class="badge badge-danger">{{ translate('Out of Stock') }}</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $preorder_count = $product->preorders->count();
                        @endphp
                        @if($preorder_count > 0)
                            <span class="badge badge-info">{{ $preorder_count }}</span>
                        @else
                            <span class="text-muted">0</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" 
                           href="{{ route('product', $product->slug) }}" target="_blank" 
                           title="{{ translate('View Product') }}">
                            <i class="las la-eye"></i>
                        </a>
                        
                        <button type="button" class="btn btn-soft-warning btn-icon btn-circle btn-sm edit-preorder-btn" 
                            data-product-id="{{ $product->id }}"
                            data-preorder-price="{{ $product->preorder_price }}"
                            data-preorder-percentage="{{ $product->preorder_payment_percentage }}"
                            title="{{ translate('Edit Pre-order Settings') }}">
                            <i class="las la-edit"></i>
                        </button>
                        
                        <button type="button" class="btn btn-soft-danger btn-icon btn-circle btn-sm disable-preorder-btn" 
                            data-product-id="{{ $product->id }}"
                            title="{{ translate('Disable Pre-order') }}">
                            <i class="las la-times"></i>
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

<!-- Edit Pre-order Modal -->
<div class="modal fade" id="edit_preorder_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ translate('Edit Pre-order Settings') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="edit_preorder_form">
                @csrf
                <input type="hidden" id="edit_product_id" name="product_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ translate('Pre-order Price') }}</label>
                        <input type="number" class="form-control" id="edit_preorder_price" name="preorder_price" 
                               placeholder="{{ translate('Leave empty for auto calculation') }}">
                        <small class="text-muted">{{ translate('If empty, will be calculated based on percentage') }}</small>
                    </div>
                    <div class="form-group">
                        <label>{{ translate('Payment Percentage') }} <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="edit_preorder_percentage" name="preorder_payment_percentage" 
                               min="1" max="100" required>
                        <small class="text-muted">{{ translate('Percentage of original price to be paid as pre-order') }}</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function() {
        // Edit pre-order settings
        $('.edit-preorder-btn').click(function() {
            var product_id = $(this).data('product-id');
            var preorder_price = $(this).data('preorder-price');
            var preorder_percentage = $(this).data('preorder-percentage');
            
            $('#edit_product_id').val(product_id);
            $('#edit_preorder_price').val(preorder_price);
            $('#edit_preorder_percentage').val(preorder_percentage || 50);
            
            $('#edit_preorder_modal').modal('show');
        });
        
        // Submit edit form
        $('#edit_preorder_form').submit(function(e) {
            e.preventDefault();
            
            $.post('{{ route('preorder_management.products.update') }}', {
                _token: '{{ csrf_token() }}',
                product_id: $('#edit_product_id').val(),
                is_preorder: 1,
                preorder_price: $('#edit_preorder_price').val(),
                preorder_payment_percentage: $('#edit_preorder_percentage').val()
            }, function(data) {
                if (data.success) {
                    AIZ.plugins.notify('success', data.message);
                    $('#edit_preorder_modal').modal('hide');
                    location.reload();
                } else {
                    AIZ.plugins.notify('danger', 'Something went wrong');
                }
            });
        });
        
        // Disable pre-order
        $('.disable-preorder-btn').click(function() {
            var product_id = $(this).data('product-id');
            
            if (confirm('{{ translate('Are you sure you want to disable pre-order for this product?') }}')) {
                $.post('{{ route('preorder_management.products.update') }}', {
                    _token: '{{ csrf_token() }}',
                    product_id: product_id,
                    is_preorder: 0
                }, function(data) {
                    if (data.success) {
                        AIZ.plugins.notify('success', data.message);
                        location.reload();
                    } else {
                        AIZ.plugins.notify('danger', 'Something went wrong');
                    }
                });
            }
        });
        
        // Search functionality
        $('#search').on('keyup', function() {
            var search = $(this).val();
            if (search.length > 2 || search.length == 0) {
                $('#sort_products').submit();
            }
        });
    });
</script>
@endsection