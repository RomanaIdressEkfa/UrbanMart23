<div class="table-responsive">
    <table class="table table-borderless">
        <thead class="bg-light">
            <tr>
                <th class="border-0 fs-14 fw-600">{{ translate('Product') }}</th>
                <th class="border-0 fs-14 fw-600 text-center">{{ translate('Unit Price') }}</th>
                <th class="border-0 fs-14 fw-600 text-center">{{ translate('Qty') }}</th>
                <th class="border-0 fs-14 fw-600 text-right">{{ translate('Total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($preorders as $preorder)
                @php
                    // Preorder model-এর relationship ব্যবহার করে প্রোডাক্টের তথ্য আনা হচ্ছে
                    $product = $preorder->product;
                @endphp
                @if($product)
                    <tr>
                        <!-- Product Image & Name -->
                        <td class="align-middle">
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <a href="{{ route('product', $product->slug) }}" class="d-block position-relative">
                                        <img src="{{ uploaded_asset($product->thumbnail_img) }}"
                                             class="img-fit size-80px rounded border"
                                             alt="{{ $product->getTranslation('name') }}"
                                             onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                             style="object-fit: cover;">
                                    </a>
                                </div>
                                <div class="flex-grow-1">
                                    <a href="{{ route('product', $product->slug) }}" class="text-decoration-none">
                                        <h6 class="fs-15 fw-600 text-dark mb-2 hover-text-primary line-height-1-4" title="{{ $product->getTranslation('name') }}">
                                            {{ $product->getTranslation('name') }}
                                        </h6>
                                    </a>
                                    @if($preorder->variant_name)
                                        <div class="fs-12 text-muted">
                                            <strong>{{ translate('Variant') }}:</strong> {{ $preorder->variant_name }}
                                        </div>
                                    @endif
                                    <div class="fs-12 text-success">
                                        <strong>{{ translate('Quantity') }}:</strong> {{ $preorder->quantity }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Unit Price -->
                        <td class="text-center align-middle">
                            <span class="fw-600 fs-14">{{ single_price($preorder->unit_price) }}</span>
                        </td>

                        <!-- Quantity -->
                        <td class="text-center align-middle">
                            <span class="fw-600 fs-14">{{ $preorder->quantity }}</span>
                        </td>

                        <!-- Total Price -->
                        <td class="text-right align-middle">
                            <span class="fw-700 fs-16 text-primary">{{ single_price($preorder->subtotal) }}</span>
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>