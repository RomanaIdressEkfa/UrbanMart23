@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('All Pre-Order Products') }}</h1>
        </div>
        <div class="col-md-6 text-md-right">
            <a href="{{ route('products.create') }}" class="btn btn-circle btn-info">
                <span>{{ translate('Add New Product') }}</span>
            </a>
        </div>
    </div>
</div>

<div class="card">
    <form class="" id="sort_products" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-0 h6">{{ translate('Pre-Order Products') }}</h5>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-0">
                    <input type="text" class="form-control form-control-sm" id="search" name="search" @isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type name & Enter') }}">
                </div>
            </div>
        </div>
    </form>
    
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th width="30%">{{ translate('Name') }}</th>
                    <th data-breakpoints="md">{{ translate('Info') }}</th>
                    <th data-breakpoints="md">{{ translate('Published') }}</th>
                    <th class="text-right">{{ translate('Options') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $key => $product)
                    <tr>
                        <td>{{ ($key+1) + ($products->currentPage() - 1) * $products->perPage() }}</td>
                        <td>
                            <a href="{{ route('product', $product->slug) }}" target="_blank" class="text-reset">
                                <div class="d-flex align-items-center">
                                    <img src="{{ uploaded_asset($product->thumbnail_img)}}" alt="Image" class="size-60px img-fit mr-3">
                                    <span class="text-muted text-truncate-2">{{ $product->getTranslation('name') }}</span>
                                </div>
                            </a>
                        </td>
                        <td>
                            <strong>{{ translate('Base Price') }}:</strong> {{ single_price($product->unit_price) }}
                        </td>
                        <td>
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input onchange="update_published(this)" value="{{ $product->id }}" type="checkbox" @if($product->published == 1) checked @endif >
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('products.admin.edit', ['id'=>$product->id, 'lang'=>env('DEFAULT_LANGUAGE')] ) }}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{ route('products.destroy', $product->id) }}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
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

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function(){
            //$('#container').removeClass('mainnav-lg').addClass('mainnav-sm');
        });

        function update_preorder_status(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('admin.preorder.products.update_status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Pre-order status updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_published(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('products.published') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Published products updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_featured(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('products.featured') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Featured products updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        $('#sort_products').on('submit', function(e){
            e.preventDefault();
            $('#sort_products').submit();
        });

        // Handle pre-order price updates
        $('.update-price-btn').on('click', function(){
            var productId = $(this).data('product-id');
            var priceInput = $('.preorder-price-input[data-product-id="' + productId + '"]');
            var newPrice = priceInput.val();
            var button = $(this);
            
            if(!newPrice || newPrice < 0) {
                AIZ.plugins.notify('danger', '{{ translate('Please enter a valid price') }}');
                return;
            }
            
            button.prop('disabled', true);
            button.html('<i class="las la-spinner la-spin"></i>');
            
            $.post('{{ route('admin.preorder.products.update_preorder_price') }}', {
                _token: '{{ csrf_token() }}',
                id: productId,
                price: newPrice
            }, function(data){
                if(data.success){
                    AIZ.plugins.notify('success', data.message);
                    // Update the current price display
                    var currentPriceElement = priceInput.closest('td').find('.text-muted');
                    currentPriceElement.text('Current: ' + data.formatted_price);
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            }).fail(function(){
                AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
            }).always(function(){
                button.prop('disabled', false);
                button.html('<i class="las la-save"></i>');
            });
        });

        // Allow Enter key to trigger price update
        $('.preorder-price-input').on('keypress', function(e){
            if(e.which === 13) {
                var productId = $(this).data('product-id');
                $('.update-price-btn[data-product-id="' + productId + '"]').click();
            }
        });
    </script>
@endsection

