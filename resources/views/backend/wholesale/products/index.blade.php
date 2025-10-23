@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{ translate('All Wholesale Products') }}</h1>
            </div>
            <div class="col-md-6 text-md-right">
                {{-- @can('add_wholesale_product') --}}
                {{-- যদি নতুন পাইকারি পণ্য যোগ করার অপশন থাকে --}}
                {{-- <a href="{{ route('wholesale_product_create.admin') }}" class="btn btn-primary">
                    <span>{{ translate('Add New Wholesale Product') }}</span>
                </a> --}}
                {{-- @endcan --}}
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-block d-md-flex">
            <h5 class="mb-0 h6">{{ translate('Wholesale Products') }}</h5>
            <form class="" id="sort_products" action="" method="GET" style="display: flex; gap: 20px;">
                <div class="box-inline pad-rgt pull-left">
                    <div class="form-group select_box mb-0">
                        <select class="form-control aiz-selectpicker" name="sort_by" id="sort_by" onchange="sort_products()">
                            <option value="">{{ translate('Sort by') }}</option>
                            <option value="name" @isset($sort_by) @if ($sort_by == 'name') selected @endif @endisset>{{ translate('Name') }}</option>
                            <option value="unit_price" @isset($sort_by) @if ($sort_by == 'unit_price') selected @endif @endisset>{{ translate('Unit Price') }}</option>
                            <option value="rating" @isset($sort_by) @if ($sort_by == 'rating') selected @endif @endisset>{{ translate('Rating') }}</option>
                        </select>
                    </div>
                </div>
                <div class="box-inline pad-rgt pull-left">
                    <div class="form-group mb-0">
                        <input type="text" class="form-control" id="search" name="search"@isset($search) value="{{ $search }}" @endisset placeholder="{{ translate('Type & Enter') }}">
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th>{{ translate('Name') }}</th>
                        <th data-breakpoints="sm">{{ translate('Added By') }}</th>
                        <th data-breakpoints="sm">{{ translate('Wholesale Price') }}</th>
                        <th data-breakpoints="md">{{ translate('Min. Qty') }}</th>
                        <th data-breakpoints="md">{{ translate('Published') }}</th>
                        <th data-breakpoints="md" class="text-right">{{ translate('Options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $key => $product)
                        <tr>
                            <td>{{ $key + 1 + ($products->currentPage() - 1) * $products->perPage() }}</td>
                            <td>
                                <a href="{{ route('product', $product->slug) }}" target="_blank" class="text-reset">
                                    {{ $product->getTranslation('name') }}
                                </a>
                            </td>
                            <td>
                                @if ($product->user)
                                    {{ $product->user->name }}
                                @else
                                    {{ translate('N/A') }}
                                @endif
                            </td>
                            <td>
                                {{-- পাইকারি মূল্যের লজিক এখানে থাকতে পারে।
                                     যদি ProductVariant মডেলে wholesale_price থাকে, তাহলে সেটি লোড করতে হবে।
                                     এখানে সহজভাবে product->unit_price ব্যবহার করা হচ্ছে, যা সম্ভবত খুচরা মূল্য। --}}
                                {{ format_price($product->unit_price) }}
                            </td>
                            <td>
                                {{-- এখানে পাইকারি বিক্রির জন্য ন্যূনতম পরিমাণ দেখানোর লজিক যোগ করুন --}}
                                {{-- যেমন: $product->min_wholesale_qty ?? 1 --}}
                                1
                            </td>
                            <td>
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input onchange="update_published(this)" value="{{ $product->id }}" type="checkbox" @if ($product->published == 1) checked @endif>
                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td class="text-right">
                                {{-- @can('edit_wholesale_product') --}}
                                {{-- <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                    href="{{ route('wholesale_product_edit.admin', ['id' => $product->id, 'lang' => env('DEFAULT_LANGUAGE')]) }}"
                                    title="{{ translate('Edit') }}">
                                    <i class="las la-edit"></i>
                                </a> --}}
                                {{-- @endcan --}}
                                {{-- @can('delete_wholesale_product') --}}
                                {{-- <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                    data-href="{{ route('wholesale_product_delete.admin', $product->id) }}"
                                    title="{{ translate('Delete') }}">
                                    <i class="las la-trash"></i>
                                </a> --}}
                                {{-- @endcan --}}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center" colspan="7">{{ translate('No wholesale products found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="aiz-pagination mt-4">
                {{ $products->links() }}
            </div>
        </div>
    </div>
@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
    <script type="text/javascript">
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
        function sort_products(el){
            $('#sort_products').submit();
        }
    </script>
@endsection

